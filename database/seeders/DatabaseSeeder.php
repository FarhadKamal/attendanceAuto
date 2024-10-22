<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('attn_lt_sett')->insert([
            [
                'name' => 'General Attendance Policy (Regular)',
                'late_rules_type' => 'Day',
                'grace_in_min_day' => 0,
                'check_grace_out_min_day' => 0,
                'grace_out_min_day' => 0,
                'grace_in_min_month' => 0,
                'check_grace_out_min_month' => 0,
                'grace_out_min_month' => 0,
                'ms_yr_prd_id' => 0,
                'ms_mn_prd_id' => 'Yes',
                'lwp_active_status' => 'Yes',
                'lwp_deduction_type' => 'Basic Salary',
                'salary_comp_type_id' => null,
                'last_update_date' => '2018-08-12 10:15:36',
                'is_active' => 'Yes'
            ],
            [
                'name' => 'General Attendance Policy (Consolidated or Contract)',
                'late_rules_type' => 'Day',
                'grace_in_min_day' => 0,
                'check_grace_out_min_day' => 0,
                'grace_out_min_day' => 0,
                'grace_in_min_month' => 0,
                'check_grace_out_min_month' => 0,
                'grace_out_min_month' => 0,
                'ms_yr_prd_id' => 0,
                'ms_mn_prd_id' => '',
                'lwp_active_status' => 'Yes',
                'lwp_deduction_type' => 'Basic Salary',
                'salary_comp_type_id' => 'grp1',
                'last_update_date' => '2018-08-12 10:17:54',
                'is_active' => 'No'
            ]
        ]);
    }
}
