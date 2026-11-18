<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            // NIK Security: HMAC-SHA256 hash for deterministic, indexable unique lookup without exposing plain NIK
            $table->string('nik_hash', 64)->unique()->index();
            // Encrypted NIK using application encryption key for secure reversible storage if needed
            $table->text('nik_encrypted');
            // Masked display representation (e.g., 3201**********12)
            $table->string('nik_masked', 20);
            $table->string('name')->index();
            $table->text('no_kk_encrypted')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->text('address');
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->foreignId('rw_id')->constrained('rws')->cascadeOnDelete();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->string('phone', 25)->nullable();
            $table->enum('status', ['active', 'moved', 'deceased'])->default('active')->index();
            $table->boolean('is_eligible_voter')->default(true)->index();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['rw_id', 'rt_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
