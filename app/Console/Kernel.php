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
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('20 9 * * *'); // 9:20 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('20 10 * * *'); // 10:20 AM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('0 15 * * *');  // 3:00 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('20 15 * * *');  // 3:20 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('30 16 * * *');  // 4:30 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('45 16 * * *');  // 4:45 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('10 17 * * *');  // 5:10 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('20 17 * * *');  // 5:20 PM
        // $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('15 18 * * *');  // 6:15 PM
        // $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('25 18 * * *');  // 6:25 PM

        $schedule->command('fetch:attendance 192.168.1.155 4370')->cron('8 16 * * *');  // 6:15 PM
        $schedule->command('fetch:attendance 192.168.1.176 4370')->cron('15 16 * * *');  // 6:25 PM

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
