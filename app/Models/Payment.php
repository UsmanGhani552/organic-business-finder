<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory,ImageUploadTrait;

    protected $fillable = [
        'name',
        'icon'
    ];

    public static function storePayment(array $data): void {
        $payment = new self;
        $data['icon'] = $payment->uploadImage(request(), 'icon', 'images/farm/payment');
        Payment::create($data);
    }

    public static function updatePayment(array $data,$payment) {
        // dd($data['icon']);
        $data['icon'] = $payment->uploadImage(request(), 'icon', 'images/farm/payment',"images/farm/payment/{$payment->icon}",$payment->icon);
        $payment->update($data);
    }
}
