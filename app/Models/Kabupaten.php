<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $fillable = ["name"];

    public function Wilayah()
    {
        return $this->hasMany(Wilayah::class,"kabupaten_id","id");
    }
}
