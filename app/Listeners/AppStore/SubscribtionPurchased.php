<?php

namespace App\Listeners\AppStore;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Imdhemy\Purchases\Events\AppStore\Subscribed;

class SubscribtionPurchased implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(Subscribed $event): void
    {
        try {
            $notification = $event->getServerNotification();
            $subscription = $notification->getSubscription();
            
            Log::info('✅ Apple Subscription Active:', [
                'type' => get_class($event),
                'notification' => $subscription
            ]);

            // Update your database with the subscription information
            // You can use the Subscription model to update the user's subscription status
            // Example:
            // \App\Models\Subscription::where('transaction_id', $subscription->getTransactionId())
            //     ->update(['status' => 1, 'expires_date' => $subscription->getExpiresDate()]);
            
        } catch (\Exception $e) {
            Log::error('Error processing Apple subscription event: ' . $e->getMessage(), [
                'exception' => $e,
                'event' => $event
            ]);
        }
    }
}
