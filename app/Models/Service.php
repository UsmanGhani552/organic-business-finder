<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name'
    ];

    public static function storeService(array $data): void {
        self::create($data);
    }

    public static function updateService(array $data , $service) {
        $service->update($data);
    }

    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'farm_service');
    }
}
