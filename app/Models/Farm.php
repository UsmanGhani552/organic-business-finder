<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory, ImageUploadTrait;
    protected $fillable = [
        'category',
        'name',
        'location',
        'lat',
        'lng',
        'phone',
        'email',
        'website',
        'description',
        'days',
        'timings',
        'delivery_option',
        'payment',
        'image',
    ];

    public static function storeFarm(array $data)
    {
        $farm = new self;
        $data['image'] = $farm->uploadImage($data['request'], 'image', 'user/images');
        
        $farm = self::create([
            'name' => $data['name'],
            'category' => $data['category'],
            'image' => $data['image'],
            'location' => $data['location'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'website' => $data['website'],
            'delivery_option' => $data['delivery_option'],
            'description' => $data['description'],
            'timings' => $data['timings'],
        ]);
        foreach ($data['days'] as $day_id) {
            FarmDay::create([
                'farm_id' => $farm->id,
                'day_id' => $day_id
            ]);
        }
        foreach ($data['payments'] as $payment_id) {
            Farmpayment::create([
                'farm_id' => $farm->id,
                'payment_id' => $payment_id
            ]);
        }
        // dd($farm);
        foreach ($data['products'] as $index => $product) {
            $product['image'] = $farm->uploadImage($data['request'], "products.$index.image", 'product/images');
            Product::create([
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'farm_id' => $farm->id,
            ]);
        }

        return $farm;
    }


    public function days()
    {
        return $this->belongsToMany(Day::class, 'farm_days');
    }
    public function payments(){
        return $this->belongsToMany(Payment::class,'farm_payments');
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
}
