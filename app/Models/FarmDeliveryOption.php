<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmDeliveryOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'delivery_option_id',
    ];

    public function deliveryOption(){
        return $this->belongsToMany(DeliveryOption::class,'farm_id');
    }
}
