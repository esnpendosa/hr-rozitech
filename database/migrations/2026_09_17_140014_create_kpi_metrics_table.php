<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('template_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('metric_key', 100)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('weight', 5, 2); // percentage weight, total must sum to 100%
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('minimum_value', 15, 2)->nullable();
            $table->decimal('maximum_value', 15, 2)->nullable();
            $table->enum('scoring_method', ['higher_is_better', 'lower_is_better', 'target_based', 'custom'])->default('higher_is_better');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'template_id']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('template_id')
                ->references('id')
                ->on('kpi_templates')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_metrics');
    }
};
