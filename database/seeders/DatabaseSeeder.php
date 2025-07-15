<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Day;
use App\Models\FarmPayment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $payment_methods = ['mastercard','visa','paypal','bitcoin'];
        // foreach ($payment_methods as $payment_method){
        //     Payment::create(['name' => $payment_method]);
        // }

        // $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        // foreach ($days as $day) {
        //     Day::create(['name' => $day]);
        // }
        
        // $categories = ['Vegitables','Fruits','Dairy'];
        // foreach ($categories as $category) {
        //     Category::create(['name' => $category]);
        // }
        // $services = ['Farm-to-table','CSA','U-Pick','Agro-tourism','Producer','Wholesaler'];
        // foreach ($services as $service) {
        //     Service::create(['name' => $service]);
        // }
        
        User::updateOrCreate(['email' => 'admin@gmail.com'],[
            'name' => 'admin',
            'password' => Hash::make('admin123'),
            'type' => 'admin'
        ]);
    }
}
