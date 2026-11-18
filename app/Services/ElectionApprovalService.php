<?php

namespace App\Services;

use App\Models\Election;
use App\Models\ElectionApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ElectionApprovalService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Dapatkan atau inisialisasi entri approval pemilihan.
     */
    public function getOrCreateApproval(Election $election): ElectionApproval
    {
        return ElectionApproval::firstOrCreate(
            ['election_id' => $election->id],
            [
                'required_approvals' => 2,
                'status' => 'pending',
                'admin_approved' => false,
                'supervisor_approved' => false,
            ]
        );
    }

    /**
     * Pihak 1: Persetujuan dan Tanda Tangan Digital Admin Panitia Pemilihan.
     */
    public function approveByAdmin(Election $election, User $admin, ?string $notes = null): ElectionApproval
    {
        if (!$admin->isAdmin() && !$admin->isSuperAdmin()) {
            throw new \RuntimeException('Hanya Admin atau Super Admin yang dapat memberikan persetujuan panitia.');
        }

        if (!$election->isClosed()) {
            throw new \RuntimeException('Pemilihan harus dalam status ditutup (closed) sebelum persetujuan hasil dapat diproses.');
        }

        return DB::transaction(function () use ($election, $admin, $notes) {
            $approval = $this->getOrCreateApproval($election);

            $approval->admin_user_id = $admin->id;
            $approval->admin_approved = true;
            $approval->admin_approved_at = Carbon::now();
            $approval->admin_notes = $notes ?? 'Hasil penghitungan suara telah diverifikasi oleh Panitia Pemilihan.';

            if ($approval->supervisor_approved) {
                $approval->status = 'approved';
            }

            $approval->save();

            $this->auditLogService->log(
                action: 'approve_election_admin',
                eventType: 'approval',
                description: "Admin {$admin->username} menyetujui hasil pemilihan '{$election->title}' (ID #{$election->id})",
                metadata: [
                    'election_id' => $election->id,
                    'approval_id' => $approval->id,
                    'notes' => $notes,
                    'is_fully_approved' => $approval->isFullyApproved(),
                ],
                user: $admin
            );

            return $approval;
        });
    }

    /**
     * Pihak 2: Persetujuan dan Tanda Tangan Digital Pengawas Independen.
     */
    public function approveBySupervisor(Election $election, User $supervisor, ?string $notes = null): ElectionApproval
    {
        if (!$supervisor->isPengawas() && !$supervisor->isSuperAdmin()) {
            throw new \RuntimeException('Hanya Pengawas yang dapat memberikan persetujuan pengawasan.');
        }

        if (!$election->isClosed()) {
            throw new \RuntimeException('Pemilihan harus dalam status ditutup (closed) sebelum persetujuan hasil dapat diproses.');
        }

        return DB::transaction(function () use ($election, $supervisor, $notes) {
            $approval = $this->getOrCreateApproval($election);

            $approval->supervisor_user_id = $supervisor->id;
            $approval->supervisor_approved = true;
            $approval->supervisor_approved_at = Carbon::now();
            $approval->supervisor_notes = $notes ?? 'Hasil penghitungan suara telah diverifikasi dan diawasi oleh Pengawas Pemilihan.';

            if ($approval->admin_approved) {
                $approval->status = 'approved';
            }

            $approval->save();

            $this->auditLogService->log(
                action: 'approve_election_supervisor',
                eventType: 'approval',
                description: "Pengawas {$supervisor->username} menyetujui hasil pemilihan '{$election->title}' (ID #{$election->id})",
                metadata: [
                    'election_id' => $election->id,
                    'approval_id' => $approval->id,
                    'notes' => $notes,
                    'is_fully_approved' => $approval->isFullyApproved(),
                ],
                user: $supervisor
            );

            return $approval;
        });
    }

    /**
     * Publikasi Hasil Resmi Pemilihan (hanya dapat dilakukan setelah persetujuan dua pihak lengkap).
     */
    public function publishResults(Election $election, User $publisher): void
    {
        if (!$election->isClosed()) {
            throw new \RuntimeException('Pemilihan belum selesai/ditutup.');
        }

        $approval = $this->getOrCreateApproval($election);
        if (!$approval->isFullyApproved()) {
            throw new \RuntimeException('Hasil pemilihan belum dapat dipublikasikan. Memerlukan persetujuan dua pihak (Admin dan Pengawas) secara lengkap.');
        }

        DB::transaction(function () use ($election, $publisher, $approval) {
            $election->update([
                'status' => 'published',
                'is_published' => true,
                'published_at' => Carbon::now(),
                'published_by' => $publisher->id,
            ]);

            $this->auditLogService->log(
                action: 'publish_election_results',
                eventType: 'election',
                description: "Memublikasikan hasil resmi pemilihan '{$election->title}' (ID #{$election->id}) setelah disetujui dua pihak.",
                metadata: [
                    'election_id' => $election->id,
                    'admin_approver_id' => $approval->admin_user_id,
                    'supervisor_approver_id' => $approval->supervisor_user_id,
                    'published_by' => $publisher->id,
                ],
                user: $publisher
            );
        });
    }
}
