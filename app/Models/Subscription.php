<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'product_id',
        'platform',
        'transaction_receipt',
        'status',
        'auto_renew_status',
        'expires_date'
    ];

    public static function storeSubscription($data) {
        self::updateOrcreate(
            ['user_id' => $data['user_id']],
            $data);
    }

    public static function changeStatus($subscription,$status,$autoRenewStatus) {
        $subscription->update([
            'status' => $status,
            'auto_renew_status' => $autoRenewStatus,
        ]);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }    
}
