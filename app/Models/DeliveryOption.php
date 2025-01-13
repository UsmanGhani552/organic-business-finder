<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function storedeliveryOption(array $data): void {
        self::create($data);
    }

    public static function updatedeliveryOption(array $data,$deliveryOption) {
        $deliveryOption->update($data);
    }

}
