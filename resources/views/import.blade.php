<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Import Data</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data" action="{{ route("import") }}">
        @csrf
        <select name="formula" required>
            <option value="" selected>--Pilih Metode --</option>
            <option value="saw">SAW</option>
            <option value="maut">MAUT</option>
            {{-- <option value="irap">IRAP</option> --}}
        </select>
        <select name="tematik_id" required>
            <option value="" selected>--Pilih Tematik --</option>
            @foreach (\App\Models\Tematik::orderBy("id","desc")->get() as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <select name="kabupaten_id" required>
            <option value="" selected>--Pilih Kabupaten --</option>
            @foreach (\App\Models\Kabupaten::orderBy("id","desc")->get() as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <input type="text" placeholder="Tahun" name="tahun" required />
        <input type="file" name="import_file" required />
        <button type="submit">Submit</button>
    </form>
</body>
</html>