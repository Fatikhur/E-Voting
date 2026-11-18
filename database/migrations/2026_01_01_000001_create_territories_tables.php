<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kelurahan / Desa
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('district')->comment('Kecamatan');
            $table->string('regency')->comment('Kabupaten / Kota');
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();
        });

        // 2. RW (Rukun Warga)
        Schema::create('rws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('rw_number', 10);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'rw_number']);
        });

        // 3. RT (Rukun Tetangga)
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->cascadeOnDelete();
            $table->string('rt_number', 10);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['rw_id', 'rt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rts');
        Schema::dropIfExists('rws');
        Schema::dropIfExists('villages');
    }
};
