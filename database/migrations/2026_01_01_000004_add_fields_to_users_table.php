<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('name');
            $table->string('email')->nullable()->change();
            $table->string('role_name', 20)->default('voter')->index()->after('password');
            $table->foreignId('role_id')->nullable()->after('role_name')->constrained('roles')->nullOnDelete();
            $table->foreignId('resident_id')->nullable()->after('role_id')->constrained('residents')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->after('resident_id')->constrained('villages')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->after('village_id')->constrained('rws')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->after('rw_id')->constrained('rts')->nullOnDelete();
            $table->string('phone', 25)->nullable()->after('rt_id');
            $table->enum('status', ['active', 'inactive'])->default('active')->index()->after('phone');
            $table->boolean('must_change_password')->default(false)->after('status');
            $table->text('two_factor_secret')->nullable()->after('must_change_password');
            $table->timestamp('last_login_at')->nullable()->after('two_factor_secret');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['resident_id']);
            $table->dropForeign(['village_id']);
            $table->dropForeign(['rw_id']);
            $table->dropForeign(['rt_id']);
            $table->dropColumn([
                'username',
                'role_name',
                'role_id',
                'resident_id',
                'village_id',
                'rw_id',
                'rt_id',
                'phone',
                'status',
                'must_change_password',
                'two_factor_secret',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
