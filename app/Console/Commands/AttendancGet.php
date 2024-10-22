<?php

namespace App\Console\Commands;

use App\Models\AttnLog;
use App\Models\AttnLtSett;
use App\Models\AttnOfficeInOutLog;
use App\Models\AttnOfficeSchedule;
use App\Services\ZktService;
use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Rats\Zkteco\Lib\ZKTeco;

class AttendancGet extends Command
{


    protected $signature = 'fetch:attendance-get';


    public function handle()
    {

        $device = new ZKTeco('192.168.1.153', 4370);

        if ($device->connect()) {
            // Fetch attendance data

            $a = $device->writeLcd();
            dd($a);

            $attendanceData = $device->getAttendance();

            // dd($attendanceData);

            $lastAttnLogDate = AttnLog::orderBy('attendance_date', 'desc')->value('attendance_date');
            // $startDate = '2023-10-20';
            // $endDate = '2024-10-22';

            // $this->zktService->deleteAttendanceData($startDate,$endDate);

            if (!empty($attendanceData)) {
                $groupedData = [];
                foreach ($attendanceData as $record) {
                    $userId = $record['id'];
                    $timestamp = $record['timestamp'];
                    $date = date('Y-m-d', strtotime($timestamp));
                    if ($date >= $lastAttnLogDate) {

                        $groupedData[$userId][$date][] = $timestamp;
                    }


                    // $groupedData[$userId][$date][] = $timestamp;
                }


                foreach ($groupedData as $userId => $dates) {

                    foreach ($dates as $date => $timestamps) {

                        foreach ($timestamps as $timestamp) {
                            $punchTime = date('H:i:s', strtotime($timestamp));
                            $inserted = $this->insertAttnLog($userId, $punchTime, $date);
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
        } else {
            $this->error('Failed to connect to ZKT device.');
        }
    }

    private function insertAttnLog($userId, $punchTime, $date)
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
                    'ip_address' => "192.168.0.153",
                    'attendance_date' => $date,
                ]);
            } else {
                $inserted = null;
            }

            if(!empty($inserted))
            {
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
                $existingLog->update([
                    'out_time' => $punchTime,
                    'early_out_minute' => $earlyoutTime,
                    'out_time_loc_id' => $loc_id,
                ]);
            } else {
                $lateTime = $this->calculateLateTime($userId, $date);
                AttnOfficeInOutLog::Create([
                    'employee_id' => $userId,
                    'attendance_date' => $date,
                    'in_time' => $punchTime,
                    'late_in_minute' => $lateTime,
                    'in_time_loc_id' => $loc_id
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
