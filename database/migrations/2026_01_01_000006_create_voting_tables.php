<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daftar Pemilih Tetap (DPT) per Pemilihan
        Schema::create('election_eligible_voters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('has_voted')->default(false)->index();
            $table->dateTime('voted_at')->nullable();
            // Bukti penerimaan anonim yang diberikan kepada pemilih (tidak dapat dikorelasikan ke kandidat)
            $table->string('receipt_token', 100)->nullable()->unique();
            $table->timestamps();

            $table->unique(['election_id', 'resident_id']);
            $table->unique(['election_id', 'user_id']);
            $table->index(['election_id', 'has_voted']);
        });

        // Kotak Suara Rahasia (SECRET BALLOT)
        // PERINGATAN ARSITEKTUR KEAMANAN:
        // Tabel ini TIDAK BOLEH memiliki relasi atau kolom user_id, resident_id, IP address, atau token pemilih.
        // Kerahasiaan suara dijamin oleh pemisahan mutlak antara tabel hak suara dan tabel surat suara ini.
        Schema::create('ballots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->date('cast_date')->index();
            $table->string('ballot_hash', 64)->unique();
            $table->boolean('is_valid')->default(true)->index();
            $table->timestamps();

            $table->index(['election_id', 'candidate_id']);
        });

        // Token Voting Sekali Pakai (Single-Use Cryptographic Nonce)
        Schema::create('voting_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->dateTime('expires_at');
            $table->boolean('is_used')->default(false)->index();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->index(['election_id', 'user_id', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_tokens');
        Schema::dropIfExists('ballots');
        Schema::dropIfExists('election_eligible_voters');
    }
};
