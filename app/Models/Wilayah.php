<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;
    protected $fillable = ["name","kabupaten_id","parent_id"];

    public function Nilai()
    {
        return $this->hasMany(Nilai::class,"wilayah_id","id");
    }

    public function Kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
}
