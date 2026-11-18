<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Services\VotingService;
use Illuminate\View\View;

class VoterDashboardController extends Controller
{
    public function __construct(
        protected VotingService $votingService
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        // Cari pemilihan yang terkait dengan wilayah pemilih ini
        $query = Election::with(['candidates', 'rw', 'rt'])
            ->where(function ($q) use ($user) {
                // Pemilihan RW yang sesuai
                $q->where(function ($rwQ) use ($user) {
                    $rwQ->where('type', 'ketua_rw')
                        ->where('rw_id', $user->rw_id);
                })
                // Atau Pemilihan RT yang sesuai
                ->orWhere(function ($rtQ) use ($user) {
                    $rtQ->where('type', 'ketua_rt')
                        ->where('rw_id', $user->rw_id)
                        ->where('rt_id', $user->rt_id);
                });
            });

        $allElections = $query->orderBy('start_time', 'desc')->get();

        // Tambahkan metadata status hak suara untuk setiap pemilihan
        $electionsData = $allElections->map(function ($election) use ($user) {
            $voterRecord = ElectionEligibleVoter::where('election_id', $election->id)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if ($user->resident_id) {
                        $q->orWhere('resident_id', $user->resident_id);
                    }
                })
                ->first();

            $hasVoted = $voterRecord ? $voterRecord->has_voted : false;
            $isEligible = (bool) $voterRecord;

            return [
                'election' => $election,
                'is_eligible' => $isEligible,
                'has_voted' => $hasVoted,
                'receipt_token' => $voterRecord?->receipt_token,
                'voted_at' => $voterRecord?->voted_at,
                'can_vote' => $election->isOpen() && $isEligible && !$hasVoted,
            ];
        });

        return view('voter.dashboard', compact('user', 'electionsData'));
    }
}
