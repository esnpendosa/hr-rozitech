<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('template_id')->nullable();
            $table->uuid('employee_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['active', 'closed', 'draft'])->default('draft');
            $table->integer('template_version')->default(1); // snapshot version at time of assignment
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'employee_id']);
            $table->index(['tenant_id', 'period_start', 'period_end']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('template_id')
                ->references('id')
                ->on('kpi_templates')
                ->nullOnDelete();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_assignments');
    }
};
