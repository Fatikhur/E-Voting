<?php

namespace Database\Seeders;

use App\Models\Rt;
use App\Models\Rw;
use App\Models\Village;
use Illuminate\Database\Seeder;

class TerritorySeeder extends Seeder
{
    public function run(): void
    {
        // Kelurahan Sukamaju
        $village = Village::firstOrCreate(
            ['code' => '32.01.01.2001'],
            [
                'name' => 'Kelurahan Sukamaju',
                'district' => 'Kecamatan Cilodong',
                'regency' => 'Kota Depok',
                'postal_code' => '16413',
            ]
        );

        // RW 01
        $rw1 = Rw::firstOrCreate(
            ['village_id' => $village->id, 'rw_number' => '01'],
            [
                'name' => 'Dusun Harmoni Indah',
                'description' => 'Wilayah pemukiman Harmoni Indah RT 01 s/d RT 03',
            ]
        );

        // RW 02
        $rw2 = Rw::firstOrCreate(
            ['village_id' => $village->id, 'rw_number' => '02'],
            [
                'name' => 'Dusun Sejahtera Asri',
                'description' => 'Wilayah pemukiman Sejahtera Asri',
            ]
        );

        // RT pada RW 01
        $rtsRw1 = ['01', '02', '03'];
        foreach ($rtsRw1 as $num) {
            Rt::firstOrCreate(
                ['rw_id' => $rw1->id, 'rt_number' => $num],
                [
                    'name' => "Rukun Tetangga {$num}",
                    'description' => "RT {$num} wilayah RW 01 Dusun Harmoni Indah",
                ]
            );
        }

        // RT pada RW 02
        $rtsRw2 = ['01', '02'];
        foreach ($rtsRw2 as $num) {
            Rt::firstOrCreate(
                ['rw_id' => $rw2->id, 'rt_number' => $num],
                [
                    'name' => "Rukun Tetangga {$num}",
                    'description' => "RT {$num} wilayah RW 02 Dusun Sejahtera Asri",
                ]
            );
        }
    }
}
