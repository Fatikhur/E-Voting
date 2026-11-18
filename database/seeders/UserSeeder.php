<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\Role;
use App\Models\Rw;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superadminRole = Role::where('name', 'superadmin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $pengawasRole = Role::where('name', 'pengawas')->first();
        $voterRole = Role::where('name', 'voter')->first();

        $village = Village::first();
        $rw = Rw::first();

        // 1. Akun Super Administrator
        $superAdminUser = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator Sistem',
                'email' => 'superadmin@evoting.local',
                'password' => Hash::make('SuperAdmin123!'),
                'role_name' => 'superadmin',
                'role_id' => $superadminRole?->id,
                'status' => 'active',
                'phone' => '081100000001',
            ]
        );
        if ($superadminRole) {
            $superAdminUser->roles()->syncWithoutDetaching([$superadminRole->id]);
        }

        // 2. Akun Administrator Panitia Pemilihan
        $adminUser = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Panitia Pemilihan RW 01',
                'email' => 'admin@evoting.local',
                'password' => Hash::make('Admin123!'),
                'role_name' => 'admin',
                'role_id' => $adminRole?->id,
                'village_id' => $village?->id,
                'rw_id' => $rw?->id,
                'status' => 'active',
                'phone' => '081100000002',
            ]
        );
        if ($adminRole) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // 3. Akun Pengawas Independen
        $pengawasUser = User::firstOrCreate(
            ['username' => 'pengawas'],
            [
                'name' => 'Pengawas Independen RW 01',
                'email' => 'pengawas@evoting.local',
                'password' => Hash::make('Pengawas123!'),
                'role_name' => 'pengawas',
                'role_id' => $pengawasRole?->id,
                'village_id' => $village?->id,
                'rw_id' => $rw?->id,
                'status' => 'active',
                'phone' => '081100000003',
            ]
        );
        if ($pengawasRole) {
            $pengawasUser->roles()->syncWithoutDetaching([$pengawasRole->id]);
        }

        // 4. Akun Pemilih Utama: 'pemilih'
        $resident1 = Resident::where('name', 'like', '%Fatikhur Rizki%')->first();
        $voter1 = User::firstOrCreate(
            ['username' => 'pemilih'],
            [
                'name' => 'Fatikhur Rizki (Pemilih 1)',
                'email' => 'pemilih@evoting.local',
                'password' => Hash::make('Pemilih123!'),
                'role_name' => 'voter',
                'role_id' => $voterRole?->id,
                'resident_id' => $resident1?->id,
                'village_id' => $resident1?->village_id,
                'rw_id' => $resident1?->rw_id,
                'rt_id' => $resident1?->rt_id,
                'status' => 'active',
                'phone' => '081234567804',
            ]
        );
        if ($voterRole) {
            $voter1->roles()->syncWithoutDetaching([$voterRole->id]);
        }

        // 5. Akun Pemilih Tambahan: pemilih2 s/d pemilih5
        $extraVoters = [
            ['username' => 'pemilih2', 'res_name' => 'Dewi Lestari'],
            ['username' => 'pemilih3', 'res_name' => 'Eko Prasetyo'],
            ['username' => 'pemilih4', 'res_name' => 'Rina Kartika'],
            ['username' => 'pemilih5', 'res_name' => 'Hendra Gunawan'],
        ];

        foreach ($extraVoters as $ev) {
            $res = Resident::where('name', 'like', "%{$ev['res_name']}%")->first();
            $user = User::firstOrCreate(
                ['username' => $ev['username']],
                [
                    'name' => $res ? $res->name : ucfirst($ev['username']),
                    'email' => "{$ev['username']}@evoting.local",
                    'password' => Hash::make('Pemilih123!'),
                    'role_name' => 'voter',
                    'role_id' => $voterRole?->id,
                    'resident_id' => $res?->id,
                    'village_id' => $res?->village_id,
                    'rw_id' => $res?->rw_id,
                    'rt_id' => $res?->rt_id,
                    'status' => 'active',
                    'phone' => $res?->phone,
                ]
            );
            if ($voterRole) {
                $user->roles()->syncWithoutDetaching([$voterRole->id]);
            }
        }
    }
}
