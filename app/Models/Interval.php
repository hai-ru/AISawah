<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interval extends Model
{
    use HasFactory;
    protected $fillable = [
        "minimum",
        "maximum",
        "bobot",
        "condition",
        "kriteria_id",
        "tipe",
    ];

    public function type()
    {
        if(empty($this->minimum) && empty($this->maximum)){
            return "condition";
        } else {
            return "interval";
        }
    }

    public function Kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
