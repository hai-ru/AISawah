<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "formula",
        "symbol",
        "bobot_preferensi",
        "type",
        "tematik_id"
    ];

    public function tematik()
    {
        return $this->belongsTo(Tematik::class);
    }

    public function Nilai()
    {
        return $this->hasMany(Nilai::class,"tematik_id","id");
    }

    public function Interval()
    {
        return $this->hasMany(Interval::class,"kriteria_id","id");
    }

}
