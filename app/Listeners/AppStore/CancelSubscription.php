<?php

namespace App\Listeners\AppStore;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Imdhemy\Purchases\Events\AppStore\Cancel;

class CancelSubscription implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(Cancel $event): void
    {
        try {
            $notification = $event->getServerNotification();
            
            Log::info('❌ App Store Cancel Subscription Event', [
                'notification' => $notification
            ]);

            // Update your database with the cancellation information
            // Example:
            // \App\Models\Subscription::where('transaction_id', $notification->getTransactionId())
            //     ->update(['status' => 0, 'cancelled_at' => $notification->getCancellationDate()]);
            
        } catch (\Exception $e) {
            Log::error('Error processing Apple cancellation event: ' . $e->getMessage(), [
                'exception' => $e,
                'event' => $event
            ]);
        }
    }
}
