@extends('front-end.layouts.master')

@push('css')
    <style>
        .tableFixHead          { overflow: auto; height: 100; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
        #kondisi{
            display: none;
        }
        .first{
            background: yellow;
        }
    </style>
@endpush

@section('content')

<input type="hidden" name="kec_select" id="kec_select" value="{{Request::get("kecamatan_id")}}" />
<input type="hidden" name="kab_select" id="kab_select" value="{{Request::get("kabupaten_id")}}" />

<div class="card">
    @include('front-end.maut.menu')
    <div class="card-body">
        <h5 class="text-center">Hasil Analisa</h5>
        <div class="">
            <a href="/?step=2" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card mt-2">
            <div class="card-header text-center">
                Pilih Wilayah
            </div>
            <div class="card-body">
                <form id="form_filter" method="GET" action="/">
                    @csrf
                    <input type="hidden" name="step" value="3" />
                    <input type="hidden" name="formula" value="maut" />
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group mb-3">
                                <select required="true" id="kabupaten" class="form-control" name="kabupaten_id">
                                    <option value="0" selected>-- Pilih Kabupaten/Kota --</option>
                                    @foreach (\App\Models\Kabupaten::orderBy("id","desc")->get() as $item)
                                        <option value="{{$item->id}}" {{ Request::get("kabupaten_id") == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select id="kecamatan" class="form-control" name="kecamatan_id">
                                <option value="0" selected>-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <input id="tahun" name="tahun" required type="number" min="0" placeholder="Tahun" class="form-control" value="{{Request::get("tahun")}}" />
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card mt-2">
                    <div class="card-header text-center">
                        1. Data Alternatif
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover tableFixHead">
                            <thead>
                                <tr>
                                    <th width="10">No.</th>
                                    <th>Wilayah</th>
                                    @foreach ($kriteria as $item)
                                        <th>{{ $item[0]->kriteria->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php($count = 1)
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td>{{$count++}}</td>
                                        <td>{{$item[0]->wilayah->name}}</td>
                                        @foreach ($item as $i => $val)
                                            <td>{{$val->input}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card mt-2">
                    <div class="card-header text-center">
                        2. Rating Kecocokan Alternatif
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover tableFixHead">
                            <thead>
                                <tr>
                                    <th width="10">No.</th>
                                    <th>Wilayah</th>
                                    @foreach ($kriteria as $item)
                                        <th>
                                            {{ $item[0]->kriteria->name }}
                                            <br/>
                                            {{ strtoupper($item[0]->kriteria->symbol) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php($count = 1)
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td>{{$count++}}</td>
                                        <td>{{$item[0]->wilayah->name}}</td>
                                        @foreach ($item as $i => $val)
                                            <td>{{$val->hasil_bobot}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card mt-2">
                    <div class="card-header text-center">
                        3. Hasil Analisa
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover tableFixHead">
                            <thead>
                                <tr>
                                    <th width="10">No</th>
                                    <th>Wilayah</th>
                                    <th>Skor</th>
                                    @foreach ($kriteria as $item)
                                        <th>
                                            {{ $item[0]->kriteria->name }}
                                            <br/>
                                            {{ strtoupper($item[0]->kriteria->symbol) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hasil as $key => $item)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$item["wilayah"]}}</td>
                                            <td>{{ $item["skor"] }}</td>
                                            @foreach ($item["data"] as $i => $val)
                                                <td>{{$val->normalisasi}} x {{$val->kriteria->bobot_preferensi}} = {{$val->output}}</td>
                                            @endforeach
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card mt-2">
                    <div class="card-header text-center">
                        4. Hasil Ranking
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover tableFixHead">
                            <thead>
                                <tr>
                                    <th width="10">Ranking</th>
                                    <th>Wilayah</th>
                                    <th>Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ranking as $key => $item)
                                        <tr class="{{$key == 0 ? 'first' : ''}}">
                                            <td>{{$key+1}}</td>
                                            <td>{{$item["wilayah"]}}</td>
                                            <td>{{ $item["skor"] }}</td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

    <script>

        $(function() {
            const v = parseInt($("#kab_select").val());
            tahun = $("#tahun").val();
            LoadKecamatan(v);
        });
        const LoadKecamatan = (kab_id) => {
            let elm = $("#kecamatan");
            $.ajax({
                method:"GET",
                url:"{{route('wilayah')}}",
                data:{"kabupaten_id":kab_id},
                beforeSend:function(){
                    elm.attr("disabled",true);
                },
                success:function(res){
                    let dom = '<option value="0" selected>-- Pilih Kecamatan --</option>';
                    res.map((item,index)=>{
                        const s = $("#kec_select").val();
                        const status = parseInt(s) == item.id ? "selected" : "";
                        console.log(s,status)
                        dom += `<option value="${item.id}" ${status}>${item.name}</option>`;
                    })
                    elm.html(dom);
                },
                complete:function(e){
                    elm.removeAttr("disabled");
                }
            })
        }

        let kec_id = 0;
        $("#kecamatan").change(function($e){
            kec_id = $(this).val();
        });
        let tahun = "";
        $("#tahun").keyup(function($e){
            tahun = $(this).val();
        });

        let kab_id = 0;
        $("#kabupaten").change(function($e){
            kab_id = $(this).val();
            LoadKecamatan($(this).val());
        })

        const simpanData = () => {
        }

        function test() {
            if(kab_id == 0 && tahun == ""){
                return swal("","silahkan tampilkan data alternatif terlebih dahulu","info");
            }
            $("#goto_form input[name=kabupaten_id]").val(kab_id);
            $("#goto_form input[name=kecamatan_id]").val(kec_id);
            $("#goto_form input[name=tahun]").val(tahun);
            $("#goto_form").submit();
        }
        
    </script>

@endpush