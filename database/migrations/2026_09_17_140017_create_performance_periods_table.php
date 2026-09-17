<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->jsonb('config_snapshot')->nullable(); // snapshot of weight config at calculation time
            $table->decimal('kpi_weight', 5, 2)->default(40);
            $table->decimal('task_weight', 5, 2)->default(20);
            $table->decimal('target_weight', 5, 2)->default(20);
            $table->decimal('attendance_weight', 5, 2)->default(20);
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_periods');
    }
};
