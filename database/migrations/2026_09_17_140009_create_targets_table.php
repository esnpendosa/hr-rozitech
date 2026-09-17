<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id')->nullable();
            $table->string('name', 255);
            $table->string('metric', 100);
            $table->decimal('target_value', 15, 2);
            $table->string('unit', 50)->nullable();
            $table->enum('period', ['daily', 'weekly', 'monthly', 'quarterly', 'annual', 'custom'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('actual_value', 15, 2)->default(0);
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->decimal('remaining_value', 15, 2)->default(0);
            $table->decimal('required_daily_rate', 15, 2)->default(0);
            $table->enum('risk_level', ['on_track', 'at_risk', 'critical', 'completed', 'expired'])->default('on_track');
            $table->enum('status', ['created', 'assigned', 'active', 'completed', 'expired'])->default('created');
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status', 'end_date']);
            $table->index(['tenant_id', 'risk_level']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
