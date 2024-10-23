<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttnOfficeSchedule extends Model
{
    use HasFactory;
    protected $table ='attn_office_schedule';
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'start_time',
        'end_time',
        'holiday_status',
        'holiday_type',
        'leave_status',
        'leave_half_day_status',
        'half_day_time',
        'leave_type',
        'attn_set_rules_id',
        'wknd_status',
        'rglar_stat',
        'movement_status',
        'short_movement_status',
    ];
    public $timestamps = false;
}
