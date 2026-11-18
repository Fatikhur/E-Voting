<?php

namespace Tests\Feature;

use App\Models\Ballot;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionApproval;
use App\Models\User;
use App\Models\Village;
use App\Models\Rw;
use App\Services\ElectionApprovalService;
use App\Services\VoteCountingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ElectionApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected Election $election;
    protected User $admin;
    protected User $supervisor;
    protected Candidate $candidate1;
    protected Candidate $candidate2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $village = Village::create(['code' => '3201', 'name' => 'Kelurahan Sukamaju', 'district' => 'Cilodong', 'regency' => 'Depok']);
        $rw = Rw::create(['village_id' => $village->id, 'rw_number' => '01']);

        $this->admin = User::create([
            'name' => 'Admin Panitia',
            'username' => 'admin_uji',
            'password' => Hash::make('Password123!'),
            'role_name' => 'admin',
            'status' => 'active',
        ]);

        $this->supervisor = User::create([
            'name' => 'Pengawas Independen',
            'username' => 'pengawas_uji',
            'password' => Hash::make('Password123!'),
            'role_name' => 'pengawas',
            'status' => 'active',
        ]);

        $this->election = Election::create([
            'code' => 'PEM-RW01-APPR',
            'title' => 'Pemilihan Uji Persetujuan',
            'type' => 'ketua_rw',
            'village_id' => $village->id,
            'rw_id' => $rw->id,
            'term_period' => '2026 - 2031',
            'start_time' => Carbon::now()->subDays(2),
            'end_time' => Carbon::now()->subDays(1),
            'status' => 'closed',
            'tie_breaker_rule' => 'Musyawarah mufakat.',
            'created_by' => $this->admin->id,
        ]);

        $this->candidate1 = Candidate::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'name' => 'Calon A',
            'vision' => 'Visi A',
            'mission' => 'Misi A',
            'status' => 'active',
        ]);

        $this->candidate2 = Candidate::create([
            'election_id' => $this->election->id,
            'candidate_number' => 2,
            'name' => 'Calon B',
            'vision' => 'Visi B',
            'mission' => 'Misi B',
            'status' => 'active',
        ]);

        ElectionApproval::create([
            'election_id' => $this->election->id,
            'required_approvals' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_two_person_rule_requires_both_admin_and_supervisor_to_publish(): void
    {
        $service = app(ElectionApprovalService::class);

        // 1. Publikasi ditolak jika belum disetujui siapa pun
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('belum dapat dipublikasikan. Memerlukan persetujuan dua pihak');
        $service->publishResults($this->election, $this->admin);
    }

    public function test_publication_succeeds_only_after_both_parties_approve(): void
    {
        $service = app(ElectionApprovalService::class);

        // Admin approve
        $service->approveByAdmin($this->election, $this->admin, 'Verifikasi panitia oke');

        $approval = ElectionApproval::where('election_id', $this->election->id)->first();
        $this->assertTrue($approval->admin_approved);
        $this->assertFalse($approval->supervisor_approved);
        $this->assertFalse($approval->isFullyApproved());

        // Supervisor approve
        $service->approveBySupervisor($this->election, $this->supervisor, 'Verifikasi pengawas oke');

        $approval->refresh();
        $this->assertTrue($approval->supervisor_approved);
        $this->assertTrue($approval->isFullyApproved());

        // Sekarang publikasi berhasil
        $service->publishResults($this->election, $this->admin);

        $this->election->refresh();
        $this->assertEquals('published', $this->election->status);
        $this->assertTrue($this->election->is_published);
        $this->assertNotNull($this->election->published_at);
    }

    public function test_tie_vote_is_detected_and_handled(): void
    {
        // 2 surat suara untuk Calon A
        Ballot::create([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_id' => $this->candidate1->id,
            'cast_date' => Carbon::now()->toDateString(),
            'ballot_hash' => Ballot::generateHash($this->election->id, $this->candidate1->id, 'salt1'),
            'is_valid' => true,
        ]);
        Ballot::create([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_id' => $this->candidate1->id,
            'cast_date' => Carbon::now()->toDateString(),
            'ballot_hash' => Ballot::generateHash($this->election->id, $this->candidate1->id, 'salt2'),
            'is_valid' => true,
        ]);

        // 2 surat suara untuk Calon B
        Ballot::create([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_id' => $this->candidate2->id,
            'cast_date' => Carbon::now()->toDateString(),
            'ballot_hash' => Ballot::generateHash($this->election->id, $this->candidate2->id, 'salt3'),
            'is_valid' => true,
        ]);
        Ballot::create([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_id' => $this->candidate2->id,
            'cast_date' => Carbon::now()->toDateString(),
            'ballot_hash' => Ballot::generateHash($this->election->id, $this->candidate2->id, 'salt4'),
            'is_valid' => true,
        ]);

        $countingService = app(VoteCountingService::class);
        $results = $countingService->getElectionResults($this->election);

        $this->assertTrue($results['is_tie']);
        $this->assertNull($results['winner']);
        $this->assertCount(2, $results['tie_candidates']);
    }
}
