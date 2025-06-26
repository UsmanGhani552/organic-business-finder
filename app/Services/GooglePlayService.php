<?php

namespace App\Services;

use Google\Client;
use Google\Service\AndroidPublisher;
use Carbon\Carbon;

class GooglePlayService
{
    protected $client;
    protected $androidPublisher;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(env('GOOGLE_APPLICATION_CREDENTIALS'));
        $this->client->addScope(AndroidPublisher::ANDROIDPUBLISHER);
        $this->androidPublisher = new AndroidPublisher($this->client);
    }

    public function getSubscription($packageName, $subscriptionId, $purchaseToken)
    {
        try {
            return $this->androidPublisher->purchases_subscriptions->get(
                $packageName,
                $subscriptionId,
                $purchaseToken
            );
        } catch (\Exception $e) {
            throw new \Exception('Google Play API error: ' . $e->getMessage());
        }
    }
}