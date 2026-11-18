<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditTrailIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_audit_logs_form_a_cryptographically_valid_chain(): void
    {
        $service = app(AuditLogService::class);

        $user = User::create([
            'name' => 'Aktor Uji',
            'username' => 'aktor_uji',
            'password' => Hash::make('Password123!'),
            'role_name' => 'admin',
            'status' => 'active',
        ]);

        $service->log('action_1', 'security', 'Deskripsi log pertama', ['param' => 1], $user);
        $service->log('action_2', 'resident', 'Deskripsi log kedua', ['param' => 2], $user);
        $service->log('action_3', 'election', 'Deskripsi log ketiga', ['param' => 3], $user);

        $result = $service->verifyChainIntegrity();

        $this->assertTrue($result['is_valid']);
        $this->assertEquals(3, $result['total_verified']);
    }

    public function test_tampered_log_content_is_instantly_detected(): void
    {
        $service = app(AuditLogService::class);

        $user = User::create([
            'name' => 'Aktor Uji 2',
            'username' => 'aktor_uji2',
            'password' => Hash::make('Password123!'),
            'role_name' => 'admin',
            'status' => 'active',
        ]);

        $service->log('step_1', 'auth', 'Log asli langkah 1', null, $user);
        $log2 = $service->log('step_2', 'auth', 'Log asli langkah 2', null, $user);
        $service->log('step_3', 'auth', 'Log asli langkah 3', null, $user);

        // Simulasi Manipulasi Ilegal Database: penyerang mengubah isi deskripsi baris log ke-2 secara langsung
        DB::table('audit_logs')
            ->where('id', $log2->id)
            ->update(['description' => 'Log ini telah dimanipulasi oleh penyerang!']);

        // Verifikasi integritas harus mendeteksi manipulasi
        $result = $service->verifyChainIntegrity();

        $this->assertFalse($result['is_valid']);
        $this->assertEquals($log2->id, $result['corrupted_id']);
        $this->assertEquals('DATA_TAMPERED', $result['error_type']);
        $this->assertArrayHasKey('total_verified', $result);
        $this->assertArrayHasKey('latest_hash', $result);
        $this->assertEquals(1, $result['total_verified']);
    }

    public function test_audit_log_canonicalizes_metadata_with_arbitrary_key_order(): void
    {
        $service = app(AuditLogService::class);

        $user = User::create([
            'name' => 'Aktor Uji 3',
            'username' => 'aktor_uji3',
            'password' => Hash::make('Password123!'),
            'role_name' => 'admin',
            'status' => 'active',
        ]);

        // Key order deliberately randomized/non-alphabetical
        $service->log('action_multi', 'voter', 'Log multi keys', [
            'total_dpt' => 10,
            'election_id' => 1,
            'newly_synced_count' => 10,
        ], $user);

        $result = $service->verifyChainIntegrity();
        $this->assertTrue($result['is_valid']);
        $this->assertEquals(1, $result['total_verified']);
    }

    public function test_superadmin_dashboard_renders_successfully(): void
    {
        $superadmin = User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin_view',
            'password' => Hash::make('Password123!'),
            'role_name' => 'superadmin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($superadmin)->get(route('superadmin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Super Administrator');
        $response->assertSee('Status Integritas Audit Trail');
    }

    public function test_superadmin_dashboard_renders_gracefully_even_when_chain_is_anomalous(): void
    {
        $service = app(AuditLogService::class);

        $superadmin = User::create([
            'name' => 'Super Administrator 2',
            'username' => 'superadmin_view2',
            'password' => Hash::make('Password123!'),
            'role_name' => 'superadmin',
            'status' => 'active',
        ]);

        $log = $service->log('step_init', 'auth', 'Log inisial', null, $superadmin);

        // Manipulate DB directly to simulate corruption
        DB::table('audit_logs')->where('id', $log->id)->update(['current_hash' => 'invalid_hash_value']);

        $response = $this->actingAs($superadmin)->get(route('superadmin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('PERINGATAN: ANOMALI TERDETEKSI');
    }

    public function test_checkpoint_can_be_signed_and_created(): void
    {
        $service = app(AuditLogService::class);

        $admin = User::create([
            'name' => 'Super Admin CP',
            'username' => 'superadmin_cp',
            'password' => Hash::make('Password123!'),
            'role_name' => 'superadmin',
            'status' => 'active',
        ]);

        $service->log('init', 'system', 'Inisialisasi sistem', null, $admin);

        $checkpoint = $service->createCheckpoint(null, 'Checkpoint Uji Integritas Rutin', $admin);

        $this->assertNotNull($checkpoint->id);
        $this->assertEquals(1, $checkpoint->checkpoint_sequence);
        $this->assertNotEmpty($checkpoint->checkpoint_hash);
    }
}
