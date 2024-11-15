<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Farm extends Model
{
    use HasFactory, ImageUploadTrait;

    protected $table = 'farms';
    protected $fillable = [
        'user_id',
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
        // dd($data);
        $farm = new self;
        $data['image'] = $farm->uploadImage($data['request'], 'image', 'farm');

        $farm = self::create(self::farmData($data));

        foreach ($data['categories'] as $category_id) {
            FarmCategory::create([
                'farm_id' => $farm->id,
                'category_id' => $category_id
            ]);
        }
        foreach ($data['days'] as $day_id) {
            FarmDay::create([
                'farm_id' => $farm->id,
                'day_id' => $day_id
            ]);
        }
        foreach ($data['payments'] as $payment_id) {
            FarmPayment::create([
                'farm_id' => $farm->id,
                'payment_id' => $payment_id
            ]);
        }
        // dd($farm);
        foreach ($data['products'] as $index => $product) {
            $product['image'] = $farm->uploadImage($data['request'], "products.$index.image", 'product');
            Product::create([
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'farm_id' => $farm->id,
            ]);
        }

        return $farm;
    }
    public static function updateFarm(array $data, $farm)
    {
        $data['image'] = $farm->uploadImage($data['request'], 'image', 'farm', "farm/{$farm->image}", $farm->image);
        // dd($data['image']);

        $farm->update(self::farmData($data));

        if ($data['categories']) {
            $farm->categories()->sync($data['categories']);
        }

        if ($data['days']) {
            $farm->days()->sync($data['days']);
        }

        if ($data['payments']) {
            $farm->payments()->sync($data['payments']);
        }

        if ($data['products']) {
            foreach ($data['products'] as $index => $product) {
                // dd($data['products']);
                $existingProduct = Product::where('farm_id', $farm->id)
                    ->where('name', $product['name'])
                    ->first();

                $product['image'] = $farm->uploadImage($data['request'], "products.$index.image", 'product', "product/{$existingProduct->image}", $existingProduct->image);

                // Use updateOrCreate to avoid duplicate entries
                Product::updateOrCreate(
                    ['farm_id' => $farm->id, 'name' => $product['name']],
                    [
                        'price' => $product['price'],
                        'image' => $product['image']
                    ]
                );
            }
        }
        return $farm;
    }
    public static function toggleSavedFarm(array $data, $user, $save): void
    {
        if ($save) {
            $user->savedFarms()->syncWithoutDetaching([$data['farm_id']]);
        } else {
            $user->savedFarms()->detach([$data['farm_id']]);
        }
    }
    public static function farmData($data)
    {
        $farmArray = [
            'user_id' => auth()->user()->id,
            'name' => $data['name'],
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
        ];
        return $farmArray;
    }
    public function syncCategories(array $categories)
    {
        $this->categories()->sync($categories);
    }

    public function syncDays(array $days)
    {
        $this->days()->sync($days);
    }

    public function syncPayments(array $payments)
    {
        $this->payments()->sync($payments);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'farm_categories');
    }
    public function days()
    {
        return $this->belongsToMany(Day::class, 'farm_days');
    }
    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'farm_payments');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
