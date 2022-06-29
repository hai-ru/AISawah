<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Wilayah2Seed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $k = \App\Models\Kabupaten::where("name","like","%SEKADAU%")->first();
        $input = [
            [
                "name"=>"Belitang",
                "kabupaten_id"=>$k->id
            ],
            [
                "name"=>"Belitang Hilir",
                "kabupaten_id"=>$k->id
            ],
            [
                "name"=>"Belitang Hulu",
                "kabupaten_id"=>$k->id
            ],
            [
                "name"=>"Nanga Mahap",
                "kabupaten_id"=>$k->id
            ],
            [
                "name"=>"Nanga Taman",
                "kabupaten_id"=>$k->id
            ],
            [
                "name"=>"Sekadau Hilir",
                "kabupaten_id"=>$k->id
            ],
            [
                "name"=>"Sekadau Hulu",
                "kabupaten_id"=>$k->id
            ],
        ];
        \App\Models\Wilayah::insert($input);
    }
}
