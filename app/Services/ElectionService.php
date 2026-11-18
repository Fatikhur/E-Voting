<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ElectionService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Buat pemilihan baru.
     */
    public function createElection(array $data, User $creator): Election
    {
        return DB::transaction(function () use ($data, $creator) {
            $code = 'PEM-' . strtoupper($data['type'] === 'ketua_rw' ? 'RW' : 'RT') . '-' . Str::random(5) . '-' . date('Y');

            $election = Election::create([
                'code' => $code,
                'title' => $data['title'],
                'type' => $data['type'],
                'village_id' => $data['village_id'],
                'rw_id' => $data['rw_id'],
                'rt_id' => $data['type'] === 'ketua_rt' ? ($data['rt_id'] ?? null) : null,
                'term_period' => $data['term_period'],
                'start_time' => Carbon::parse($data['start_time']),
                'end_time' => Carbon::parse($data['end_time']),
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'tie_breaker_rule' => $data['tie_breaker_rule'] ?? 'Musyawarah mufakat panitia pengawas dan para kandidat atau putaran kedua.',
                'created_by' => $creator->id,
            ]);

            // Buat inisialisasi persetujuan dua pihak
            ElectionApproval::create([
                'election_id' => $election->id,
                'required_approvals' => 2,
                'status' => 'pending',
            ]);

            $this->auditLogService->log(
                action: 'create_election',
                eventType: 'election',
                description: "Membuat pemilihan baru '{$election->title}' (Kode: {$election->code})",
                metadata: [
                    'election_id' => $election->id,
                    'code' => $election->code,
                    'type' => $election->type,
                    'start_time' => $election->start_time->toIso8601String(),
                    'end_time' => $election->end_time->toIso8601String(),
                ],
                user: $creator
            );

            return $election;
        });
    }

    /**
     * Update data pemilihan dengan aturan ketat keamanan.
     */
    public function updateElection(Election $election, array $data, User $actor): Election
    {
        if (in_array($election->status, ['open', 'closed', 'published'], true)) {
            throw new \RuntimeException('Pemilihan yang sedang berlangsung atau sudah selesai tidak dapat diubah sembarangan.');
        }

        return DB::transaction(function () use ($election, $data, $actor) {
            $oldData = $election->only(['title', 'start_time', 'end_time', 'status', 'term_period']);

            $election->update([
                'title' => $data['title'] ?? $election->title,
                'term_period' => $data['term_period'] ?? $election->term_period,
                'start_time' => isset($data['start_time']) ? Carbon::parse($data['start_time']) : $election->start_time,
                'end_time' => isset($data['end_time']) ? Carbon::parse($data['end_time']) : $election->end_time,
                'description' => $data['description'] ?? $election->description,
                'tie_breaker_rule' => $data['tie_breaker_rule'] ?? $election->tie_breaker_rule,
            ]);

            $this->auditLogService->log(
                action: 'update_election',
                eventType: 'election',
                description: "Memperbarui konfigurasi pemilihan '{$election->title}' (ID #{$election->id})",
                metadata: [
                    'election_id' => $election->id,
                    'old' => $oldData,
                    'new' => $election->only(['title', 'start_time', 'end_time', 'status', 'term_period']),
                ],
                user: $actor
            );

            return $election;
        });
    }

    /**
     * Jadwalkan pemilihan (draft -> scheduled).
     */
    public function scheduleElection(Election $election, User $actor): void
    {
        if ($election->candidates()->count() < 1) {
            throw new \RuntimeException('Pemilihan belum memiliki kandidat. Daftarkan minimal satu kandidat.');
        }

        $election->update(['status' => 'scheduled']);

        $this->auditLogService->log(
            action: 'schedule_election',
            eventType: 'status_change',
            description: "Menjadwalkan pemilihan '{$election->title}' (ID #{$election->id})",
            metadata: ['election_id' => $election->id, 'status' => 'scheduled'],
            user: $actor
        );
    }

    /**
     * Buka pemungutan suara (scheduled/draft -> open).
     */
    public function openElection(Election $election, User $actor): void
    {
        if ($election->candidates()->where('status', 'active')->count() < 1) {
            throw new \RuntimeException('Pemilihan tidak memiliki kandidat aktif.');
        }

        $now = Carbon::now();
        if ($now->lt($election->start_time)) {
            // Jika dibuka manual sebelum start_time, sesuaikan start_time ke sekarang
            $election->start_time = $now;
        }

        $election->status = 'open';
        $election->save();

        $this->auditLogService->log(
            action: 'open_election',
            eventType: 'status_change',
            description: "Membuka waktu pemungutan suara untuk pemilihan '{$election->title}' (ID #{$election->id})",
            metadata: ['election_id' => $election->id, 'opened_at' => $now->toIso8601String()],
            user: $actor
        );
    }

    /**
     * Tutup pemungutan suara (open -> closed).
     */
    public function closeElection(Election $election, User $actor): void
    {
        if ($election->status !== 'open') {
            throw new \RuntimeException('Hanya pemilihan dengan status open yang dapat ditutup.');
        }

        $now = Carbon::now();
        $election->status = 'closed';
        if ($now->lt($election->end_time)) {
            $election->end_time = $now;
        }
        $election->save();

        $this->auditLogService->log(
            action: 'close_election',
            eventType: 'status_change',
            description: "Menutup pemungutan suara untuk pemilihan '{$election->title}' (ID #{$election->id})",
            metadata: [
                'election_id' => $election->id,
                'closed_at' => $now->toIso8601String(),
                'total_ballots_collected' => $election->ballots()->count(),
            ],
            user: $actor
        );
    }

    /**
     * Transisi otomatis status pemilihan berdasarkan jadwal waktu (Scheduler/Cron).
     */
    public function autoUpdateElectionStatuses(): array
    {
        $now = Carbon::now();
        $openedCount = 0;
        $closedCount = 0;

        // Buka pemilihan yang sudah mencapai jadwal mulai
        $scheduledElections = Election::where('status', 'scheduled')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>', $now)
            ->get();

        foreach ($scheduledElections as $election) {
            $election->update(['status' => 'open']);
            $openedCount++;

            $this->auditLogService->log(
                action: 'auto_open_election',
                eventType: 'schedule',
                description: "Pemilihan '{$election->title}' (ID #{$election->id}) otomatis dibuka sesuai jadwal.",
                metadata: ['election_id' => $election->id]
            );
        }

        // Tutup pemilihan yang sudah melampaui jadwal berakhir
        $expiredElections = Election::where('status', 'open')
            ->where('end_time', '<=', $now)
            ->get();

        foreach ($expiredElections as $election) {
            $election->update(['status' => 'closed']);
            $closedCount++;

            $this->auditLogService->log(
                action: 'auto_close_election',
                eventType: 'schedule',
                description: "Pemilihan '{$election->title}' (ID #{$election->id}) otomatis ditutup sesuai jadwal batas waktu.",
                metadata: ['election_id' => $election->id]
            );
        }

        return [
            'opened' => $openedCount,
            'closed' => $closedCount,
        ];
    }
}
