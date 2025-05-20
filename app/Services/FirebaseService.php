<?php

namespace App\Services;

use App\Models\Notification as ModelsNotification;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\Messaging\NotFound;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path('firebase-adminsdk.json'));
        $this->messaging = $firebase->createMessaging();
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        try {
            $notification = Notification::create($title, $body);
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data);

            $response = $this->messaging->send($message);

            return [
                'success' => true,
                'response' => $response,
            ];
        } catch (NotFound $e) {
            return [
                'success' => false,
                'message' => 'The device token is not recognized. It might have been unregistered or registered to a different Firebase project.',
                'error' => $e->getMessage(),
            ];
        } catch (Exception $e) {
            Log::error('Error sending Firebase notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while sending the notification.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function sendNotificationToMultipleDevices($deviceTokens, $title, $body, $imageUrl = null, $data = [])
    {
        try {
            // dd('asd');
            $notification = Notification::create($title, $body, asset('notifications/'.$imageUrl));
            $messages = [];
            $uniqueUsers = $deviceTokens->unique('user_id');
            foreach ($deviceTokens as $deviceToken) {
                // dd($deviceToken['fcm_token']);
                $messages[] = CloudMessage::withTarget('token', $deviceToken['fcm_token'])
                    ->withNotification($notification)
                    ->withData($data);
            }
            // dd($messages);
            foreach ($uniqueUsers as $user) {
                // dd($user->user_id);
                ModelsNotification::create([
                    'title' => $title,
                    'body' => $body,
                    'image' => $imageUrl,
                    'user_id' => $user->user_id
                ]);
            }

            // Send all messages
            $responses = $this->messaging->sendAll($messages);
            return [
                'success' => true,
                'responses' => $responses,
            ];
        } catch (NotFound $e) {
            return [
                'success' => false,
                'message' => 'The device token is not recognized. It might have been unregistered or registered to a different Firebase project.',
                'error' => $e->getMessage(),
            ];
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
