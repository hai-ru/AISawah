<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;
    protected $fillable = [
        "input",
        "formula",
        "output",
        "hasil_bobot",
        "tematik_id",
        "kriteria_id",
        "wilayah_id",
        "tahun",
        "input_type",
        "normalisasi",
        "alternatif",
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
