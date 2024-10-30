<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'day_id',
    ];

    public function day(){
        return $this->belongsToMany(Day::class,'farm_id');
    }
}
