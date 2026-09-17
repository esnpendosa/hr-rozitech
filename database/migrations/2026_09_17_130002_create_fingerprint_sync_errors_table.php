<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_sync_errors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('sync_run_id')->constrained('fingerprint_sync_runs')->cascadeOnDelete();
            $table->foreignUuid('device_id')->constrained('fingerprint_devices')->cascadeOnDelete();
            $table->uuid('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->jsonb('raw_data')->nullable();
            $table->text('error_message');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'sync_run_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_sync_errors');
    }
};
