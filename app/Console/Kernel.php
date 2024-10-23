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
        $schedule->command('fetch:attendence-new-155')->everyTenMinutes()
        ->between('09:00', '19:00');
        $schedule->command('fetch:attendence-old-176')->everyFiveMinutes()
        ->between('09:00', '19:00');
        // $schedule->command('fetch:attendance-get')->cron('20 9 * * *'); // 9:20 AM
        // $schedule->command('fetch:attendance-get')->cron('20 10 * * *'); // 10:20 AM
        // $schedule->command('fetch:attendance-get')->cron('0 14 * * *');  // 2:00 PM
        // $schedule->command('fetch:attendance-get')->cron('0 16 * * *');  // 4:00 PM
        // $schedule->command('fetch:attendance-get')->cron('40 16 * * *');  // 4:00 PM

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
