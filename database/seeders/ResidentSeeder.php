<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        $village = Village::where('code', '32.01.01.2001')->first();
        if (!$village) {
            return;
        }

        $rw1 = Rw::where('village_id', $village->id)->where('rw_number', '01')->first();
        $rt1 = Rt::where('rw_id', $rw1->id)->where('rt_number', '01')->first();
        $rt2 = Rt::where('rw_id', $rw1->id)->where('rt_number', '02')->first();
        $rt3 = Rt::where('rw_id', $rw1->id)->where('rt_number', '03')->first();

        $residentsData = [
            // Tokoh & Kandidat
            [
                'nik' => '3276011204750001',
                'name' => 'H. Ahmad Fauzi, S.E.',
                'gender' => 'L',
                'birth_place' => 'Depok',
                'birth_date' => '1975-04-12',
                'address' => 'Jl. Harmoni Raya Blok A No. 12',
                'rt_id' => $rt1->id,
                'phone' => '081234567801',
            ],
            [
                'nik' => '3276012508800002',
                'name' => 'Ir. Budi Santoso, M.T.',
                'gender' => 'L',
                'birth_place' => 'Bogor',
                'birth_date' => '1980-08-25',
                'address' => 'Jl. Harmoni Raya Blok B No. 05',
                'rt_id' => $rt2->id,
                'phone' => '081234567802',
            ],
            [
                'nik' => '3276011802850003',
                'name' => 'Siti Aminah, S.Pd.',
                'gender' => 'P',
                'birth_place' => 'Jakarta',
                'birth_date' => '1985-02-18',
                'address' => 'Jl. Melati RT 03 Blok C No. 09',
                'rt_id' => $rt3->id,
                'phone' => '081234567803',
            ],
            // Pemilih Akun Demo Utama
            [
                'nik' => '3276010101900004',
                'name' => 'Fatikhur Rizki (Pemilih 1)',
                'gender' => 'L',
                'birth_place' => 'Depok',
                'birth_date' => '1990-01-01',
                'address' => 'Jl. Harmoni 1 No. 10',
                'rt_id' => $rt1->id,
                'phone' => '081234567804',
            ],
            // Warga / Pemilih Tambahan
            [
                'nik' => '3276011506880005',
                'name' => 'Dewi Lestari (Pemilih 2)',
                'gender' => 'P',
                'birth_place' => 'Bandung',
                'birth_date' => '1988-06-15',
                'address' => 'Jl. Harmoni 1 No. 15',
                'rt_id' => $rt1->id,
                'phone' => '081234567805',
            ],
            [
                'nik' => '3276012211920006',
                'name' => 'Eko Prasetyo (Pemilih 3)',
                'gender' => 'L',
                'birth_place' => 'Solo',
                'birth_date' => '1992-11-22',
                'address' => 'Jl. Harmoni 2 No. 08',
                'rt_id' => $rt2->id,
                'phone' => '081234567806',
            ],
            [
                'nik' => '3276010507950007',
                'name' => 'Rina Kartika (Pemilih 4)',
                'gender' => 'P',
                'birth_place' => 'Semarang',
                'birth_date' => '1995-07-05',
                'address' => 'Jl. Harmoni 2 No. 21',
                'rt_id' => $rt2->id,
                'phone' => '081234567807',
            ],
            [
                'nik' => '3276013009980008',
                'name' => 'Hendra Gunawan (Pemilih 5)',
                'gender' => 'L',
                'birth_place' => 'Yogyakarta',
                'birth_date' => '1998-09-30',
                'address' => 'Jl. Harmoni 3 No. 04',
                'rt_id' => $rt3->id,
                'phone' => '081234567808',
            ],
            // Warga Senior / Lansia (Contoh uji UI ramah lansia)
            [
                'nik' => '3276011005520009',
                'name' => 'Opa Sutrisno (Lansia - 74 Th)',
                'gender' => 'L',
                'birth_place' => 'Madiun',
                'birth_date' => '1952-05-10',
                'address' => 'Jl. Harmoni 1 No. 01',
                'rt_id' => $rt1->id,
                'phone' => '081234567809',
            ],
            [
                'nik' => '3276011210550010',
                'name' => 'Oma Kartini (Lansia - 71 Th)',
                'gender' => 'P',
                'birth_place' => 'Surabaya',
                'birth_date' => '1955-10-12',
                'address' => 'Jl. Harmoni 1 No. 02',
                'rt_id' => $rt1->id,
                'phone' => '081234567810',
            ],
        ];

        foreach ($residentsData as $res) {
            $nikHash = Resident::hashNik($res['nik']);
            $nikEncrypted = Crypt::encryptString($res['nik']);
            $nikMasked = Resident::maskNik($res['nik']);

            Resident::firstOrCreate(
                ['nik_hash' => $nikHash],
                [
                    'nik_encrypted' => $nikEncrypted,
                    'nik_masked' => $nikMasked,
                    'name' => $res['name'],
                    'gender' => $res['gender'],
                    'birth_place' => $res['birth_place'],
                    'birth_date' => $res['birth_date'],
                    'address' => $res['address'],
                    'village_id' => $village->id,
                    'rw_id' => $rw1->id,
                    'rt_id' => $res['rt_id'],
                    'phone' => $res['phone'],
                    'status' => 'active',
                    'is_eligible_voter' => true,
                    'notes' => 'Data warga awal hasil sinkronisasi wilayah RW 01',
                ]
            );
        }
    }
}
