<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $roles = [
            'superadmin' => [
                'display_name' => 'Super Administrator',
                'description' => 'Akses penuh ke seluruh konfigurasi sistem, audit, backup, dan manajemen pengguna tingkat tinggi.',
            ],
            'admin' => [
                'display_name' => 'Administrator Panitia RT/RW',
                'description' => 'Mengelola data warga, DPT, pembuatan pemilihan, kandidat, dan verifikasi hasil pemilihan.',
            ],
            'pengawas' => [
                'display_name' => 'Pengawas Independen',
                'description' => 'Mengawasi jalannya proses pemilihan, memantau partisipasi secara real-time, memeriksa audit trail, dan memberi persetujuan hasil.',
            ],
            'voter' => [
                'display_name' => 'Pemilih / Warga',
                'description' => 'Melihat profil kandidat, menggunakan hak suara sekali untuk pemilihan yang diikuti, dan melihat bukti penerimaan suara.',
            ],
        ];

        $roleModels = [];
        foreach ($roles as $name => $data) {
            $roleModels[$name] = Role::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $data['display_name'],
                    'description' => $data['description'],
                ]
            );
        }

        // 2. Buat Permissions
        $permissions = [
            // Sistem & Konfigurasi
            ['name' => 'manage_system', 'display_name' => 'Kelola Sistem & Backup', 'category' => 'system'],
            ['name' => 'manage_users', 'display_name' => 'Kelola Akun Pengguna', 'category' => 'users'],
            ['name' => 'manage_territories', 'display_name' => 'Kelola Wilayah RT/RW', 'category' => 'territory'],
            
            // Warga & DPT
            ['name' => 'manage_residents', 'display_name' => 'Kelola Data Warga', 'category' => 'residents'],
            ['name' => 'import_residents', 'display_name' => 'Import CSV Data Warga', 'category' => 'residents'],
            ['name' => 'export_residents', 'display_name' => 'Export Data Warga', 'category' => 'residents'],
            ['name' => 'manage_dpt', 'display_name' => 'Kelola & Sinkron DPT', 'category' => 'voters'],
            
            // Pemilihan & Kandidat
            ['name' => 'manage_elections', 'display_name' => 'Kelola Pemilihan', 'category' => 'elections'],
            ['name' => 'manage_candidates', 'display_name' => 'Kelola Kandidat', 'category' => 'candidates'],
            
            // Voting
            ['name' => 'cast_vote', 'display_name' => 'Gunakan Hak Suara', 'category' => 'voting'],
            
            // Pengawasan & Audit
            ['name' => 'supervise_elections', 'display_name' => 'Akses Dashboard Pengawas', 'category' => 'supervision'],
            ['name' => 'submit_observer_reports', 'display_name' => 'Buat Laporan Pengawasan', 'category' => 'supervision'],
            ['name' => 'view_audit_logs', 'display_name' => 'Lihat Jejak Audit', 'category' => 'audit'],
            ['name' => 'verify_audit_integrity', 'display_name' => 'Verifikasi Integritas Rantai Hash', 'category' => 'audit'],
            ['name' => 'create_audit_checkpoint', 'display_name' => 'Tanda Tangan Checkpoint Audit', 'category' => 'audit'],
            
            // Persetujuan & Laporan
            ['name' => 'approve_election_admin', 'display_name' => 'Persetujuan Hasil Pihak Admin', 'category' => 'approvals'],
            ['name' => 'approve_election_supervisor', 'display_name' => 'Persetujuan Hasil Pihak Pengawas', 'category' => 'approvals'],
            ['name' => 'publish_results', 'display_name' => 'Publikasi Hasil Resmi Pemilihan', 'category' => 'elections'],
            ['name' => 'export_reports', 'display_name' => 'Ekspor Hasil & Berita Acara PDF', 'category' => 'reports'],
        ];

        $permissionModels = [];
        foreach ($permissions as $perm) {
            $permissionModels[$perm['name']] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'category' => $perm['category'],
                ]
            );
        }

        // 3. Hubungkan Permissions ke Roles
        // Superadmin: Semua permissions kecuali cast_vote (karena Superadmin tidak boleh voting mewakili orang lain)
        $superAdminPerms = array_diff(array_keys($permissionModels), ['cast_vote']);
        $roleModels['superadmin']->permissions()->sync(
            array_map(fn($p) => $permissionModels[$p]->id, $superAdminPerms)
        );

        // Admin:
        $adminPerms = [
            'manage_residents', 'import_residents', 'export_residents', 'manage_dpt',
            'manage_elections', 'manage_candidates', 'view_audit_logs',
            'approve_election_admin', 'publish_results', 'export_reports',
        ];
        $roleModels['admin']->permissions()->sync(
            array_map(fn($p) => $permissionModels[$p]->id, $adminPerms)
        );

        // Pengawas:
        $supervisorPerms = [
            'supervise_elections', 'submit_observer_reports', 'view_audit_logs',
            'verify_audit_integrity', 'create_audit_checkpoint', 'approve_election_supervisor',
            'export_reports',
        ];
        $roleModels['pengawas']->permissions()->sync(
            array_map(fn($p) => $permissionModels[$p]->id, $supervisorPerms)
        );

        // Voter:
        $voterPerms = ['cast_vote'];
        $roleModels['voter']->permissions()->sync(
            array_map(fn($p) => $permissionModels[$p]->id, $voterPerms)
        );
    }
}
