<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('assignment_id');
            $table->uuid('metric_id');
            $table->decimal('actual_value', 15, 2)->default(0);
            $table->decimal('score', 5, 2)->default(0);          // score 0-100
            $table->decimal('weighted_score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->uuid('recorded_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'assignment_id']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('assignment_id')
                ->references('id')
                ->on('kpi_assignments')
                ->cascadeOnDelete();

            $table->foreign('metric_id')
                ->references('id')
                ->on('kpi_metrics')
                ->cascadeOnDelete();

            $table->foreign('recorded_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_results');
    }
};
