<?php

namespace App\Console\Commands;

use App\Models\AttnLog;
use App\Models\AttnLtSett;
use App\Models\AttnOfficeInOutLog;
use App\Models\AttnOfficeSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Rats\Zkteco\Lib\ZKTeco;


class AttendenceProcess extends Command
{
    protected $signature = 'fetch:attendance-process';

    public function handle()
    {
        $device = new ZKTeco('192.168.1.153', 4370);

        if ($device->connect()) {
            // Fetch attendance data
            $attendanceData = $device->getAttendance();
            $lastAttnLogDate = AttnLog::orderBy('attendance_date', 'desc')->value('attendance_date');
            if (!empty($attendanceData)) {
                $groupedData = [];
                foreach ($attendanceData as $record) {
                    $userId = $record['id'];
                    $timestamp = $record['timestamp'];
                    $date = date('Y-m-d', strtotime($timestamp));

                    if ($date >= $lastAttnLogDate) {
                        $groupedData[$userId][$date][] = $timestamp;
                    }
                }

                DB::beginTransaction(); // Use transaction for batch processing
                try {
                    $insertedLogs = [];
                    foreach ($groupedData as $userId => $dates) {
                        foreach ($dates as $date => $timestamps) {
                            foreach ($timestamps as $timestamp) {
                                $punchTime = date('H:i:s', strtotime($timestamp));

                                // Batch collect inserts
                                $logExists = AttnLog::where('employee_id', $userId)
                                    ->where('attendance_date', $date)
                                    ->where('punch_time', $punchTime)
                                    ->exists();

                                if (!$logExists) {
                                    $insertedLogs[] = [
                                        'employee_id' => $userId,
                                        'punch_time' => $punchTime,
                                        'entry_time' => now(),
                                        'ip_address' => "192.168.0.153",
                                        'attendance_date' => $date,
                                    ];
                                }
                            }
                        }
                    }

                    if (!empty($insertedLogs)) {
                        // AttnLog::insert($insertedLogs);
                        // dd($insertedLogs);

                        foreach ($insertedLogs as $log) {
                            $insertedLog = AttnLog::create($log);
                            $this->insertAttendance($log['employee_id'], $log['punch_time'], $log['attendance_date'], $insertedLog->id);
                        }

                        $device->clearAttendance(); // Clear attendance once after all records are inserted
                    }

                    DB::commit();
                    $this->info('Attendance log fetched and stored successfully.');
                } catch (\Exception $e) {
                    DB::rollBack(); // Rollback in case of an error
                    $this->error('Error during processing: ' . $e->getMessage());
                }

                $device->disconnect();
            }
        } else {
            $this->error('Failed to connect to ZKT device.');
        }
    }

    private function insertAttendance($userId, $punchTime, $date, $loc_id)
    {
        try {
            $existingLog = AttnOfficeInOutLog::where('employee_id', $userId)
                ->whereDate('attendance_date', $date)
                ->first();

            if ($existingLog) {
                $earlyOutTime = $this->calculateEarlyOutTime($userId, $date);
                $existingLog->update([
                    'out_time' => $punchTime,
                    'early_out_minute' => $earlyOutTime,
                    'out_time_loc_id' => $loc_id,
                ]);
            } else {
                $lateTime = $this->calculateLateTime($userId, $date);
                AttnOfficeInOutLog::create([
                    'employee_id' => $userId,
                    'attendance_date' => $date,
                    'in_time' => $punchTime,
                    'late_in_minute' => $lateTime,
                    'in_time_loc_id' => $loc_id,
                ]);
            }
        } catch (\Exception $e) {
            $this->error('Insert Attendance Database error: ' . $e->getMessage());
        }
    }

    public function calculateLateTime($employeeId, $attendanceDate)
    {
        $schedule = AttnOfficeSchedule::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if (!$schedule) {
            return 0;
        }

        $setting = AttnLtSett::find($schedule->attn_set_rules_id);
        $graceInTime = $setting ? $setting->grace_in_min_day : 0;

        $attLog = AttnLog::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if ($attLog) {
            $expectedStartTime = Carbon::parse($schedule->start_time)->addMinutes($graceInTime);
            $actualPunchTime = Carbon::parse($attLog->punch_time);

            if ($actualPunchTime->greaterThan($expectedStartTime)) {
                return $actualPunchTime->diffInMinutes($expectedStartTime);
            }
        }

        return 0;
    }

    public function calculateEarlyOutTime($employeeId, $attendanceDate)
    {
        $schedule = AttnOfficeSchedule::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if (!$schedule) {
            return 0;
        }

        $setting = AttnLtSett::find($schedule->attn_set_rules_id);
        $graceOutTime = $setting ? $setting->grace_out_min_day : 0;

        $attLog = AttnLog::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->orderBy('id', 'desc')
            ->first();

        if ($attLog) {
            $expectedEndTime = Carbon::parse($schedule->end_time);
            $actualPunchTime = Carbon::parse($attLog->punch_time)->addMinutes($graceOutTime);

            if ($actualPunchTime->lessThan($expectedEndTime)) {
                return $expectedEndTime->diffInMinutes($actualPunchTime);
            }
        }

        return 0;
    }
}
