<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchSequentialAttendance extends Command
{


    protected $signature = 'fetch:sequential-attendance {time}';
    protected $description = 'Fetch attendance sequentially for each IP address based on time';

    public function handle()
    {
        $time = $this->argument('time');

        // Log start time for clarity
        $this->info("Starting sequential attendance fetch for {$time}.");

        // Execute each fetch command sequentially
        $this->call('fetch:attendance', ['ip' => '192.168.1.155', 'port' => '4370']);
        $this->call('fetch:attendance', ['ip' => '192.168.1.176', 'port' => '4370']);
        $this->call('fetch:attendance', ['ip' => '192.168.0.70', 'port' => '4370']);
        $this->call('fetch:attendance', ['ip' => '192.168.0.134', 'port' => '4370']);

        $this->info("Completed sequential attendance fetch for {$time}.");

        return Command::SUCCESS;
    }
}
