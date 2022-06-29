@extends('front-end.layouts.master')

@push('css')
    <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />
    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
    <style>
        .tableFixHead          { overflow: auto; height: 100; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
        #kondisi{
            display: none;
        }
    </style>
@endpush

@section('content')

<div class="card">
    @include('front-end.maut.menu')
    <div class="card-body">
        <h5 class="text-center">Data Alternatif</h5>
        <div class="">
            <button onclick="test()" class="btn btn-primary float-end"><i class="fas fa-arrow-right"></i> Lanjutkan</button>
            <a href="/" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card mt-2">
            <div class="card-header text-center">
                Pilih Wilayah
            </div>
            <div class="card-body">
                <form id="form_filter" method="POST" action="{{ route('data.alternatif') }}">
                    @csrf
                    <input type="hidden" name="formula" value="maut" />
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group mb-3">
                                <select required="true" id="kabupaten" class="form-control" name="kabupaten_id">
                                    <option value="0" selected>-- Pilih Kabupaten/Kota --</option>
                                </select>
                                <button class="btn btn-primary" type="button"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select id="kecamatan" class="form-control" name="kecamatan_id">
                                <option value="0" selected>-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <input id="tahun" name="tahun" required type="number" min="0" placeholder="Tahun" class="form-control" />
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                            <button type="button" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header text-center">
                Tabel Data
            </div>
            <div class="card-body table-responsive">
                <div id="spreadsheet"></div>
            </div>
        </div>
    </div>
</div>

<form id="goto_form" method="GET" action="/maut">
    <input type="hidden" name="step" value="3"/>
    <input type="hidden" name="formula" value="maut"/>
    <input type="hidden" name="kabupaten_id" value=""/>
    <input type="hidden" name="tahun" value=""/>
    <input type="hidden" name="kecamatan_id" value=""/>
</form>
@endsection

@push('js')

    <script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    <script>
        $(function() {
            LoadKabupaten();
        });

        const LoadKabupaten = () => {
            let elm = $("#kabupaten");
            $.ajax({
                method:"GET",
                url:"{{route('wilayah')}}",
                beforeSend:function(){
                    elm.attr("disabled",true);
                },
                success:function(res){
                    let dom = '<option value="0" selected>-- Pilih Kabupaten/Kota --</option>';
                    res.map((item,index)=>{
                        dom += `<option value="${item.id}">${item.name}</option>`;
                    })
                    elm.html(dom);
                },
                complete:function(e){
                    elm.removeAttr("disabled");
                }
            })
        }

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
                        dom += `<option value="${item.id}">${item.name}</option>`;
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

    <script>
        
        let jx = null;

        let initiate =[];
        $("#form_filter").validate({
            submitHandler:function(form){
                $.ajax({
                    method:"POST",
                    url:form.action,
                    data:$(form).serialize(),
                    success:function(res){
                        console.log(res,jx)
                        initiate = res;
                        if(jx !== null){
                            jexcel.destroy(document.getElementById('spreadsheet'), false);
                            $("#spreadsheet").removeClass();
                        }
                        jx += 1;
                        jspreadsheet(document.getElementById('spreadsheet'), {
                            data:res.data,
                            defaultColWidth: '200px',
                            allowInsertRow:true,
                            allowInsertColumn:false,
                            allowDeleteRow:false,
                            allowDeleteColumn:false,
                            columns: res.columns
                        });
                    },
                    error:function(e){
                        console.log(e)
                    }
                })
                return false;
            }
        })
        

    </script>
@endpush