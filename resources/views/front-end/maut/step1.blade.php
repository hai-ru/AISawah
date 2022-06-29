@extends('front-end.layouts.master')

@push('css')
    <style>
        .tableFixHead          { overflow: auto; height: 100; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
        #kondisi{
            display: none;
        }
    </style>
@endpush

@section('content')
<!-- Modal -->
<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="ubah_data" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Data & Interval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit" method="POST" action="{{route("kriteria.store")}}">
                    @csrf
                    <input type="hidden" name="formula" value="maut" />
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Kriteria</label>
                            <input required name="name" placeholder="xxx" class="form-control" />
                        </div>
                        <div class="col-sm-4">
                            <label>Inisial</label>
                            <input required name="symbol" placeholder="c1" class="form-control" />
                        </div>
                        <div class="col-sm-4">
                            <label>Bobot Preferensi</label>
                            <input required name="bobot_preferensi" placeholder="0" type="number" min="0" class="form-control" />
                        </div>
                    </div>
                </form>

                <div class="card mt-2">
                    <div class="card-header text-center">
                        Form Interval
                    </div>
                    <div class="card-body">
                        <form id="form_interval_editor">
                            @csrf
                            <input type="hidden" name="id" value="0" />
                            <input type="hidden" name="index" value="-1" />
                            <p>Perubahan : <span id="interval_editor">Tambah data</span></p>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Tipe</label>
                                    <select id="tipe_interval_input" required name="tipe" class="form-control">
                                        <option value="interval">interval</option>
                                        <option value="condition">condition</option>
                                    </select>
                                </div>
                                <div class="col-sm-6" id="kondisi">
                                    <label>Kondisi</label>
                                    <input name="condition" placeholder="0" class="form-control" />
                                </div>
                                <div class="col-sm-3 interval_input">
                                    <label>Min</label>
                                    <input type="number" required name="minimum" placeholder="0" class="form-control" />
                                </div>
                                <div class="col-sm-3 interval_input">
                                    <label>Max</label>
                                    <input type="number" required name="maximum" placeholder="10" class="form-control" />
                                    <p>*isi 0 jika lebih dari sama dengan</p>
                                </div>
                                <div class="col-sm-3">
                                    <div>
                                        <label>Bobot</label>
                                        <input type="number" required name="bobot" placeholder="1" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="d-grid mt-2">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header text-center">
                        Data Interval
                    </div>
                    <div class="card-body">
                        <div class="text-end">
                            <button onclick="tambahInterval()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</button>
                        </div>
                        <table id="interval_data" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tipe</th>
                                    <th>Parameter</th>
                                    <th>Bobot</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button id="edit_simpan" onclick="$('#edit').submit()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    @include('front-end.maut.menu')
    <div class="card-body">
        <h5 class="text-center">Kriteria & Interval</h5>
        <div class="text-end">
            <button onclick="simpan()" class="btn btn-primary"><i class="fas fa-arrow-right"></i> Lanjutkan</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover tableFixHead" id="kriteria_tabel">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Inisial</th>
                        <th>Bobot Preferensi (100%)</th>
                        <th>Interval</th>
                    </tr>
                    <tr>
                        <form id="add" method="POST" action="{{route("kriteria.store")}}">
                            @csrf
                            <input type="hidden" name="formula" value="maut" />
                            <th>*</th>
                            <th>
                                <input 
                                required
                                name="name" 
                                placeholder="kriteria baru" 
                                class="form-control" />
                            </th>
                            <th><input required name="symbol" placeholder="inisial" class="form-control" /></th>
                            <th><input required name="bobot_preferensi" placeholder="bobot" type="number" min="0" class="form-control" /></th>
                            <th><div class="d-grid"><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button></div></th>
                        </form>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center"> <i class="fas fa-circle-notch fa-spin"></i> Sedang memuat...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        let data = [];
        let status_save = null;

        const LoadKriteria = () => {
            $.ajax({
                method:"GET",
                url:"{{ route('kriteria.data',['formula'=>'maut']) }}",
                beforeSend:function(){
                    const dom = `<tr>
                        <td colspan="6" class="text-center"> <i class="fas fa-circle-notch fa-spin"></i> Sedang memuat...</td>
                    </tr>`;
                    $("#kriteria_tabel tbody").html(dom);
                },
                success:function(res){
                    let total_bobot = 0;
                    let dom = "";
                    data = res;
                    status_save = null;
                    res.map((item,index)=>{
                        let interval_dom = '<tr><td colspan="2">Prameter</td><td>Bobot</td></tr>';

                        if(item.interval.length == 0){
                            status_save = index;
                            interval_dom += `<tr><td colspan="3" class="text-center"><i class="fas fa-times"></i> Interval Kosong</td></tr>`;
                        }

                        item.interval.map((item,index)=>{
                            let dom_in = "<tr>";
                            const val = item.tipe !== "condition" ? 
                                "<td>"+item.minimum+" - "+item.maximum+"</td>"
                                :
                                "<td>"+item.condition+"</td>";
                            dom_in += val;
                            dom_in += "<td>=</td><td>"+item.bobot+"</td></tr>";
                            interval_dom += dom_in;
                        })
                        dom += `<tr id="index_row_${index}">
                            <td>${index+1}</td>
                            <td>${item.name}</td>
                            <td>${item.symbol}</td>
                            <td>${item.bobot_preferensi}</td>
                            <td>
                                <p class="text-center mb-0">
                                    <a onclick="UbahData(${index})" href="javascript:void(0)"><i class="fas fa-pencil-alt"></i> Ubah Data & Interval</a>
                                    <a onclick="HapusData(${index})" href="javascript:void(0)"><i class="fas fa-trash"></i> Hapus Data</a>
                                </p>
                                <table class="table">
                                    ${interval_dom}
                                </table>
                            </td>
                        </tr>`;
                        total_bobot += item.bobot_preferensi;
                    })
                    dom += `<tr>
                            <td colspan="3" class="text-center">TOTAL</td>
                            <td colspan="2" class="text-start">${total_bobot}</td>
                        </tr>`;
                    $("#kriteria_tabel tbody").html(dom);
                },
                error:function(){
                    const dom = `
                    <tr>
                        <td colspan="4" class="text-center"> <i class="fas fa-times"></i> Gagal memuat. <a onclick="LoadKriteria()" href="javascript:void(0)">Muat Ulang</a> </td>
                    </tr>`;
                    $("#kriteria_tabel tbody").html(dom);
                }
            })
        }
        let edit_data = null;
        let edit_data_index = null;
        const UbahData = (index) => {
            edit_data_index = index;
            edit_data = data[index];
            $("#edit input[name=name]").val(edit_data.name);
            $("#edit select[name=type]").val(edit_data.type);
            $("#edit input[name=symbol]").val(edit_data.symbol);
            $("#edit input[name=bobot_preferensi]").val(edit_data.bobot_preferensi);
            LoadInterval();
            $("#ubah_data").modal("show")
        }

        const LoadInterval = () => {
            let dom = "";
            edit_data.interval.map((item,index)=>{
                if(item.delete === 1) return;
                const param = item.tipe === "interval" ?
                +item.minimum+" - "+item.maximum
                :
                item.condition;
                const data = `
                <tr>
                    <td>${index+1}</td>
                    <td>${item.tipe}</td>
                    <td>${param}</td>
                    <td>${item.bobot}</td>
                    <td class="text-center">
                        <a onclick="ubahInterval(${index})" href="javascript:void(0)">Ubah</a>
                        <a onclick="hapusInterval(${index})" href="javascript:void(0)">Hapus</a>
                    </td>
                </tr>
                `;
                dom += data;
            })
            $("#interval_data tbody").html(dom);
        }

        $(function() {
            LoadKriteria();
        });

        $("#add").validate({
            submitHandler:function(form){
                $.ajax({
                    method:"POST",
                    url:"{{ route('kriteria.store') }}",
                    data:$(form).serialize(),
                    beforeSend:function(){
                        $("#add button").attr("disabled",true);
                    },
                    success:function(res){
                        swal('',res.message,res.status);
                        LoadKriteria();
                    },
                    error:function(e){
                        console.log(e);
                    },
                    complete:function(){
                        $("#add button").attr("disabled",false);
                    }
                })
                return false;
            }
        })

        $("#edit").validate({
            submitHandler:function(form){
                const data = getFormData($("#edit"))
                for(key in data){
                    edit_data[key] = data[key];
                }
                edit_data.item_id = edit_data.id;
                console.log(edit_data)
                $.ajax({
                    method:"POST",
                    url:"{{ route('kriteria.store') }}",
                    data:edit_data,
                    beforeSend:function(){
                        $("#edit_simpan").attr("disabled",true);
                        $("#edit_simpan").text("Loading");
                    },
                    success:function(res){
                        swal('',res.message,res.status);
                        LoadKriteria();
                        if(res.status == "success") $("#ubah_data").modal("hide");
                    },
                    error:function(e){
                        console.log(e);
                    },
                    complete:function(){
                        $("#edit_simpan").attr("disabled",false);
                        $("#edit_simpan").text("Simpan");
                    }
                })
                return false;
            }
        })

        const HapusData = (index) => {
            let input = data[index];
            input.delete = 1;
            input.item_id = input.id;
            $.ajax({
                method:"POST",
                url:"{{ route('kriteria.store') }}",
                data:input,
                beforeSend:function(){
                    // $("#add button").attr("disabled",true);
                },
                success:function(res){
                    swal('',res.message,res.status);
                    LoadKriteria();
                    if(res.status == "success") $("#ubah_data").modal("hide");
                },
                error:function(e){
                    console.log(e);
                },
                complete:function(){
                    // $("#add button").attr("disabled",false);
                }
            })
        }

        const simpan = () => {
            if(status_save !== null){
                $('html, body').animate({
                    scrollTop: $("#index_row_"+status_save).offset().top
                }, 0);
                const baris = status_save+1;
                swal("","Interval data kosong pada baris ke-"+baris,"error");
                return;
            }
            window.location.href = "/maut?step=2";
        }

        const ubahInterval = (index) => {
            const {interval} = edit_data;
            const data = interval[index];
            if(data === undefined) alert("data not found")
            for(key in data){
                const val = data[key];
                if(key == "tipe"){
                    $("#form_interval_editor select[name=tipe]").val(val).change()
                }
                $("#form_interval_editor input[name="+key+"]").val(val)
            }
            $("#form_interval_editor input[name=index]").val(index)
            $("#interval_editor").text("Baris ke-"+(index+1))
        }

        const tambahInterval = () => {
            $("#form_interval_editor input").val("");
            $("#form_interval_editor input[name=index]").val("-1")
            $("#form_interval_editor input[name=id]").val("0")
            $("#interval_editor").text("Tambah data")
        }

        $("#tipe_interval_input").change(function(){
            if($(this).val() === "interval") {
                $("#kondisi").hide();
                $(".interval_input").show();

                $("#kondisi input").removeAttr("required");
                $(".interval_input input").attr("required",true);
            } else {
                $("#kondisi").show();
                $(".interval_input").hide();

                $(".interval_input input").removeAttr("required");
                $("#kondisi input").attr("required",true);
            }
        })

        $("#form_interval_editor").validate({
            submitHandler:function(form){
                let data = getFormData($("#form_interval_editor"))
                let {interval} = edit_data;
                data.minimum = parseFloat(data.minimum)
                data.maximum = parseFloat(data.maximum)
                if(data.index >=0){
                    interval[data.index] = data;
                } else {
                    interval.push(data);
                    tambahInterval();
                }
                edit_data.interval = interval;
                // data[edit_data_index] = edit_data;
                LoadInterval();
                return false;
            }
        })

        const hapusInterval = (index) => {
            let {interval} = edit_data;
            interval[index].delete = 1;
            edit_data.interval = interval;
            // data[edit_data_index] = edit_data;
            LoadInterval();
        }

        function getFormData($form){
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};

            $.map(unindexed_array, function(n, i){
                indexed_array[n['name']] = n['value'];
            });

            return indexed_array;
        }

    </script>
@endpush