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
        Schema::create('attn_log', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->date('attendance_date')->nullable();
            $table->time('punch_time')->nullable();
            $table->timestamp('entry_time')->nullable();
            $table->integer('loc_id')->nullable()->default(0);
            $table->string('verify_code')->nullable();
            $table->string('entry_type')->nullable()->default('machine');
            $table->integer('created_by')->nullable()->default(1);
            $table->string('ip_address')->nullable();
            $table->string('reference_no')->nullable()->default(1);
            $table->string('file_name')->nullable()->default('');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attn_log');
    }
};
