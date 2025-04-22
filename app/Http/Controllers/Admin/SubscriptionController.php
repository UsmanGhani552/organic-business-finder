<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {
            $response = Http::withToken(auth()->user()->api_token) // Include the auth token
            ->get(url('/api/get-subscription'), [
                'user_id' => $subscription->user_id,
            ]);
        }

        return view('admin.subscription.index',[
            'subscriptions' => $subscriptions
        ]);
    }
}
