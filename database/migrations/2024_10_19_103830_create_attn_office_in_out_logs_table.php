<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attn_office_in_out_log', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->date('attendance_date');
            $table->time('in_time')->nullable();
            $table->integer('late_in_minute')->nullable();
            $table->unsignedBigInteger('in_time_loc_id')->nullable();
            $table->time('out_time')->nullable();
            $table->integer('early_out_minute')->nullable();
            $table->unsignedBigInteger('out_time_loc_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attn_office_in_out_log');
    }
};
