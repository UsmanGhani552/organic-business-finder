<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FirebaseService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notifications to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $title = "Daily Reminder";
            $body = "Discover fresh produce near you. Check out latest listings now!";
            $users = User::where('type', 'visitor')->get(); 
            // dd($users);
            foreach ($users as $user) {
                $deviceTokens = $user->deviceTokens;
                $imageUrl = $user->image;
                $firebaseService = app(FirebaseService::class);
                $res = $firebaseService->sendNotificationToMultipleDevices($deviceTokens, $title, $body, $imageUrl);
                // dd($res);
            };
        } catch (Exception $e) {
            Log::error('Error sending Firebase notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while sending the notification.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
