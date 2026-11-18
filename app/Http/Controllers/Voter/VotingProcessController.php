<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Services\VotingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VotingProcessController extends Controller
{
    public function __construct(
        protected VotingService $votingService
    ) {}

    /**
     * Langkah 1: Tampilkan kartu daftar kandidat pemilihan aktif.
     */
    public function showCandidates(Election $election): View|RedirectResponse
    {
        $user = auth()->user();

        // 1. Cek apakah pemilih sudah memilih
        if ($this->votingService->hasVoted($election, $user)) {
            return redirect()->route('voting.receipt', $election)
                ->with('info', 'Anda sudah menggunakan hak suara Anda untuk pemilihan ini.');
        }

        // 2. Cek apakah pemilihan sedang dibuka
        if (!$election->isOpen()) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'Waktu pemungutan suara untuk pemilihan ini belum dibuka atau telah berakhir.');
        }

        $candidates = $election->candidates()->where('status', 'active')->orderBy('candidate_number', 'asc')->get();

        return view('voter.candidates', compact('election', 'candidates', 'user'));
    }

    /**
     * Langkah 2: Halaman konfirmasi pilihan kandidat (Anti-Salah Klik & Pengambilan Token).
     */
    public function confirmChoice(Request $request, Election $election): View|RedirectResponse
    {
        $user = auth()->user();

        if ($this->votingService->hasVoted($election, $user)) {
            return redirect()->route('voting.receipt', $election);
        }

        if (!$election->isOpen()) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'Pemilihan tidak sedang aktif.');
        }

        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $candidate = Candidate::where('id', $request->candidate_id)
            ->where('election_id', $election->id)
            ->where('status', 'active')
            ->firstOrFail();

        try {
            // Hasilkan single-use voting token nonce
            $token = $this->votingService->generateVotingToken($election, $user);

            return view('voter.confirm', compact('election', 'candidate', 'token', 'user'));
        } catch (\Exception $e) {
            return redirect()->route('voter.dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Langkah 3: Eksekusi penyimpanan suara secara atomik dan rahasia.
     */
    public function cast(Request $request, Election $election): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'token' => 'required|string',
        ]);

        try {
            $receiptToken = $this->votingService->castVote(
                $election,
                $user,
                (int) $request->candidate_id,
                $request->token
            );

            session()->flash('new_vote_receipt', $receiptToken);

            return redirect()->route('voting.receipt', $election)
                ->with('success', 'Suara Anda telah berhasil direkam secara aman, rahasia, dan permanen ke dalam kotak suara!');
        } catch (\Exception $e) {
            return redirect()->route('voter.dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Langkah 4: Tampilan bukti penerimaan suara anonim (Anonymous Receipt Proof).
     */
    public function receipt(Election $election): View|RedirectResponse
    {
        $user = auth()->user();

        $voterRecord = ElectionEligibleVoter::where('election_id', $election->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->resident_id) {
                    $q->orWhere('resident_id', $user->resident_id);
                }
            })
            ->first();

        if (!$voterRecord || !$voterRecord->has_voted) {
            return redirect()->route('voter.dashboard')
                ->with('error', 'Anda belum menggunakan hak suara pada pemilihan ini.');
        }

        $receiptToken = session('new_vote_receipt') ?? $voterRecord->receipt_token;

        return view('voter.receipt', compact('election', 'voterRecord', 'receiptToken', 'user'));
    }
}
