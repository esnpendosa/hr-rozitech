<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Konversi users table dari bigint PK ke UUID, tambah kolom tenant_id, fcm_token, locale.
     * Juga update foreign key di sessions table agar sesuai UUID.
     */
    public function up(): void
    {
        // Hapus foreign key & index di sessions yang referensi users.id dulu
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        // Drop kolom user_id lama di sessions (bigint)
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        // Hapus auto-increment id lama dari users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        // Tambah UUID id sebagai PK baru
        // PostgreSQL: perlu tambah kolom, set default, lalu jadikan PK
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->first();
        });

        // Set primary key
        DB::statement('ALTER TABLE users ADD PRIMARY KEY (id)');

        // Tambah kolom tenant_id, fcm_token, locale
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable()->after('id');
            $table->text('fcm_token')->nullable()->after('password');
            $table->string('locale', 10)->default('id')->after('fcm_token');
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('email');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->nullOnDelete();
        });

        // Tambah kembali user_id di sessions sebagai UUID
        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['email']);
            $table->dropColumn(['tenant_id', 'fcm_token', 'locale', 'deleted_at']);
        });

        DB::statement('ALTER TABLE users DROP CONSTRAINT users_pkey');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->index('user_id');
        });
    }
};
