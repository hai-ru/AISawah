<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hasil Import</title>
    <style>
        table, td, th {
            border: 1px solid black;
            padding: 5px 10px;
        }

        table {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }
        .top{
            background: yellow;
        }
        .hit{
            /* float: right; */
        }
        .text-center{
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h1>AI MADM <br/> Simple Additive Weighting (SAW)</h1>
    </div>
    <form>
        <label>Filter Data</label>
        <select name="tematik_id">
            <option value="" selected>--Pilih Tematik --</option>
            @foreach ($tematik as $item)
                <option value="{{ $item->id }}" {{ Request::get("tematik_id") == $item->id ? "selected" : "" }} >{{ $item->name }}</option>
            @endforeach
        </select>
        <select name="kabupaten_id">
            <option value="" selected>--Pilih Kabupaten --</option>
            @foreach ($kabupaten as $item)
                <option value="{{ $item->id }}" {{ Request::get("kabupaten_id") == $item->id ? "selected" : "" }} >{{ $item->name }}</option>
            @endforeach
        </select>
        <select name="tahun">
            <option value="" selected>--Pilih Tahun --</option>
            @foreach ($tahun as $item)
                <option value="{{ $item->tahun }}" {{ Request::get("tahun") == $item->tahun ? "selected" : "" }} >{{ $item->tahun }}</option>
            @endforeach
        </select>
        <input name="tipe" type="submit" value="submit">
        @if (
            Request::has("tematik_id") && 
            Request::has("tahun") && 
            Request::has("kabupaten_id") &&
            !$data->isEmpty()
        )
            <input name="tipe" type="submit" value="reset">
        @endif
        <input name="tipe" type="submit" value="import">
    </form>

    <h3>1. Data Alternatif</h3>
    <table>
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
            @foreach ($data as $key => $item)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{$item[0]->wilayah->name}}</td>
                    @foreach ($item as $i => $val)
                        <td>{{$val->input}}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>2. Rating Kecocokan Alternatif</h3>
    <table>
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
            @foreach ($data as $key => $item)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{$item[0]->wilayah->name}}</td>
                    @foreach ($item as $i => $val)
                        <td>{{$val->hasil_bobot}}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>3. Hasil Analisa</h3>
    <table>
        <thead>
            <tr>
                <th width="10">Ranking</th>
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
            @foreach ($ranking as $key => $item)
                    <tr class="{{$key == 0 ? "top" : ""}}">
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

    <h3>Kriteria</h3>
    <table>
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Inisial</th>
                <th>Bobot Preferensi (100%) </th>
                <th>Parameter & Bobot</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;    
            @endphp
            @foreach ($kriteria as $x => $item)
                @php
                    $total += $item[0]->kriteria->bobot_preferensi; 
                @endphp
                <tr>
                    <td>{{ $item[0]->kriteria->name }}</td>
                    <td class="text-center">{{ strtoupper($item[0]->kriteria->symbol) }}</td>
                    <td>{{ $item[0]->kriteria->bobot_preferensi }}</td>
                    <td>
                        <ul>
                            @foreach ($item[0]->kriteria->interval as $key => $val)
                                @if($val->type() == "condition")
                                    <li>{{$val->condition}} = {{ $val->bobot }}</li>
                                @else
                                    <li>{{$val->minimum}} - {{$val->maximum}} = {{ $val->bobot }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td>{{$total}}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>