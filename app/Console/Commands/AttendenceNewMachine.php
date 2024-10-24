<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\AttnLog;
use App\Models\AttnLtSett;
use App\Models\AttnOfficeInOutLog;
use App\Models\AttnOfficeSchedule;
use App\Services\ZktService;
use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use Rats\Zkteco\Lib\ZKTeco;

class AttendenceNewMachine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected  $signature = 'fetch:attendance {ip} {port=4370}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {


        $ip = $this->argument('ip'); // Get the IP address from the command
        $port = $this->argument('port');
        $createdBy = '';
        switch ($ip) {
            case '192.168.0.70':
                $createdBy = 'bot70';
                break;
            case '192.168.0.134':
                $createdBy = 'bot134';
                break;
            default:
                $createdBy = 'bot';
                break;
        }
        $device = new ZKTeco($ip, $port);

        if ($device->connect()) {
            // Fetch attendance data
            $attendanceData = $device->getAttendance();


            // $lastAttnLogDate = AttnLog::where('entry_type', 'Machine')
            // ->where('ip_address', $ip)
            // ->orderBy('attendance_date', 'desc')
            // ->value('attendance_date');

            $lastAttnLogDate = AttnLog::where('entry_type', 'Machine')
                ->select('employee_id', 'attendance_date')
                ->orderBy('attendance_date', 'desc')
                ->get()
                ->groupBy('employee_id')
                ->map(function ($group) {
                    return $group->first()->attendance_date; // Get the most recent date for each employee
                });



            if (!empty($attendanceData)) {
                $groupedData = [];
                foreach ($attendanceData as $record) {
                    $userId = $record['id'];
                    $timestamp = $record['timestamp'];
                    $date = date('Y-m-d', strtotime($timestamp));

                    if (isset($lastAttnLogDates[$userId])) {

                        if ($date >= $lastAttnLogDate[$userId]) {
                            $groupedData[$userId][$date][] = $timestamp; // Group by user and date
                        }
                    } else {
                        // If the user has no previous attendance logs, include their current records
                        $groupedData[$userId][$date][] = $timestamp;
                    }

                    // if (is_null($lastAttnLogDate) || $date > $lastAttnLogDate) {

                    //     $groupedData[$userId][$date][] = $timestamp;
                    // }


                    // $groupedData[$userId][$date][] = $timestamp;
                }


                foreach ($groupedData as $userId => $dates) {


                    foreach ($dates as $date => $timestamps) {

                        foreach ($timestamps as $timestamp) {
                            $punchTime = date('H:i:s', strtotime($timestamp));
                            $inserted = $this->insertAttnLog($userId, $punchTime, $date,$ip,$createdBy);
                            // if ($inserted) {
                            //     // $device->clearAttendance();
                            // } else {
                            //     echo ('');
                            // }
                        }
                    }
                }

                $device->disconnect();
                $this->info('Attendance log fetched and stored successfully.');
            }
            else{
                $this->info('Attendance log Not found.');
            }
        } else {
            $this->error('Failed to connect to ZKT device.');
        }
    }

    private function insertAttnLog($userId, $punchTime, $date,$ip,$createdBy)
    {



        try {
            $inserted = null;
            $logExists = AttnLog::where('employee_id', $userId)
                ->where('attendance_date', $date)
                ->where('punch_time', $punchTime)
                ->exists();

            if (!$logExists) {

                $inserted = AttnLog::create([
                    'employee_id' => $userId,
                    'punch_time' => $punchTime,
                    'entry_time' => now(),
                    'entry_type' => "Machine",
                    'created_by' => $createdBy,
                    'ip_address' => $ip,
                    'reference_no' => ' ',
                    'file_name' => ' ',
                    'attendance_date' => $date,
                ]);
            } else {
                $inserted = null;
            }

            if (!empty($inserted)) {
                $loc_id = $inserted->id;
                $this->insertAttendance($userId, $punchTime, $date, $loc_id);
            }


            return $inserted ? true : false;
        } catch (\Exception $e) {
            $this->error('Database error->: ' . $e);
            return false;
        }
    }

    private function insertAttendance($userId, $punchTime, $date, $loc_id)
    {

        try {
            $existingLog = AttnOfficeInOutLog::where('employee_id', $userId)->whereDate('attendance_date', $date)->first();
            if ($existingLog) {
                $earlyoutTime = $this->calculateEarlyOutTime($userId, $date);

                DB::table('attn_office_in_out_log')
                ->where('employee_id', $userId)
                ->whereDate('attendance_date', $date) // Ensure correct date format
                ->update([
                    'out_time' => $punchTime,
                    'early_out_minute' => $earlyoutTime,
                    'out_time_loc_id' => $loc_id,
                ]);

                // $existingLog->update([
                //     'out_time' => $punchTime,
                //     'early_out_minute' => $earlyoutTime,
                //     'out_time_loc_id' => $loc_id,
                // ]);
            } else {
                $lateTime = $this->calculateLateTime($userId, $date);
                AttnOfficeInOutLog::Create([
                    'employee_id' => $userId,
                    'attendance_date' => $date,
                    'in_time' => $punchTime,
                    'late_in_minute' => $lateTime,
                    'in_time_loc_id' => $loc_id,
                    'out_time' => $punchTime,
                    'early_out_minute' => 0,
                    'out_time_loc_id' => $loc_id,
                ]);
            }
        } catch (\Exception $e) {
            $this->error(' Insert Attendance Database error: ' . $e->getMessage());
            return false;
        }
    }


    public function calculateLateTime($employeeId, $attendanceDate)
    {


        $schedule = AttnOfficeSchedule::where('employee_id', $employeeId)->whereDate('attendance_date', $attendanceDate)->first();
        $setting = AttnLtSett::find($schedule->attn_set_rules_id);
        $graceInTime = $setting ? $setting->grace_in_min_day : 0;


        $attLog = AttnLog::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if ($schedule && $attLog) {

            $expectedStartTime = Carbon::parse($schedule->start_time)->addMinutes($graceInTime);
            $actualPunchTime = Carbon::parse($attLog->punch_time);


            if ($actualPunchTime > $expectedStartTime) {
                $lateMinutes = $actualPunchTime->diffInMinutes($expectedStartTime);
                return $lateMinutes;
            } else {
                return 0;
            }
        }

        return 0;
    }

    public function calculateEarlyOutTime($employeeId, $attendanceDate)
    {


        $schedule = AttnOfficeSchedule::where('employee_id', $employeeId)->whereDate('attendance_date', $attendanceDate)->first();
        $setting = AttnLtSett::find($schedule->attn_set_rules_id);
        $graceOutTime = $setting ? $setting->grace_out_min_day : 0;


        $attLog = AttnLog::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->orderBy('id', 'desc')
            ->first();

        if ($schedule && $attLog) {

            $expectedEndTime = Carbon::parse($schedule->end_time);
            $actualPunchTime = Carbon::parse($attLog->punch_time)->addMinutes($graceOutTime);


            if ($actualPunchTime < $expectedEndTime) {
                $earlyMinutes = $actualPunchTime->diffInMinutes($expectedEndTime);
                return $earlyMinutes;
            } else {
                return 0;
            }
        }

        return 0;
    }
}
