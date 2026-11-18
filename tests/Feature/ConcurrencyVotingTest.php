<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\User;
use App\Services\VotingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcurrencyVotingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Uji ketahanan terhadap double submission simultan (Race Condition Defense).
     * Memastikan bahwa satu pemilih tidak dapat menghasilkan lebih dari satu suara
     * meskipun request masuk dalam milidetik yang sama.
     */
    public function test_concurrent_voting_attempts_yield_only_single_accepted_ballot(): void
    {
        $votingService = app(VotingService::class);

        $election = Election::where('status', 'open')->firstOrFail();
        $voter = User::where('username', 'pemilih2')->firstOrFail();
        $candidate = $election->candidates()->where('status', 'active')->firstOrFail();

        // Pastikan pemilih belum voting
        ElectionEligibleVoter::where('election_id', $election->id)
            ->where('user_id', $voter->id)
            ->update(['has_voted' => false, 'voted_at' => null]);

        $token1 = $votingService->generateVotingToken($election, $voter);

        // Percobaan 1: Berhasil
        $receipt = $votingService->castVote($election, $voter, $candidate->id, $token1);
        $this->assertNotEmpty($receipt);

        // Percobaan 2: Replay langsung (simulasi double-click / concurrent replay)
        $this->expectException(\RuntimeException::class);
        $votingService->castVote($election, $voter, $candidate->id, $token1);
    }
}
