<?php

namespace App\Services;

use App\Models\Ballot;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\User;
use App\Models\VotingToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VotingService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Hasilkan token voting acak sekali pakai (Single-Use Nonce) untuk konfirmasi suara.
     */
    public function generateVotingToken(Election $election, User $user): string
    {
        if (!$election->isOpen()) {
            throw new \RuntimeException('Pemilihan tidak sedang dalam masa pemungutan suara aktif.');
        }

        // Cek status hak suara pemilih
        $eligibleVoter = ElectionEligibleVoter::where('election_id', $election->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->resident_id) {
                    $q->orWhere('resident_id', $user->resident_id);
                }
            })
            ->first();

        if (!$eligibleVoter) {
            throw new \RuntimeException('Anda tidak terdaftar sebagai pemilih pada pemilihan ini.');
        }

        if ($eligibleVoter->has_voted) {
            throw new \RuntimeException('Anda sudah menggunakan hak suara Anda pada pemilihan ini.');
        }

        // Batalkan token lama yang belum terpakai untuk user ini pada pemilihan ini
        VotingToken::where('election_id', $election->id)
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Buat token baru dengan masa berlaku 15 menit
        $plainToken = Str::random(48);
        $tokenHash = hash('sha256', $plainToken);

        VotingToken::create([
            'election_id' => $election->id,
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'expires_at' => Carbon::now()->addMinutes(15),
            'is_used' => false,
        ]);

        return $plainToken;
    }

    /**
     * Eksekusi pemberian suara secara aman, atomik, dan rahasia.
     * Menerapkan Row-Level Locking untuk mematikan peluang double-voting & race condition.
     */
    public function castVote(Election $election, User $user, int $candidateId, string $plainToken): string
    {
        return DB::transaction(function () use ($election, $user, $candidateId, $plainToken) {
            // 1. Validasi waktu & status pemilihan
            if (!$election->isOpen()) {
                throw new \RuntimeException('Pemilihan tidak aktif atau telah ditutup.');
            }

            // 2. Validasi kandidat
            $candidate = Candidate::where('id', $candidateId)
                ->where('election_id', $election->id)
                ->where('status', 'active')
                ->first();

            if (!$candidate) {
                throw new \RuntimeException('Kandidat tidak valid atau telah mengundurkan diri.');
            }

            // 3. Validasi Token Sekali Pakai (Single-Use Token Verification)
            $tokenHash = hash('sha256', $plainToken);
            $votingToken = VotingToken::where('election_id', $election->id)
                ->where('user_id', $user->id)
                ->where('token_hash', $tokenHash)
                ->lockForUpdate()
                ->first();

            if (!$votingToken || !$votingToken->isValid()) {
                throw new \RuntimeException('Token voting tidak valid, sudah digunakan, atau telah kedaluwarsa.');
            }

            // 4. KUNCI BARIS PEMILIH DENGAN PESSIMISTIC LOCK (Pencegah Double Voting & Concurrency)
            $voterRecord = ElectionEligibleVoter::where('election_id', $election->id)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if ($user->resident_id) {
                        $q->orWhere('resident_id', $user->resident_id);
                    }
                })
                ->lockForUpdate()
                ->first();

            if (!$voterRecord) {
                throw new \RuntimeException('Anda tidak terdaftar dalam Daftar Pemilih Tetap (DPT) pemilihan ini.');
            }

            if ($voterRecord->has_voted) {
                throw new \RuntimeException('Hak suara Anda telah digunakan sebelumnya. Pemilihan ganda tidak diperbolehkan.');
            }

            // 5. Tandai token sebagai telah terpakai
            $votingToken->update([
                'is_used' => true,
                'used_at' => Carbon::now(),
            ]);

            // 6. Buat kode bukti penerimaan anonim (Anonymous Receipt Hash)
            // Dihasilkan dari salt acak kriptografis sehingga TIDAK mengungkapkan pilihan kandidat
            // Format: RCP-XXXXXX-<50 hex chars> (Total 61 karakter, aman untuk kolom varchar(64) atau varchar(100))
            $receiptToken = 'RCP-' . strtoupper(Str::random(6)) . '-' . substr(hash('sha256', Str::random(32) . '|' . $election->id . '|' . microtime(true)), 0, 50);

            // 7. Tandai hak suara pemilih sebagai telah digunakan
            $voterRecord->update([
                'has_voted' => true,
                'voted_at' => Carbon::now(),
                'receipt_token' => $receiptToken,
                // Pastikan user_id terasosiasi jika sebelumnya null
                'user_id' => $user->id,
            ]);

            // 8. SIMPAN SURAT SUARA RAHASIA (SECRET BALLOT)
            // Surat suara disimpan ke tabel ballots TANPA user_id, TANPA resident_id, TANPA IP address, TANPA receipt_token
            $ballotSalt = Str::random(32);
            $ballotHash = Ballot::generateHash($election->id, $candidate->id, $ballotSalt);

            Ballot::create([
                'id' => (string) Str::uuid(),
                'election_id' => $election->id,
                'candidate_id' => $candidate->id,
                'cast_date' => Carbon::now()->toDateString(),
                'ballot_hash' => $ballotHash,
                'is_valid' => true,
            ]);

            // 9. Catat Audit Log (PENTING: JANGAN SIMPAN ID KANDIDAT YANG DIPILIH!)
            $this->auditLogService->log(
                action: 'cast_vote',
                eventType: 'voting',
                description: "Pemilih {$user->username} (User ID #{$user->id}) berhasil menggunakan hak suara pada Pemilihan '{$election->title}' (ID #{$election->id}).",
                metadata: [
                    'election_id' => $election->id,
                    'voter_user_id' => $user->id,
                    'receipt_prefix' => substr($receiptToken, 0, 10),
                ],
                user: $user
            );

            return $receiptToken;
        });
    }

    /**
     * Cek status apakah user sudah voting pada suatu pemilihan.
     */
    public function hasVoted(Election $election, User $user): bool
    {
        return ElectionEligibleVoter::where('election_id', $election->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->resident_id) {
                    $q->orWhere('resident_id', $user->resident_id);
                }
            })
            ->where('has_voted', true)
            ->exists();
    }
}
