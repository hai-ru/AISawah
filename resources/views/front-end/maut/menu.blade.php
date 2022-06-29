<div class="card-header">
    <ul class="nav nav-tabs card-header-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link {{!Request::has("step") || Request::get("step") == 1 ? "active" : "disabled"}}" href="/maut?step=1">Langkah 1 (Kriteria)</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link {{Request::get("step") == 2 ? "active" : "disabled"}}" href="/?step=2">Langkah 2 (Data Wilayah)</a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link {{Request::get("step") == 2 ? "active" : "disabled"}}" href="/maut?step=2">Langkah 2 (Data Alternatif)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::get("step") == 3 ? "active" : "disabled"}}" href="/maut?step=3">Langkah 3 (Hasil Analisa)</a>
        </li>
    </ul>
</div>