<?php

namespace App\Console;

use Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('fetch:sequential-attendance', ['time' => '10:30 AM'])->cron('30 10 * * *'); // 10:30 AM
        $schedule->command('fetch:sequential-attendance', ['time' => '12:30 PM'])->cron('30 12 * * *'); // 12:30 PM
        $schedule->command('fetch:sequential-attendance', ['time' => '17:30 PM'])->cron('30 12 * * *'); // 5:30 PM
        $schedule->command('fetch:sequential-attendance', ['time' => '18:30 PM'])->cron('30 12 * * *'); // 6:30 PM

        // //-------------------------------------- Check In- 9:30 AM---------------------------------------------//
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('30 9 * * *'); // 9:30 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 9 * * *'); // 9:30 AM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('30 9 * * *'); // 9:30 AM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('30 9 * * *'); // 9:30 AM

        // //-------------------------------------- Check In- 10:30 AM---------------------------------------------//
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('30 10 * * *'); // 10:30 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 10 * * *'); // 10:30 AM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('30 10 * * *');  // 10:30 AM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('30 10 * * *'); // 10:30 AM





        // //-------------------------------------- Check In- 12:30 AM---------------------------------------------//
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('30 12 * * *'); // 12:30 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 12 * * *'); // 12:30 AM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('30 12 * * *');  // 12:30 AM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('30 12 * * *'); // 12:30 AM


        // //-------------------------------------- Check Out- 05:30 PM---------------------------------------------//
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('30 17 * * *'); // 05:30 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 17 * * *'); // 05:30 PM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('30 17 * * *');  // 05:30 PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('30 17 * * *'); // 05:30 PM
        // //-------------------------------------- Check Out- 06:30 PM---------------------------------------------//
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('30 18 * * *'); // 06:30 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 18 * * *'); // 06:30 PM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('30 18 * * *');  // 06:30 PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('30 18 * * *'); // 06:30 PM





        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('10 9 * * *'); // 9:10 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 9 * * *'); // 9:25 AM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('10 10 * * *'); // 10:10 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 10 * * *'); // 10:25 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('0 15 * * *');  // 3:00 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('20 15 * * *');  // 3:20 PM
        // // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 16 * * *');  // 4:30 PM
        // // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('45 16 * * *');  // 4:45 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('15 17 * * *');  // 5:15 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 17 * * *');  // 5:25 PM

        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('15 18 * * *');  // 6:15 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 18 * * *');  // 6:25 PM


        // //Dhaka
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('40 9 * * *');  // 9:40 PM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('30 10 * * *');  // 9:40 PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('55 9 * * *');  // 9:55: PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('40 10 * * *');  // 9:55: PM

        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('00 12 * * *');  // 12:00 PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('15 12 * * *');  // 12:15: PM

        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('40 17 * * *');  // 5:40 PM
        // $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('40 18 * * *');  // 5:40 PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('45 17 * * *');  // 6:45 PM
        // $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('45 18 * * *');  // 6:45 PM

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
