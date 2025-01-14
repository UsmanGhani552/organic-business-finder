<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory,ImageUploadTrait;

    protected $fillable = [
        'name',
        'icon'
    ];

    public static function storeCategory(array $data): void {
        $category = new self;
        $data['icon'] = $category->uploadImage(request(), 'icon', 'images/farm/category');
        self::create($data);
    }

    public static function updateCategory(array $data,$category) {
        $data['icon'] = $category->uploadImage(request(), 'icon', 'images/farm/category',"images/farm/category/{$category->icon}",$category->icon);
        $category->update($data);
    }
}
