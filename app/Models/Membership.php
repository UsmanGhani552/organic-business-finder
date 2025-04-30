<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name' , 'price' , 'description'];

    protected $casts = [
        'description' => 'array'
    ];

    public static function storeMembership($data) {
        self::create($data);
    }

    public function updateMembership($data) {
        $this->update($data);
        
    }
}
