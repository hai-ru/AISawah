<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InitSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $wilayah = [
            [
                "daerah"=>"KAB. SANGGAU",
                "kecamatan"=>[
                    "Toba",
                    "Meliau",
                    "Kapuas",
                    "Mukok",
                    "Jangkang",
                    "Bonti",
                    "Parindu",
                    "Tayan Hilir",
                    "Balai",
                    "Tayan Hulu",
                    "Kembayan",
                    "Beduwai",
                    "Noyan",
                    "Sekayam",
                    "Entikong",
                ]
            ],
            [
                "daerah"=>"KAB. SEKADAU",
                "kecamatan"=>[
                    "Belitang",
                    "Belitang Hilir",
                    "Belitang Hulu",
                    "Nanga Mahap",
                    "Nanga Taman",
                    "Sekadau Hilir",
                    "Sekadau Hulu",
                ]
            ]
        ];

        foreach ($wilayah as $key => $value) {
            $kab = \App\Models\Kabupaten::create(["name"=>$value["daerah"]]);
            $kecamatan = [];
            foreach ($value["kecamatan"] as $i => $val) {
                $new_data = [
                    "name"=>$val,
                    "kabupaten_id"=>$kab->id,
                ];
                $kecamatan[] = $new_data;
            }
            \App\Models\Wilayah::insert($kecamatan);
        }

    }
}
