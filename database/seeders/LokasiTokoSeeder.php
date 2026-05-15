<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LokasiToko;

class LokasiTokoSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'barcode' => 'CSAsWRjHzFSBmV5WeNH5lC0Rcgr1',
                'nama_toko' => 'Kopi Darma',
                'latitude' => -7.2709993345725,
                'longitude' => 112.75499219888,
                'accuracy' => 85,
            ],
            [
                'barcode' => 'https://maps.app.goo.gl/yrRhR7L4j3ZimaKPA',
                'nama_toko' => 'Three Vision Cafe',
                'latitude' => -7.2708727714234,
                'longitude' => 112.75495049127,
                'accuracy' => 68,
            ],
            [
                'barcode' => 'TOKO_ALPHA_SURABAYA',
                'nama_toko' => 'Toko Alpha',
                'latitude' => -7.2710,
                'longitude' => 112.7550,
                'accuracy' => 42,
            ],
            [
                'barcode' => 'TOKO_BETA_MALL',
                'nama_toko' => 'Toko Beta Mall',
                'latitude' => -7.2712,
                'longitude' => 112.7551,
                'accuracy' => 55,
            ],
            [
                'barcode' => 'TOKO_GAMMA_PUSAT',
                'nama_toko' => 'Toko Gamma Pusat',
                'latitude' => -7.2708,
                'longitude' => 112.7549,
                'accuracy' => 73,
            ],
        ];

        foreach ($data as $item) {
            LokasiToko::updateOrCreate(
                ['barcode' => $item['barcode']],
                $item
            );
        }

        echo "\n✅ Seeder: " . count($data) . " data toko berhasil ditambahkan!\n";
    }
}
