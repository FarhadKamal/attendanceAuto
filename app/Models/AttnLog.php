<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttnLog extends Model
{
    use HasFactory;

    protected $table ='attn_log';
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'punch_time',
        'entry_time',
        'loc_id',
        'verify_code',
        'entry_type',
        'created_by',
        'ip_address',
        'reference_no',
        'file_name'
    ];
    public $timestamps = false;
}
