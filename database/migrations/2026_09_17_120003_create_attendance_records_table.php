<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->uuid('work_schedule_id')->nullable();
            $table->foreign('work_schedule_id')->references('id')->on('work_schedules')->nullOnDelete();
            $table->date('attendance_date');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 10, 7)->nullable();
            $table->string('check_in_photo', 255)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'permission', 'leave', 'holiday', 'off'])->default('absent');
            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->enum('source', ['mobile', 'fingerprint', 'manual', 'system'])->default('mobile');
            $table->string('source_device_id', 100)->nullable();
            $table->string('source_event_id', 255)->nullable();
            $table->uuid('work_location_id')->nullable();
            $table->foreign('work_location_id')->references('id')->on('work_locations')->nullOnDelete();
            $table->boolean('is_outside_geofence')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'employee_id', 'attendance_date']);
            $table->index(['tenant_id', 'employee_id', 'attendance_date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
