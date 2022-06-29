<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class LogicController extends Controller
{
    public function get_kriteria(Request $request)
    {
        $formula = $request->formula ?? "saw";
        return \App\Models\Kriteria::where("formula",$formula)
        ->with("interval")
        ->get();
    }

    public function store_kriteria(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'type' => 'required',
            'symbol' => 'required',
            'bobot_preferensi' => 'required|min:0',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return[
                "status"=>"error",
                "message"=>$error
            ];
        }
        if($request->bobot_preferensi < 0){
            return[
                "status"=>"error",
                "message"=>"Minimum bobot preferensi 0"
            ];
        }
        if($request->has("item_id")){
            $k = \App\Models\Kriteria::find($request->item_id);
            if(empty($k))
                return[
                    "status"=>"error",
                    "message"=>"data tidak ditemukan"
                ];
            if($request->has("delete") && $request->delete == 1){
                $k->delete();
                return["status"=>"success","message"=>"data berhasil dihapus"];
            }

            if($request->has("interval")){

                foreach ($request->interval as $key => $value) {
                    if(isset($value["tipe"]) && $value["tipe"] == "condition") 
                    $value["condition"] = strtolower($value["condition"]);
                    if(isset($value["delete"])){
                        $k->interval()
                        ->where("id",$value["id"])
                        ->delete();
                        continue;
                    }
                    if(intVal($value["id"]) > 0){
                        $k->interval()
                        ->where("id",$value["id"])
                        ->first()
                        ->update($value);
                    } else {
                        $k->interval()->create($value);
                    }
                }
            }

            $k->update($data);
            return["status"=>"success","message"=>"data berhasil diubah"];
        }
        if(
            \App\Models\Kriteria::where("symbol",$request->symbol)
            ->get()
            ->isNotEmpty()
        ){
            return[
                "status"=>"error",
                "message"=>"Inisial sudah di pakai"
            ];
        }
        $data["tematik_id"] = \App\Models\Tematik::first()->id;
        $k = \App\Models\Kriteria::create($data);
        return["status"=>"success","message"=>"data berhasil ditambahkan"];

    }

    public function get_wilayah(Request $request)
    {
        if($request->has("kabupaten_id")){
            return \App\Models\Wilayah::where([
                "kabupaten_id"=>$request->kabupaten_id,
                "tipe"=>"Kecamatan"
            ])
            ->get();
        }
        if($request->has("kabupaten_id") && $request->has("kecamatan_id")){
            return \App\Models\Wilayah::where([
                "kabupaten_id"=>$request->kabupaten_id,
                "parent_id"=>$request->kecamatan_id,
                "tipe"=>"Desa"
            ])
            ->get();
        }
        return \App\Models\Kabupaten::orderBy("id","desc")->get();
    }

    public function get_data_alternatif(Request $request)
    {
        $s = ["Kecamatan"];
        if(isset($request->kecamatan_id) && intval($request->kecamatan_id) !== 0){
            $s = ["Desa"];
        }
        $data["columns"] = [
            [
                "type"=>'dropdown',
                "title"=>'Admin',
                "source"=>$s
            ],
            [
                "type"=>'text',
                "title"=>'Wilayah'
            ]
        ];

        $data["kriteria"] = \App\Models\Kriteria::where("formula",$request->formula)
        ->with("interval")
        ->orderBy("id","asc")
        ->get();

        foreach ($data["kriteria"] as $key => $value) {
            $new = [
                "type"=>'numeric',
                "title"=>$value->name
            ];
            if($value->interval()->groupBy("tipe")->first()->tipe == "condition"){
                $source = [];
                foreach ($value->interval as $i => $val) {
                    $source[] = $val->condition;
                }
                $new = [
                    "type"=>'dropdown',
                    "title"=>$value->name,
                    "source"=>$source
                ];
            }
            $data["columns"][] = $new;
        }

        $tematik = \App\Models\Tematik::first();

        $hasil = \App\Models\Nilai::where([
            "tematik_id"=>$tematik->id,
            "tahun"=>$request->tahun,
            "formula"=>$request->formula
        ])
        ->with("wilayah","wilayah.kabupaten","kriteria")
        ->whereHas("wilayah",function($q) use($request){

            if(isset($request->kecamatan_id) && intval($request->kecamatan_id) !== 0){
                $q->where([
                    "kabupaten_id"=>$request->kabupaten_id,
                    "parent_id"=>$request->kecamatan_id
                ]);
            }
            if(isset($request->kabupaten_id) && intval($request->kabupaten_id) !== 0){
                $q->where("kabupaten_id",$request->kabupaten_id);
            }
        })
        ->get();

        $row = $hasil->groupBy("wilayah_id");
        $data["data"] = [];
        foreach ($row as $key => $value) {
            $new_data = [
                $value[0]->wilayah->tipe,
                $value[0]->wilayah->name,
            ];
            $value = $value->sortBy("kriteria_id");
            // DD($value);
            foreach ($value as $i => $val) {
                $baru = $val->input;
                $tipe = $val->kriteria->interval()->groupBy("tipe")->first();
                if($tipe->tipe == "condition") {
                    $baru = \strval($val->input);
                    // DD($baru);
                }
                $new_data[] = $baru;
            }
            $data["data"][] = $new_data;
        }

        if(empty($data["data"])){
            $new_data = [];
            foreach ($data["columns"] as $key => $value) {
                $v = 0;
                if($value["type"] == "dropdown") $v = $value["source"][0];
                if($value["type"] == "text") $v = "";
                $new_data[] = $v;
            }
            $data["data"] = [$new_data];
        }

        // DD($data["data"]);

        return $data;
    }
}
