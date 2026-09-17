<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_counters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('feature_key', 100);
            $table->integer('count')->default(0);
            $table->string('period', 20)->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'feature_key', 'period']);
            $table->index(['tenant_id', 'feature_key']);

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_counters');
    }
};
