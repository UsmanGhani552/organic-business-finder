<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($farm) {
            $farm->products()->delete();
            $farm->categories()->detach();
            $farm->payments()->detach();
            $farm->days()->detach();
        });
    }

    public static function getFarmRelatedData($farms, $key = 1)
    {
        $farmArrays = $farms->toArray();
        $relation = $key === 1 ? [$farmArrays] : $farmArrays;
        foreach ($relation as &$value) {
            foreach ($value as &$farm) {
                $farm['categories'] = Arr::pluck($farm['categories'], 'name');
                $farm['days'] = Arr::pluck($farm['days'], 'name');
                $farm['payments'] = Arr::pluck($farm['payments'], 'name');
                if ($key == 2) {
                    $farm['is_save'] = $farm['pivot']['save'] ?? 0;
                }
            }
        }
        return $key === 1 ? $relation[0] : $relation;
    }

    public static function storeFarm(array $data)
    {
        Log::info('Farm data received', $data);
        // dd($data);
        $farm = new self;
        $data['image'] = $farm->uploadImage($data['request'], 'image', 'images/farm');

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
            $product['image'] = $farm->uploadImage($data['request'], "products.$index.image", 'images/product');
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
        $data['image'] = $farm->uploadImage($data['request'], 'image', 'images/farm', "images/farm/{$farm->image}", $farm->image);
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
                // dd($product);
                // Check if the product exists
                $existingProduct = Product::where('farm_id', $farm->id)
                    ->where('id', $product['id'])
                    ->first();
                    // Determine the old image path if the product exists, otherwise set it to null
                    $oldImagePath = $existingProduct ? "images/product/{$existingProduct->image}" : null;

                    // Upload the new image or keep the default
                    $product['image'] = $farm->uploadImage(
                        $data['request'],
                        "products.$index.image",
                        'images/product',
                        $oldImagePath,
                        $existingProduct->image ?? null
                    );

                    // Use updateOrCreate to update or insert the product
                    Product::updateOrCreate(
                        ['farm_id' => $farm->id, 'id' => $product['id'] ?? ''],
                        [
                            'name' => $product['name'],
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
            $user->savedFarms()->syncWithoutDetaching([
                $data['farm_id'] => ['save' => true],
            ]);
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

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
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
