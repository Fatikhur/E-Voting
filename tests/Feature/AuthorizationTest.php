<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_voter_cannot_access_superadmin_dashboard(): void
    {
        $voter = User::create([
            'name' => 'Pemilih Biasa',
            'username' => 'pemilih_biasa',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'status' => 'active',
        ]);

        $response = $this->actingAs($voter)->get('/superadmin/dashboard');
        $response->assertStatus(403);
    }

    public function test_voter_cannot_access_admin_dashboard(): void
    {
        $voter = User::create([
            'name' => 'Pemilih Biasa',
            'username' => 'pemilih_biasa2',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'status' => 'active',
        ]);

        $response = $this->actingAs($voter)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_superadmin_user_management(): void
    {
        $admin = User::create([
            'name' => 'Admin RW',
            'username' => 'admin_rw',
            'password' => Hash::make('Password123!'),
            'role_name' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/superadmin/users');
        $response->assertStatus(403);
    }

    public function test_pengawas_cannot_access_admin_resident_creation(): void
    {
        $pengawas = User::create([
            'name' => 'Pengawas RW',
            'username' => 'pengawas_rw',
            'password' => Hash::make('Password123!'),
            'role_name' => 'pengawas',
            'status' => 'active',
        ]);

        $response = $this->actingAs($pengawas)->get('/admin/residents/create');
        $response->assertStatus(403);
    }

    public function test_superadmin_can_access_superadmin_dashboard(): void
    {
        $superadmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin_test',
            'password' => Hash::make('Password123!'),
            'role_name' => 'superadmin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($superadmin)->get('/superadmin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Super Administrator');
    }
}
