<?php

namespace Tests\Feature;

use App\Models\Ballot;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use App\Models\Village;
use App\Services\VotingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VotingSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Village $village;
    protected Rw $rw;
    protected Rt $rt;
    protected Election $election;
    protected Candidate $candidate1;
    protected Candidate $candidate2;
    protected User $voterUser;
    protected Resident $resident;
    protected ElectionEligibleVoter $dptRecord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->village = Village::create(['code' => '3201', 'name' => 'Kelurahan Sukamaju', 'district' => 'Cilodong', 'regency' => 'Depok']);
        $this->rw = Rw::create(['village_id' => $this->village->id, 'rw_number' => '01']);
        $this->rt = Rt::create(['rw_id' => $this->rw->id, 'rt_number' => '01']);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin Panitia',
            'username' => 'admin_panitia',
            'password' => Hash::make('Password123!'),
            'role_name' => 'admin',
            'status' => 'active',
        ]);

        // Create Active Election (Status: Open)
        $this->election = Election::create([
            'code' => 'PEM-RW01-TEST',
            'title' => 'Pemilihan Uji Coba RW 01',
            'type' => 'ketua_rw',
            'village_id' => $this->village->id,
            'rw_id' => $this->rw->id,
            'term_period' => '2026 - 2031',
            'start_time' => Carbon::now()->subHours(1),
            'end_time' => Carbon::now()->addHours(5),
            'status' => 'open',
            'created_by' => $admin->id,
        ]);

        // Candidates
        $this->candidate1 = Candidate::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'name' => 'Kandidat Nomor Satu',
            'vision' => 'Visi 1',
            'mission' => 'Misi 1',
            'status' => 'active',
        ]);

        $this->candidate2 = Candidate::create([
            'election_id' => $this->election->id,
            'candidate_number' => 2,
            'name' => 'Kandidat Nomor Dua',
            'vision' => 'Visi 2',
            'mission' => 'Misi 2',
            'status' => 'active',
        ]);

        // Resident & Voter
        $plainNik = '3276010101900001';
        $this->resident = Resident::create([
            'nik_hash' => Resident::hashNik($plainNik),
            'nik_encrypted' => Crypt::encryptString($plainNik),
            'nik_masked' => Resident::maskNik($plainNik),
            'name' => 'Pemilih Sah',
            'gender' => 'L',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Test No 1',
            'village_id' => $this->village->id,
            'rw_id' => $this->rw->id,
            'rt_id' => $this->rt->id,
            'status' => 'active',
            'is_eligible_voter' => true,
        ]);

        $this->voterUser = User::create([
            'name' => 'Pemilih Sah',
            'username' => 'pemilih_sah',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'resident_id' => $this->resident->id,
            'village_id' => $this->village->id,
            'rw_id' => $this->rw->id,
            'rt_id' => $this->rt->id,
            'status' => 'active',
        ]);

        // DPT entry
        $this->dptRecord = ElectionEligibleVoter::create([
            'election_id' => $this->election->id,
            'resident_id' => $this->resident->id,
            'user_id' => $this->voterUser->id,
            'has_voted' => false,
        ]);
    }

    public function test_eligible_voter_can_cast_vote_successfully(): void
    {
        $votingService = app(VotingService::class);

        // Generate token
        $token = $votingService->generateVotingToken($this->election, $this->voterUser);
        $this->assertNotEmpty($token);

        // Cast vote
        $response = $this->actingAs($this->voterUser)->post("/voting/{$this->election->id}/cast", [
            'candidate_id' => $this->candidate1->id,
            'token' => $token,
        ]);

        $response->assertRedirect(route('voting.receipt', $this->election));

        // 1. DPT Record marked as voted
        $this->dptRecord->refresh();
        $this->assertTrue($this->dptRecord->has_voted);
        $this->assertNotNull($this->dptRecord->voted_at);
        $this->assertNotNull($this->dptRecord->receipt_token);

        // 2. Secret Ballot inserted anonymously
        $this->assertDatabaseCount('ballots', 1);
        $ballot = Ballot::first();
        $this->assertEquals($this->election->id, $ballot->election_id);
        $this->assertEquals($this->candidate1->id, $ballot->candidate_id);
        $this->assertTrue($ballot->is_valid);
    }

    public function test_voter_cannot_vote_twice_anti_double_voting(): void
    {
        $votingService = app(VotingService::class);

        // First vote
        $token1 = $votingService->generateVotingToken($this->election, $this->voterUser);
        $votingService->castVote($this->election, $this->voterUser, $this->candidate1->id, $token1);

        $this->dptRecord->refresh();
        $this->assertTrue($this->dptRecord->has_voted);

        // Second vote attempt via service throws exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Anda sudah menggunakan hak suara Anda');
        $votingService->generateVotingToken($this->election, $this->voterUser);
    }

    public function test_reused_token_is_rejected(): void
    {
        $votingService = app(VotingService::class);
        $token = $votingService->generateVotingToken($this->election, $this->voterUser);

        // Use token once
        $votingService->castVote($this->election, $this->voterUser, $this->candidate1->id, $token);

        // Attempting to reuse same token
        $this->expectException(\RuntimeException::class);
        $votingService->castVote($this->election, $this->voterUser, $this->candidate2->id, $token);
    }

    public function test_voter_cannot_vote_when_election_is_not_open(): void
    {
        // Set election to closed
        $this->election->update(['status' => 'closed']);

        $votingService = app(VotingService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Pemilihan tidak sedang dalam masa pemungutan suara aktif.');

        $votingService->generateVotingToken($this->election, $this->voterUser);
    }

    public function test_cannot_vote_for_candidate_of_different_election(): void
    {
        // Another election
        $otherElection = Election::create([
            'code' => 'PEM-OTHER',
            'title' => 'Pemilihan Lain',
            'type' => 'ketua_rw',
            'village_id' => $this->village->id,
            'rw_id' => $this->rw->id,
            'term_period' => '2026 - 2031',
            'start_time' => Carbon::now()->subHours(1),
            'end_time' => Carbon::now()->addHours(5),
            'status' => 'open',
            'created_by' => $this->voterUser->id,
        ]);

        $otherCandidate = Candidate::create([
            'election_id' => $otherElection->id,
            'candidate_number' => 1,
            'name' => 'Kandidat Luar',
            'vision' => 'Visi Luar',
            'mission' => 'Misi Luar',
            'status' => 'active',
        ]);

        $votingService = app(VotingService::class);
        $token = $votingService->generateVotingToken($this->election, $this->voterUser);

        // Trying to cast vote on this election but with otherCandidate->id
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Kandidat tidak valid');

        $votingService->castVote($this->election, $this->voterUser, $otherCandidate->id, $token);
    }

    public function test_ballot_table_does_not_contain_voter_identity(): void
    {
        $votingService = app(VotingService::class);
        $token = $votingService->generateVotingToken($this->election, $this->voterUser);
        $votingService->castVote($this->election, $this->voterUser, $this->candidate1->id, $token);

        $ballot = Ballot::first();

        // Architectural Assertion: Ballot model has no user_id or resident_id
        $this->assertArrayNotHasKey('user_id', $ballot->toArray());
        $this->assertArrayNotHasKey('resident_id', $ballot->toArray());
        $this->assertArrayNotHasKey('voter_id', $ballot->toArray());
        $this->assertArrayNotHasKey('ip_address', $ballot->toArray());
    }
}
