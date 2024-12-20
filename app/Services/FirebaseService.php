<?php

namespace App\Services;

use App\Models\Notification as ModelsNotification;
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
        } catch (\Exception $e) {
            Log::error('Error sending Firebase notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while sending the notification.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function sendNotificationToMultipleDevices(array $deviceTokens, $title, $body, $userId, $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            $messages = [];
            foreach ($deviceTokens as $deviceToken) {
                $messages[] = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification($notification)
                    ->withData($data);
            }

            // Send all messages
            $responses = $this->messaging->sendAll($messages);
            ModelsNotification::create([
                'title' => $title,
                'body' => $body,
                'user_id' => $userId
            ]);
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
        } catch (\Exception $e) {
            Log::error('Error sending Firebase notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while sending the notification.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
