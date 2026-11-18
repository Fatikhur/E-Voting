<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'E-Voting RT/RW',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Nama resmi aplikasi sistem pemilihan digital.',
            ],
            [
                'key' => 'organization_name',
                'value' => 'Rukun Warga 01 Dusun Harmoni Indah',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Nama pengurus wilayah pengguna utama sistem.',
            ],
            [
                'key' => 'allow_online_registration',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Apakah pendaftaran warga secara mandiri diizinkan tanpa verifikasi admin.',
            ],
            [
                'key' => 'voting_token_expiry_minutes',
                'value' => '15',
                'type' => 'integer',
                'group' => 'voting',
                'description' => 'Masa berlaku token sekali pakai pada saat konfirmasi pilihan (menit).',
            ],
            [
                'key' => 'require_two_factor_admin',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Mewajibkan autentikasi dua faktor untuk peran Admin dan Super Admin.',
            ],
            [
                'key' => 'enable_anonymous_receipt',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'voting',
                'description' => 'Menerbitkan tanda terima anonim kriptografis bagi pemilih setelah suara disimpan.',
            ],
        ];

        foreach ($settings as $st) {
            SystemSetting::updateOrCreate(
                ['key' => $st['key']],
                $st
            );
        }
    }
}
