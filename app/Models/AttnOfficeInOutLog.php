<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttnOfficeInOutLog extends Model
{
    use HasFactory;
    protected $table ='attn_office_in_out_log';
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'in_time',
        'late_in_minute',
        'in_time_loc_id',
        'out_time',
        'early_out_minute',
        'out_time_loc_id',

    ];
}
