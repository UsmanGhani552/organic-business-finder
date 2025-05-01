<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_transaction_id',
        'transaction_id',
        'product_id',
        'platform',
        'transaction_receipt',
        'status',
        'auto_renew_status',
        'expires_date'
    ];

    public static function storeSubscription($data)
    {
        Subscription::updateOrCreate(
            ['transaction_id' => $data['transaction_id']],
            $data
        );
        $user = User::find($data['user_id']);
        if ($user) {
            $user->startSubscription();
        }
    }

    public static function changeStatus($subscription, $status, $autoRenewStatus)
    {
        $subscription->update([
            'status' => $status,
            'auto_renew_status' => $autoRenewStatus,
        ]);
        if($subscription->status == 2) {
            $user = User::find($subscription->user_id);
            if ($user) {
                $user->stopSubscription();
            }
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
