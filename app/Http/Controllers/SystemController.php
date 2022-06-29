<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Str;
use DB;

class SystemController extends Controller
{

    private function readCSV($csvFile)
    {
        $delimiter = ";";
        $file_handle = fopen($csvFile, 'r');
        $line_of_text = [];
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $delimiter);
        }
        fclose($file_handle);
        return $line_of_text;
    }

    private function olah_string($val){
        $data = \explode(",",$val);
        $ribuan = \explode(".",$data[0]);
        $ribuan = \join("",$ribuan);
        if(!isset($data[1])) $data[1] = 0;
        $val = $ribuan.".".$data[1];
        return floatval($val);
    }

    public function import_data(Request $request)
    {
        
        $type = $request->import_file->getClientOriginalExtension();
        $folder = "/import-csv";
        $file_name = Str::random(9).".".$type;
        $path = $request->import_file->storeAs($folder,$file_name);
        $path = \storage_path("app/".$path);
        $result = $this->readCSV($path);
        $header = [];

        //cari header
        foreach ($result[0] as $key => $value) {
            $value = strtolower($value);
            $kriteria = \App\Models\Kriteria::where("symbol",$value)->with("tematik")->first();
            $new_data = [
                "columnName"=>$value,
                "kriteria"=>$kriteria,
            ];
            $header[] = $new_data;
        }

        DB::beginTransaction();
        try {

            //olah hasil bobot
            foreach ($result as $key => $value) {
                if($key === 0) continue;
                $wilayah = null;
                foreach ($value as $i => $val) {
                    $type = $header[$i];
                    if(Str::contains($type["columnName"] , "wilayah")){
                        $wilayah = \App\Models\Wilayah::where("name","like","%".$val."%")->first();
                        continue;
                    }

                    $new_row["input_type"] = "string";
                    if (preg_match('~[0-9]+~', $val)) {
                        $new_row["input_type"] = "number";
                        $val = $this->olah_string($val);
                    }
                    $kriteria = $type["kriteria"];
                    if(!isset($kriteria->id)) {
                        return ["status"=>false,"message"=>"invalid file format","data"=>$type];
                    }
                    $new_row["kriteria_id"] = $kriteria->id;
                    $new_row["tematik_id"] = $kriteria->tematik->id;
                    $new_row["input"] = $val;
                    $new_row["formula"] = $request->formula;
                    $new_row["tahun"] = $request->tahun;
                    $new_row["wilayah_id"] = $wilayah->id;
                    $row[] = $new_row;

                    if($request->formula == "maut"){
                        $bobot_tertinggi_kriteria = $kriteria->interval()->max("bobot");
                    }

                    foreach ($kriteria->interval as $i => $interval) {
                        if($interval->type() == "interval") {
                            $val = \floatval($val);
                            if($interval->maximum == 0 && $val >= $interval->minimum){
                                $new_row["hasil_bobot"] = $interval->bobot;
                                break;
                            }
                            if($val >= $interval->minimum && $val <= $interval->maximum){
                                $new_row["hasil_bobot"] = $interval->bobot;
                                break;
                            }
                        } else {
                            $val = \strval($val);
                            $interval->condition = \strval($interval->condition);
                            if(strtolower($val) == strtolower($interval->condition)){
                                $new_row["hasil_bobot"] = $interval->bobot;
                            }
                        }

                    }

                    if($request->formula == "maut"){
                        $new_row["hasil_bobot"] = $new_row["hasil_bobot"] / $bobot_tertinggi_kriteria;
                        $new_row["hasil_bobot"] = number_format($new_row["hasil_bobot"],2);
                    }

                    \App\Models\Nilai::create($new_row);

                }
            }

            DB::commit();

            //olah sesuai metode
            switch ($request->formula) {
                case 'saw':
                    $this->olah_saw();
                    break;
                case 'maut':
                    $this->olah_maut();
                    break;
                
                default:
                    return ["status"=>false,"message"=>"metode belum didukung"];
                    break;
            }


            return redirect()->route("hasil",
            [
                "tematik_id"=>$request->tematik_id,
                "kabupaten_id"=>$wilayah->kabupaten_id,
                "formula"=>$request->formula,
                "tahun"=>$request->tahun,
            ]);
        } catch(\Exception $e) {
            DD($e);
            return ["status"=>false,"message"=>$e->getMessage()];
        }

    }

    public function olah_saw()
    {
        $n = \App\Models\Nilai::with("kriteria","kriteria.interval","wilayah","wilayah.kabupaten")
        ->where("formula","saw")
        ->take(1000)
        ->get();
        
        foreach($n as $key => $value){
            $result = \App\Models\Nilai::whereHas("wilayah",function($q) use($value) {
                $q->where("kabupaten_id",$value->wilayah->kabupaten_id);
            })
            ->where([
                "tematik_id"=>$value->tematik_id,
                "tahun"=>$value->tahun,
                "kriteria_id"=>$value->kriteria_id,
                "formula"=>"saw"
            ])
            ->select(
                "tematik_id",
                "wilayah_id",
                "kriteria_id",
                "tahun",
                "hasil_bobot",
            )
            ->addSelect(DB::raw("min(hasil_bobot) as minimum"))
            ->addSelect(DB::raw("max(hasil_bobot) as maximum"))
            ->get();

            $indicator = $result->first();

            $value->normalisasi = $value->kriteria->type === "max" ?
                $value->hasil_bobot / $indicator->maximum
                :
                $indicator->minimum / $value->hasil_bobot;
            $value->normalisasi = number_format($value->normalisasi,2);
            $value->output = $value->normalisasi * $value->kriteria->bobot_preferensi;
            $value->output = number_format($value->output,2);
            $value->save();
            
        }

        return true;

    }
    public function olah_maut()
    {
        $n = \App\Models\Nilai::with("kriteria","kriteria.interval","wilayah","wilayah.kabupaten")
        ->where("formula","maut")
        ->take(1000)
        ->get();

        DB::beginTransaction();
        try{
            
            foreach($n as $key => $value){

                $indicator = \App\Models\Nilai::whereHas("wilayah",function($q) use($value) {
                    $q->where("kabupaten_id",$value->wilayah->kabupaten_id);
                })
                ->where([
                    "tematik_id"=>$value->tematik_id,
                    "tahun"=>$value->tahun,
                    "kriteria_id"=>$value->kriteria_id,
                    "formula"=>"maut"
                ])
                ->select(
                    "tematik_id",
                    "wilayah_id",
                    "kriteria_id",
                    "tahun",
                    "hasil_bobot",
                )
                ->addSelect(DB::raw("min(hasil_bobot) as minimum"))
                ->addSelect(DB::raw("max(hasil_bobot) as maximum"))
                ->first();

                // DD("( ".$value->hasil_bobot." - ".$indicator->minimum." ) / ".$indicator->maximum,$indicator);

                $pembagi = $indicator->maximum - $indicator->minimum;
                $value->normalisasi = $pembagi == 0 ? 0 : ( $value->hasil_bobot - $indicator->minimum ) / $pembagi;
                $value->normalisasi = number_format($value->normalisasi,2);
                $value->output = $value->normalisasi * $value->kriteria->bobot_preferensi;
                $value->output = number_format($value->output,2);
                $value->save();
                
            }
            DB::commit();
    
            return true;
        } catch(\Exception $e) {
            DD($e);
            return false;
        }

    }

    public function hasil_import(Request $request)
    {
        if($request->has("tipe")) {

            if($request->tipe == "import") {
                return redirect()->route("import");
            }
            
            if($request->tipe == "reset") {
                \App\Models\Nilai::where([
                    "tematik_id"=>$request->tematik_id,
                    "tahun"=>$request->tahun,
                ])
                ->whereHas("wilayah",function($q) use($request){
                    $q->where("kabupaten_id",$request->kabupaten_id);
                })
                ->delete();
            }
        }
        $data["formula"] = [
            "saw"=>"Simple Additive Weighting",
            "maut"=>"Multi Attribute Utility Theory"
        ];
        $data["judul"] = "";
        if($request->has("formula")) $data["judul"] = $data["formula"][$request->formula]." (".strtoupper($request->formula).")";

        $data["kabupaten"] = \App\Models\Kabupaten::get();
        $data["tahun"] = \App\Models\Nilai::groupBy("tahun")->get();
        $data["tematik"] = \App\Models\Tematik::orderBy("id","desc")->get();
        $hasil = \App\Models\Nilai::where([
            "tematik_id"=>$request->tematik_id,
            "tahun"=>$request->tahun,
            "formula"=>$request->formula
        ])
        ->with("wilayah","wilayah.kabupaten","kriteria")
        ->whereHas("wilayah",function($q) use($request){
            $q->where("kabupaten_id",$request->kabupaten_id);
        })
        ->get();
        $data["data"] = $hasil->groupBy("wilayah_id");
        $data["kriteria"] = $hasil->groupBy("kriteria_id");
        $data["ranking"] = [];
        foreach($data["data"] as $key => $value){
            $new_data = [
                "wilayah"=>$value[0]->wilayah->name,
                "skor"=>$value->sum("output"),
                "data"=>$value
            ];
            $data["ranking"][] = $new_data;
        }
        // usort($data["ranking"], function ($item1, $item2) {
        //     return $item2['skor'] <=> $item1['skor'];
        // });
        return view('hasil-import',$data);
    }

    public function test(Request $request)
    {
        if($request->has("seed")){
            $input = [
                [
                    "symbol"=>"D9",
                    "interval"=>[
                        [
                            "condition"=>"tidak ada",
                            "bobot"=>1,
                        ],
                        [
                            "condition"=>"sebagian",
                            "bobot"=>2,
                        ],
                        [
                            "condition"=>"seluruh",
                            "bobot"=>3,
                        ],
                    ],
                ],
                [
                    "symbol"=>"D10",
                    "interval"=>[
                        [
                            "minimum"=>0,
                            "maximum"=>15999,
                            "bobot"=>1,
                        ],
                        [
                            "minimum"=>16000,
                            "maximum"=>29999,
                            "bobot"=>2,
                        ],
                        [
                            "minimum"=>30000,
                            "maximum"=>0,
                            "bobot"=>3,
                        ],
                    ],
                ]
            ];
            foreach ($input as $key => $value) {
                $k = \App\Models\Kriteria::where("symbol",$value["symbol"])->first();
                foreach ($value["interval"] as $key => $value) {
                    $value["kriteria_id"] = $k->id;
                    \App\Models\Interval::create($value);
                }
            }
            return true;
        }
        // $formula = $request->formula ?? "maut";
        // return \App\Models\Kriteria::where("formula",$formula)
        // ->with("interval")
        // ->get();
        $n = \App\Models\Nilai::where("input_type","string")->get();
        foreach($n as $k => $v) {
            $v->input = strtolower($v->input);
            $v->save();
        }
        return $n;
    }

    public function home(Request $request)
    {
        if(isset($request->step)){
            switch ($request->step) {
                case '2':
                    return view('front-end.saw.step2');
                    break;
                case '3':

                    $hasil = \App\Models\Nilai::where([
                        "tematik_id"=>4,
                        "tahun"=>$request->tahun,
                        "formula"=>"saw"
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

                    $data["data"] = $hasil->groupBy("wilayah_id");
                    $data["kriteria"] = $hasil->groupBy("kriteria_id");
                    $data["hasil"] = [];
                    foreach($data["data"] as $key => $value){
                        $new_data = [
                            "wilayah"=>$value[0]->wilayah->name,
                            "skor"=>$value->sum("output"),
                            "data"=>$value
                        ];
                        $data["hasil"][] = $new_data;
                    }
                    $data["ranking"] = $data["hasil"];
                    usort($data["ranking"], function ($item1, $item2) {
                        return $item2['skor'] <=> $item1['skor'];
                    });
                    return view('front-end.saw.step3',$data);
                    break;
                
                default:
                    return view('front-end.homepage');
                    break;
            }
        }
        return view('front-end.homepage');
    }

    public function maut(Request $request)
    {
        if(isset($request->step)){
            switch ($request->step) {
                case '2':
                    return view('front-end.maut.step2');
                    break;
                case '3':

                    $hasil = \App\Models\Nilai::where([
                        "tematik_id"=>4,
                        "tahun"=>$request->tahun,
                        "formula"=>"maut"
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

                    $data["data"] = $hasil->groupBy("wilayah_id");
                    $data["kriteria"] = $hasil->groupBy("kriteria_id");
                    $data["hasil"] = [];
                    foreach($data["data"] as $key => $value){
                        $new_data = [
                            "wilayah"=>$value[0]->wilayah->name,
                            "skor"=>$value->sum("output"),
                            "data"=>$value
                        ];
                        $data["hasil"][] = $new_data;
                    }
                    $data["ranking"] = $data["hasil"];
                    usort($data["ranking"], function ($item1, $item2) {
                        return $item2['skor'] <=> $item1['skor'];
                    });
                    return view('front-end.maut.step3',$data);
                    break;
                
                default:
                    return view('front-end.maut.step1');
                    break;
            }
        }
        return view('front-end.maut.step1');
    }

}
