<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
            $table->uuid('uploaded_by')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            $table->string('disk', 50)->default('local');
            $table->string('path', 500);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('size')->default(0);
            $table->string('collection', 100)->nullable();
            $table->string('model_type', 255)->nullable();
            $table->uuid('model_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('tenant_id');
            $table->index(['model_type', 'model_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('files'); }
};
