<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Rats\Zkteco\Lib\ZKTeco;

class FetchZKTData extends Command
{
    protected $signature = 'fetch:zkt-data';
    protected $description = 'Fetch data from ZKT device and insert into database';

    public function handle()
    {
        echo('All Ok');
        $zk = new ZKTeco('192.168.1.153', 4370); // IP and port of the ZKT device

        if ($zk->connect()) {
            $users = $zk->getUser(); // Method to fetch users (check library documentation)

            foreach ($users as $user) {
                User::updateOrCreate(
                    ['employee_id' => $user['userid']], // Assuming employee_id is unique
                    [
                        'name' => $user['name'],
                        'password' => bcrypt('123'), // Handle passwords securely
                        // Add other fields as needed
                    ]
                );
            }

            $zk->disconnect();
            $this->info('Data fetched and inserted successfully.');
        } else {
            $this->error('Failed to connect to ZKT device.');
        }
    }
}
