<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tematik extends Model
{
    use HasFactory;
    protected $fillable = ["name"];

    public function kriteria()
    {
        return $this->hasMany(Kriteria::class,"tematik_id","id");
    }
}
