<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('target_id');
            $table->uuid('recorded_by')->nullable();
            $table->decimal('value', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamp('created_at')->nullable();

            // Indexes
            $table->index(['tenant_id', 'target_id', 'recorded_at']);

            // Foreign keys
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreign('target_id')
                ->references('id')
                ->on('targets')
                ->cascadeOnDelete();

            $table->foreign('recorded_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_progress');
    }
};
