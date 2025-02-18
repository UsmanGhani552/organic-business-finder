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
        'delivery_option_id',
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
        // dd($farms[7]);
        $farmArrays = $farms->toArray();
        $relation = $key === 1 ? [$farmArrays] : $farmArrays;
        foreach ($relation as &$value) {
            foreach ($value as &$farm) {
                $farm['categories'] = Arr::pluck($farm['categories'], 'name');
                $days = [];
                $daysname = Arr::pluck($farm['days'], 'name');
                $daysPivot = Arr::pluck($farm['days'], 'pivot');
                // dd($daystimings);
                for ($i = 0; $i < count($daysname); $i++) {
                    $days[$i] = [
                        'name' => $daysname[$i],
                        'timings' => $daysPivot[$i]['timings'],
                        'location' => $daysPivot[$i]['location'],
                        'lat' => $daysPivot[$i]['lat'],
                        'lng' => $daysPivot[$i]['lng'],
                    ];
                }
                $farm['days'] = $days;
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
        $farm->categories()->attach($data['categories']);
        $farm->payments()->attach($data['payments']);
        $farm->services()->attach($data['services']);

        foreach ($data['days'] as $day) {
            $farm->days()->attach($day['day_id'], ['timings' => $day['timings'], 'location' => $day['location'], 'lat' => $day['lat'], 'lng' => $day['lng']]);
        }
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

        // dd($data);
        $farm->update(self::farmData($data));

        if (isset($data['categories'])) {
            $farm->categories()->sync($data['categories']);
        }

        if (isset($data['days'])) {
            $days = [];
            foreach ($data['days'] as $day) {
                $days[$day['day_id']] = ['timings' => $day['timings'], 'location' => $day['location'], 'lat' => $day['lat'], 'lng' => $day['lng']];
            }
            $farm->days()->sync($days);
        }

        if (isset($data['payments'])) {
            $farm->payments()->sync($data['payments']);
        }
        if (isset($data['services'])) {
            $farm->services()->sync($data['services']);
        }
        if (isset($data['products'])) {
            // dd('asd');
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
            'name' => $data['name'] ?? null,
            'image' => $data['image'] ?? null,
            'location' => $data['location'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'phone' => isset($data['phone']) ? json_encode($data['phone']) : null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'delivery_option_id' => $data['delivery_option_id'] ?? null,
            'description' => $data['description'] ?? null,
            'timings' => $data['timings'] ?? null,
        ];
    
        // Remove all null values to avoid updating fields to NULL
        return array_filter($farmArray, fn($value) => !is_null($value));
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
        return $this->belongsToMany(Day::class, 'farm_days')->withPivot('timings', 'location', 'lat', 'lng');
    }
    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'farm_payments');
    }
    public function delivery_option()
    {
        return $this->belongsTo(DeliveryOption::class, 'delivery_option_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'farm_service');
    }
}
