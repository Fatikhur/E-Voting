<?php

namespace App\Services;

use App\Models\AuditCheckpoint;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public const GENESIS_HASH = '0000000000000000000000000000000000000000000000000000000000000000';

    /**
     * Catat audit log baru dengan mekanisme Cryptographic Hash Chaining.
     */
    public function log(
        string $action,
        string $eventType,
        string $description,
        ?array $metadata = null,
        ?User $user = null
    ): AuditLog {
        return DB::transaction(function () use ($action, $eventType, $description, $metadata, $user) {
            $currentUser = $user ?? Auth::user();
            $now = Carbon::now();
            $timestampString = $now->toDateTimeString();

            // Kunci tabel log secara berurutan untuk menjaga konsistensi rantai hash
            $lastLog = AuditLog::query()
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $prevHash = $lastLog ? $lastLog->current_hash : self::GENESIS_HASH;

            $userId = $currentUser ? $currentUser->id : null;
            $username = $currentUser ? $currentUser->username : 'system';
            $role = $currentUser ? $currentUser->role_name : 'system';
            $canonicalMetadata = AuditLog::canonicalizeMetadata($metadata);

            $currentHash = AuditLog::calculateHash(
                $prevHash,
                $timestampString,
                $userId,
                $action,
                $eventType,
                $description,
                $canonicalMetadata
            );

            return AuditLog::create([
                'user_id' => $userId,
                'username' => $username,
                'role' => $role,
                'action' => $action,
                'event_type' => $eventType,
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'user_agent' => Request::userAgent() ?? 'CLI/System',
                'description' => $description,
                'metadata' => $canonicalMetadata,
                'prev_hash' => $prevHash,
                'current_hash' => $currentHash,
                'created_at' => $now,
            ]);
        });
    }

    /**
     * Verifikasi integritas seluruh rantai hash audit log (Tamper-evident verification).
     * Mendeteksi jika ada data yang dimanipulasi, diubah, atau dihapus langsung di database.
     */
    public function verifyChainIntegrity(): array
    {
        $logs = AuditLog::query()->orderBy('id', 'asc')->get();

        if ($logs->isEmpty()) {
            return [
                'is_valid' => true,
                'total_verified' => 0,
                'message' => 'Belum ada catatan audit log dalam sistem.',
                'latest_hash' => self::GENESIS_HASH,
            ];
        }

        $expectedPrevHash = self::GENESIS_HASH;

        foreach ($logs as $index => $log) {
            // 1. Cek apakah prev_hash sesuai dengan hash baris sebelumnya
            if ($log->prev_hash !== $expectedPrevHash) {
                return [
                    'is_valid' => false,
                    'total_verified' => $index,
                    'latest_hash' => $expectedPrevHash,
                    'corrupted_id' => $log->id,
                    'index' => $index + 1,
                    'error_type' => 'PREV_HASH_MISMATCH',
                    'message' => "Integritas terputus pada Log ID #{$log->id}. Nilai prev_hash tidak cocok dengan baris sebelumnya.",
                    'expected_prev_hash' => $expectedPrevHash,
                    'actual_prev_hash' => $log->prev_hash,
                ];
            }

            // 2. Hitung ulang current_hash dan bandingkan dengan yang tersimpan
            $recalculatedHash = AuditLog::calculateHash(
                $log->prev_hash,
                $log->created_at->toDateTimeString(),
                $log->user_id,
                $log->action,
                $log->event_type,
                $log->description,
                $log->metadata
            );

            if ($log->current_hash !== $recalculatedHash) {
                return [
                    'is_valid' => false,
                    'total_verified' => $index,
                    'latest_hash' => $expectedPrevHash,
                    'corrupted_id' => $log->id,
                    'index' => $index + 1,
                    'error_type' => 'DATA_TAMPERED',
                    'message' => "Integritas terputus pada Log ID #{$log->id}. Konten log telah dimanipulasi atau diubah di database.",
                    'expected_hash' => $recalculatedHash,
                    'stored_hash' => $log->current_hash,
                ];
            }

            $expectedPrevHash = $log->current_hash;
        }

        return [
            'is_valid' => true,
            'total_verified' => $logs->count(),
            'message' => 'Seluruh rantai audit log valid dan utuh (tidak ada manipulasi data terdeteksi).',
            'latest_hash' => $expectedPrevHash,
        ];
    }

    /**
     * Buat checkpoint integritas berkala yang dapat ditandatangani oleh Pengawas / Admin.
     */
    public function createCheckpoint(?int $electionId = null, ?string $note = null, ?User $signedBy = null): AuditCheckpoint
    {
        $currentUser = $signedBy ?? Auth::user();
        if (!$currentUser) {
            throw new \RuntimeException('Pengguna yang menandatangani checkpoint harus terotentikasi.');
        }

        $integrity = $this->verifyChainIntegrity();
        if (!$integrity['is_valid']) {
            throw new \RuntimeException('Tidak dapat membuat checkpoint: Rantai audit log terdeteksi mengalami kerusakan atau manipulasi!');
        }

        $lastCheckpoint = AuditCheckpoint::orderBy('checkpoint_sequence', 'desc')->first();
        $nextSequence = $lastCheckpoint ? $lastCheckpoint->checkpoint_sequence + 1 : 1;
        $startAuditId = $lastCheckpoint ? $lastCheckpoint->end_audit_id + 1 : 1;

        $lastLog = AuditLog::orderBy('id', 'desc')->first();
        $endAuditId = $lastLog ? $lastLog->id : 0;
        $totalLogsCounted = AuditLog::count();

        $checkpointHash = hash('sha256', implode('|', [
            $nextSequence,
            $startAuditId,
            $endAuditId,
            $totalLogsCounted,
            $integrity['latest_hash'],
            $currentUser->id,
            Carbon::now()->toIso8601String(),
        ]));

        $checkpoint = AuditCheckpoint::create([
            'checkpoint_sequence' => $nextSequence,
            'election_id' => $electionId,
            'start_audit_id' => $startAuditId,
            'end_audit_id' => $endAuditId,
            'total_logs_counted' => $totalLogsCounted,
            'checkpoint_hash' => $checkpointHash,
            'signature_note' => $note ?? 'Checkpoint audit log ditandatangani secara berkala.',
            'signed_by' => $currentUser->id,
        ]);

        $this->log(
            action: 'create_checkpoint',
            eventType: 'security',
            description: "Membuat Checkpoint Audit #{$nextSequence} dengan hash {$checkpointHash}",
            metadata: [
                'checkpoint_id' => $checkpoint->id,
                'sequence' => $nextSequence,
                'end_audit_id' => $endAuditId,
                'total_logs' => $totalLogsCounted,
            ],
            user: $currentUser
        );

        return $checkpoint;
    }
}
