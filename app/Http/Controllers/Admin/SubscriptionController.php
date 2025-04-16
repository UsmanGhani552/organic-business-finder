<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::all();
        return view('admin.subscription.index',[
            'subscriptions' => $subscriptions
        ]);
    }
    public function create()
    {
        return view('admin.subscription.create');
    }
    public function edit()
    {
        return view('admin.subscription.edit');
    }
    public function show()
    {
        return view('admin.subscription.show');
    }
}
