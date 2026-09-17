<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->uuid('device_id')->nullable();
            $table->foreign('device_id')->references('id')->on('fingerprint_devices')->nullOnDelete();
            $table->enum('event_type', ['check_in', 'check_out', 'break_start', 'break_end'])->default('check_in');
            $table->timestamp('event_time');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('source', ['mobile', 'fingerprint', 'manual'])->default('mobile');
            $table->string('source_device_id', 100)->nullable();
            $table->string('source_event_id', 255)->nullable();
            $table->jsonb('raw_data')->nullable();
            $table->boolean('processed')->default(false);
            $table->uuid('attendance_record_id')->nullable();
            $table->foreign('attendance_record_id')->references('id')->on('attendance_records')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['tenant_id', 'employee_id', 'event_time']);
            $table->index(['tenant_id', 'processed']);
        });

        // Partial unique index: only when source_event_id IS NOT NULL
        DB::statement('
            CREATE UNIQUE INDEX attendance_events_tenant_device_event_unique
            ON attendance_events (tenant_id, source_device_id, source_event_id)
            WHERE source_event_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
    }
};
