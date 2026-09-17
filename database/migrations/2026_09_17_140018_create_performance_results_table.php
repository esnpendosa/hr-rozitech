<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('period_id');
            $table->uuid('employee_id');
            $table->decimal('kpi_score', 5, 2)->default(0);
            $table->decimal('task_score', 5, 2)->default(0);
            $table->decimal('target_score', 5, 2)->default(0);
            $table->decimal('attendance_score', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->default(0);
            $table->string('grade', 10)->nullable(); // A, B, C, D
            $table->text('notes')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('created_at')->nullable();

            // Unique: one result per employee per period
            $table->unique(['period_id', 'employee_id']);

            // Indexes
            $table->index(['tenant_id', 'period_id']);
            $table->index(['tenant_id', 'employee_id']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('period_id')
                ->references('id')
                ->on('performance_periods')
                ->cascadeOnDelete();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_results');
    }
};
