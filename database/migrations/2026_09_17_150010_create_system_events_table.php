<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('system_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
            $table->string('event', 255);
            $table->jsonb('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['tenant_id', 'event']);
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('system_events'); }
};
