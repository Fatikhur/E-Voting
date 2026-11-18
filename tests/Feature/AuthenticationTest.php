<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Warga Tester',
            'username' => 'warga_test',
            'email' => 'warga@test.com',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'login' => 'warga_test',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_login_using_16_digit_nik(): void
    {
        $village = Village::create(['code' => '3201', 'name' => 'Desa Test', 'district' => 'Kec', 'regency' => 'Kab']);
        $rw = Rw::create(['village_id' => $village->id, 'rw_number' => '01']);
        $rt = Rt::create(['rw_id' => $rw->id, 'rt_number' => '01']);

        $plainNik = '3276010101900001';
        $resident = Resident::create([
            'nik_hash' => Resident::hashNik($plainNik),
            'nik_encrypted' => Crypt::encryptString($plainNik),
            'nik_masked' => Resident::maskNik($plainNik),
            'name' => 'Warga NIK Tester',
            'gender' => 'L',
            'address' => 'Jl. Test 1',
            'village_id' => $village->id,
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'status' => 'active',
            'is_eligible_voter' => true,
        ]);

        $user = User::create([
            'name' => 'Warga NIK Tester',
            'username' => 'warga_nik',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'resident_id' => $resident->id,
            'status' => 'active',
        ]);

        // Login menggunakan 16 digit NIK
        $response = $this->post('/login', [
            'login' => $plainNik,
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::create([
            'name' => 'Warga Salah Sandi',
            'username' => 'warga_salah',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'login' => 'warga_salah',
            'password' => 'WrongPassword!',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('error');
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'name' => 'Warga Nonaktif',
            'username' => 'warga_nonaktif',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'status' => 'inactive',
        ]);

        $response = $this->post('/login', [
            'login' => 'warga_nonaktif',
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('error');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'User Logout',
            'username' => 'user_logout',
            'password' => Hash::make('Password123!'),
            'role_name' => 'voter',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
