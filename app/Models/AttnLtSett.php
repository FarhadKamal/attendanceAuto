<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttnLtSett extends Model
{
    use HasFactory;

    protected $table ='attn_lt_sett';
    protected $fillable = [
        'name',
        'late_rules_type',
        'grace_in_min_day',
        'check_grace_out_min_day',
        'grace_out_min_day',
        'grace_in_min_month',
        'check_grace_out_min_month',
        'grace_out_min_month',
        'ms_yr_prd_id',
        'ms_mn_prd_id',
        'lwp_active_status',
        'lwp_deduction_type',
        'salary_comp_type_id',
        'last_update_date',
        'is_active'
    ];
}
