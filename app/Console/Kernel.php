<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('fetch:attendance-get')->everyMinute()
        // ->between('09:00', '19:00');
        // $schedule->command('fetch:attendence-new-155')->everyTenMinutes()
        // ->between('09:00', '19:00');
        // $schedule->command('fetch:attendence-old-176')->everyFiveMinutes()
        // ->between('09:00', '19:00');
        $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('10 9 * * *'); // 9:10 AM
        $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 9 * * *'); // 9:25 AM
        $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('10 10 * * *'); // 10:10 AM
        $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 10 * * *'); // 10:25 AM
        $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('0 15 * * *');  // 3:00 PM
        $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('20 15 * * *');  // 3:20 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 16 * * *');  // 4:30 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('45 16 * * *');  // 4:45 PM
        $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('15 17 * * *');  // 5:15 PM
        $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 17 * * *');  // 5:25 PM

        $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('15 18 * * *');  // 6:15 PM
        $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 18 * * *');  // 6:25 PM


        //Dhaka
        $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('40 9 * * *');  // 9:40 PM
        $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('55 9 * * *');  // 9:55: PM

        $schedule->command('fetch:attendance 192.168.0.70 4370')->cron('40 17 * * *');  // 5:40 PM
        $schedule->command('fetch:attendance 192.168.0.134 4370')->cron('45 18 * * *');  // 6:45 PM

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
