<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audit Logs dengan Rantai Hash Kriptografis (Cryptographic Hash Chaining)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username')->nullable();
            $table->string('role')->nullable();
            $table->string('action');
            $table->string('event_type', 30)->index()->comment('auth, resident, voter, election, candidate, schedule, status_change, supervision, approval, security');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description');
            $table->json('metadata')->nullable();
            // Hash chaining fields untuk mendeteksi pemalsuan log
            $table->string('prev_hash', 64);
            $table->string('current_hash', 64)->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['event_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // 2. Audit Checkpoints (Integritas Periodik / Titik Verifikasi yang Dapat Ditandatangani)
        Schema::create('audit_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('checkpoint_sequence')->unique();
            $table->foreignId('election_id')->nullable()->constrained('elections')->nullOnDelete();
            $table->unsignedBigInteger('start_audit_id');
            $table->unsignedBigInteger('end_audit_id');
            $table->unsignedInteger('total_logs_counted');
            $table->string('checkpoint_hash', 64);
            $table->text('signature_note')->nullable();
            $table->foreignId('signed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Persetujuan Dua Pihak Sebelum Hasil Dipublikasi (Two-Person Rule: Admin & Pengawas)
        Schema::create('election_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->unsignedTinyInteger('required_approvals')->default(2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            
            // Persetujuan Pihak 1 (Admin)
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('admin_approved')->default(false);
            $table->dateTime('admin_approved_at')->nullable();
            $table->text('admin_notes')->nullable();

            // Persetujuan Pihak 2 (Pengawas)
            $table->foreignId('supervisor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('supervisor_approved')->default(false);
            $table->dateTime('supervisor_approved_at')->nullable();
            $table->text('supervisor_notes')->nullable();

            $table->timestamps();
        });

        // 4. Laporan / Catatan Pengawas (Supervision Reports)
        Schema::create('observer_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignId('supervisor_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('report_type', ['catatan', 'indikasi_pelanggaran', 'rekomendasi', 'klarifikasi'])->default('catatan');
            $table->string('title');
            $table->text('content');
            $table->string('evidence_path')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'resolved'])->default('submitted')->index();
            $table->text('admin_response')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });

        // 5. System Settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('observer_reports');
        Schema::dropIfExists('election_approvals');
        Schema::dropIfExists('audit_checkpoints');
        Schema::dropIfExists('audit_logs');
    }
};
