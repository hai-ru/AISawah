<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KriteriaSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $t = \App\Models\Tematik::create(["name"=>"Data lahan"]);
        $formula = "saw";
        $data = [
            [
                "name"=>"Luas Baku Sawah (Ha)",
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
                "type"=>"max",
            ],
            [
                "name"=>"Produksi (ton/thn)",
                "interval"=>[
                    [
                        "minimum"=>0,
                        "maximum"=>500,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>501,
                        "maximum"=>1000,
                        "bobot"=>2,
                    ],
                    [
                        "minimum"=>1000,
                        "maximum"=>0,
                        "bobot"=>3,
                    ],
                ],
                "formula"=>$formula,
                "type"=>"max",
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
                "type"=>"max",
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
                "type"=>"max",
            ],
            [
                "name"=>"HGU (Ha)",
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
                "type"=>"max",
            ],
            [
                "name"=>"Kawasan Moratorium Gambut (Ha)",
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
                "type"=>"max",
            ],
            [
                "name"=>"Kawasan Hutan (Ha)",
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
                "type"=>"max",
            ],
            [
                "name"=>"Jarak Sumber Air (km)",
                "interval"=>[
                    [
                        "minimum"=>0,
                        "maximum"=>2,
                        "bobot"=>1,
                    ],
                    [
                        "minimum"=>2.1,
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
                "type"=>"max",
            ]
        ];
        foreach ($data as $key => $value) {
            $kriteria = [
                "name"=>$value["name"],
                "formula"=>$value["formula"],
                "type"=>$value["type"],
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
