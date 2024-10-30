<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Day;
use App\Models\FarmPayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $payment_methods = ['mastercard','visa','paypal','bitcoin'];

        foreach ($payment_methods as $payment_method){
            Payment::create(['name' => $payment_method]);
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($days as $day) {
            Day::create(['name' => $day]);
        }
        
    }
}
