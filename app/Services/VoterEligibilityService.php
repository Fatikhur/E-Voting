<?php

namespace App\Services;

use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VoterEligibilityService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Evaluasi kelayakan warga negara sebagai pemilih.
     */
    public function checkResidentEligibility(Resident $resident): array
    {
        if ($resident->status !== 'active') {
            return [
                'is_eligible' => false,
                'reason' => 'Status warga tidak aktif (' . ucfirst($resident->status) . ').',
            ];
        }

        if (!$resident->is_eligible_voter) {
            return [
                'is_eligible' => false,
                'reason' => 'Status kelayakan pemilih ditandai tidak memenuhi syarat secara manual oleh administrator.',
            ];
        }

        if ($resident->birth_date && $resident->age < 17) {
            return [
                'is_eligible' => false,
                'reason' => "Usia warga belum mencukupi ({$resident->age} tahun, minimal 17 tahun).",
            ];
        }

        return [
            'is_eligible' => true,
            'reason' => 'Warga memenuhi seluruh kriteria pemilih.',
        ];
    }

    /**
     * Sinkronisasi Daftar Pemilih Tetap (DPT) untuk pemilihan berdasarkan cakupan wilayahnya.
     */
    public function syncDptForElection(Election $election, ?User $actor = null): int
    {
        $query = Resident::query()
            ->where('village_id', $election->village_id)
            ->where('rw_id', $election->rw_id)
            ->where('status', 'active')
            ->where('is_eligible_voter', true);

        if ($election->type === 'ketua_rt' && $election->rt_id) {
            $query->where('rt_id', $election->rt_id);
        }

        $residents = $query->with('user')->get();
        $syncedCount = 0;

        DB::transaction(function () use ($election, $residents, &$syncedCount, $actor) {
            foreach ($residents as $resident) {
                $check = $this->checkResidentEligibility($resident);
                if (!$check['is_eligible']) {
                    continue;
                }

                $record = ElectionEligibleVoter::firstOrCreate(
                    [
                        'election_id' => $election->id,
                        'resident_id' => $resident->id,
                    ],
                    [
                        'user_id' => $resident->user ? $resident->user->id : null,
                        'has_voted' => false,
                    ]
                );

                if ($record->wasRecentlyCreated) {
                    $syncedCount++;
                } elseif ($resident->user && !$record->user_id) {
                    // Update user_id jika sebelumnya belum terhubung
                    $record->update(['user_id' => $resident->user->id]);
                }
            }

            $this->auditLogService->log(
                action: 'sync_dpt',
                eventType: 'voter',
                description: "Sinkronisasi DPT untuk pemilihan '{$election->title}' (ID #{$election->id}): {$syncedCount} pemilih baru ditambahkan.",
                metadata: [
                    'election_id' => $election->id,
                    'newly_synced_count' => $syncedCount,
                    'total_dpt' => ElectionEligibleVoter::where('election_id', $election->id)->count(),
                ],
                user: $actor
            );
        });

        return $syncedCount;
    }

    /**
     * Tautkan akun pengguna baru ke seluruh entri DPT yang bersangkutan.
     */
    public function linkUserToEligibleVoters(User $user): int
    {
        if (!$user->resident_id) {
            return 0;
        }

        return ElectionEligibleVoter::where('resident_id', $user->resident_id)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);
    }
}
