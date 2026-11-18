<?php

namespace App\Services;

use App\Models\Ballot;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;

class VoteCountingService
{
    /**
     * Hitung hasil perolehan suara pemilihan secara menyeluruh dan independen.
     */
    public function getElectionResults(Election $election): array
    {
        // 1. Data DPT & Partisipasi
        $totalEligibleVoters = ElectionEligibleVoter::where('election_id', $election->id)->count();
        $totalVotedVoters = ElectionEligibleVoter::where('election_id', $election->id)->where('has_voted', true)->count();
        $totalUnvotedVoters = max(0, $totalEligibleVoters - $totalVotedVoters);
        $turnoutPercentage = $totalEligibleVoters > 0
            ? round(($totalVotedVoters / $totalEligibleVoters) * 100, 2)
            : 0;

        // 2. Data Kotak Suara (Ballots)
        $totalValidBallots = Ballot::where('election_id', $election->id)->where('is_valid', true)->count();
        $totalInvalidBallots = Ballot::where('election_id', $election->id)->where('is_valid', false)->count();
        $totalBallots = $totalValidBallots + $totalInvalidBallots;

        // 3. Verifikasi Integritas Independen:
        // Jumlah pemilih yang ditandai has_voted harus sama dengan total surat suara yang diterima
        $isCountConsistent = ($totalVotedVoters === $totalValidBallots);

        // 4. Perolehan Suara per Kandidat
        $candidates = Candidate::where('election_id', $election->id)
            ->orderBy('candidate_number', 'asc')
            ->get();

        $candidateResults = [];
        $maxVotes = -1;
        $candidatesWithMaxVotes = [];

        foreach ($candidates as $candidate) {
            $votesCount = Ballot::where('election_id', $election->id)
                ->where('candidate_id', $candidate->id)
                ->where('is_valid', true)
                ->count();

            $percentage = $totalValidBallots > 0
                ? round(($votesCount / $totalValidBallots) * 100, 2)
                : 0;

            $candidateResults[] = [
                'id' => $candidate->id,
                'candidate_number' => $candidate->candidate_number,
                'name' => $candidate->name,
                'photo_url' => $candidate->photo_url,
                'status' => $candidate->status,
                'votes_count' => $votesCount,
                'percentage' => $percentage,
            ];

            if ($votesCount > $maxVotes) {
                $maxVotes = $votesCount;
                $candidatesWithMaxVotes = [$candidate];
            } elseif ($votesCount === $maxVotes && $maxVotes > 0) {
                $candidatesWithMaxVotes[] = $candidate;
            }
        }

        // 5. Penentuan Pemenang atau Status Suara Seri (Tie Vote)
        $isTie = count($candidatesWithMaxVotes) > 1;
        $winner = null;

        if (!$isTie && count($candidatesWithMaxVotes) === 1 && $maxVotes > 0) {
            $winnerCandidate = $candidatesWithMaxVotes[0];
            $winner = [
                'id' => $winnerCandidate->id,
                'candidate_number' => $winnerCandidate->candidate_number,
                'name' => $winnerCandidate->name,
                'votes' => $maxVotes,
                'percentage' => $totalValidBallots > 0 ? round(($maxVotes / $totalValidBallots) * 100, 2) : 0,
            ];
        }

        return [
            'election' => [
                'id' => $election->id,
                'code' => $election->code,
                'title' => $election->title,
                'type' => $election->type,
                'status' => $election->status,
                'is_published' => $election->is_published,
                'tie_breaker_rule' => $election->tie_breaker_rule,
            ],
            'statistics' => [
                'total_eligible_voters' => $totalEligibleVoters,
                'total_voted' => $totalVotedVoters,
                'total_unvoted' => $totalUnvotedVoters,
                'turnout_percentage' => $turnoutPercentage,
                'total_valid_ballots' => $totalValidBallots,
                'total_invalid_ballots' => $totalInvalidBallots,
                'total_ballots' => $totalBallots,
                'is_count_consistent' => $isCountConsistent,
            ],
            'candidates' => $candidateResults,
            'is_tie' => $isTie,
            'tie_candidates' => $isTie ? array_map(fn($c) => [
                'id' => $c->id,
                'candidate_number' => $c->candidate_number,
                'name' => $c->name,
                'votes' => $maxVotes,
            ], $candidatesWithMaxVotes) : [],
            'winner' => $winner,
        ];
    }
}
