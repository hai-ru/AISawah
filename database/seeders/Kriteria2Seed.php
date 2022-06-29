<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Kriteria2Seed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $t = \App\Models\Tematik::where(["name"=>"Data lahan"])->first();
        $formula = "maut";
        $data = [
            [
                "name"=>"Luas Baku (Ha)",
                "interval"=>[
                    [
                        "minimum"=>0,
                        "maximum"=>100,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>101,
                        "maximum"=>200,
                        "bobot"=>2,
                    ],
                    [
                        "minimum"=>200,
                        "maximum"=>0,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D1"
            ],
            [
                "name"=>"Produksi (ton/thn)",
                "interval"=>[
                    [
                        "minimum"=>0,
                        "maximum"=>2,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>2.1,
                        "maximum"=>4,
                        "bobot"=>2,
                    ],
                    [
                        "minimum"=>4,
                        "maximum"=>0,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D2"
            ],
            [
                "name"=>"Indeks Pertanaman (IP)",
                "interval"=>[
                    [
                        "condition"=>1,
                        "bobot"=>1,
                    ],
                    [
                        "condition"=>2,
                        "bobot"=>2,
                    ],
                    [
                        "condition"=>3,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D3"
            ],
            [
                "name"=>"Produktivitas (ton/ha)",
                "interval"=>[
                    [
                        "minimum"=>0,
                        "maximum"=>1,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>1.1,
                        "maximum"=>3,
                        "bobot"=>2,
                    ],
                    [
                        "minimum"=>3,
                        "maximum"=>0,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D4"
            ],
            [
                "name"=>"Jenis Permukaan Jalan",
                "interval"=>[
                    [
                        "condition"=>"tanah",
                        "bobot"=>1,
                    ],
                    [
                        "condition"=>"kerikil",
                        "bobot"=>2,
                    ],
                    [
                        "condition"=>"batu",
                        "bobot"=>2,
                    ],
                    [
                        "condition"=>"aspal",
                        "bobot"=>3,
                    ],
                    [
                        "condition"=>"beton",
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D5"
            ],
            [
                "name"=>"Jumlah Penduduk",
                "interval"=>[
                    [
                        "minimum"=>0,
                        "maximum"=>15999,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>16000,
                        "maximum"=>30000,
                        "bobot"=>2,
                    ],
                    [
                        "minimum"=>30001,
                        "maximum"=>0,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D6"
            ],
            [
                "name"=>"Jarak ke ibukota kabupaten",
                "interval"=>[
                    [
                        "minimum"=>30,
                        "maximum"=>0,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>11,
                        "maximum"=>29,
                        "bobot"=>2,
                    ],
                    [
                        "minimum"=>0,
                        "maximum"=>10,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "symbol"=>"D7"
            ]
        ];

        foreach ($data as $key => $value) {

            $kriteria = [
                "name"=>$value["name"],
                "formula"=>$value["formula"],
                "symbol"=>$value["symbol"],
                "tematik_id"=>$t->id
            ];
            
            $k = \App\Models\Kriteria::create($kriteria);
            $interval = [];
            foreach ($value["interval"] as $i => $val) {
                $new = $val;
                $new["kriteria_id"] = $k->id;
                $interval[] = $new;
            }
            \App\Models\Interval::insert($interval);
        }
    }
}
