<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('title');
            $table->enum('type', ['ketua_rt', 'ketua_rw']);
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->foreignId('rw_id')->constrained('rws')->cascadeOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->string('term_period', 50)->comment('Contoh: 2026 - 2031');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'open', 'closed', 'published', 'cancelled'])->default('draft')->index();
            $table->text('tie_breaker_rule')->nullable()->comment('Aturan jika terjadi perolehan suara seri');
            $table->boolean('is_published')->default(false)->index();
            $table->dateTime('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['start_time', 'end_time']);
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->unsignedInteger('candidate_number');
            $table->string('name');
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->text('address')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('vision');
            $table->text('mission');
            $table->text('bio')->nullable();
            $table->enum('status', ['active', 'withdrawn'])->default('active')->index();
            $table->text('withdrawal_reason')->nullable();
            $table->timestamps();

            $table->unique(['election_id', 'candidate_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('elections');
    }
};
