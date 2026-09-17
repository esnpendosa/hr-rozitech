<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_device_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('device_id')->constrained('fingerprint_devices')->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('device_user_id', 100); // ID karyawan di dalam device
            $table->boolean('is_enrolled')->default(false);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            $table->unique(['device_id', 'employee_id']);
            $table->index(['tenant_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_device_users');
    }
};
