<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubscriptionRequest;
use App\Models\Membership;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Services\GooglePlayService;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use Imdhemy\AppStore\Receipts\Verifier as AppStore;
use Imdhemy\Purchases\Facades\Subscription as PurchasesSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Imdhemy\AppStore\ClientFactory;
use Imdhemy\Purchases\Facades\Product;
use Google\Client;
use Google\Service\AndroidPublisher;

class SubscriptionController extends Controller
{
    public function isBase64($str)
    {
        return base64_encode(base64_decode($str, true)) === $str;
    }
    public function storeSubscription(StoreSubscriptionRequest $request)
    {
        try {
            if ($request->platform === 'apple') {
                $subscription = $this->validateIos($request->all());
                Subscription::storeSubscription($subscription);
            } else {
                $subscription = $this->validateGoogle($request->all());
                Subscription::storeGoogleSubscription($subscription);
                // return response()->json(['message' => 'Google not implemented yet'], 200);
            }

            return response()->json(['message' => 'Subscription stored successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Subscription could not be stored', 'error' => $th->getMessage()], 500);
        }
    }

    public function validateIos($data)
    {
        $receipt = $data['receipt'];
        $receiptResponse = PurchasesSubscription::appStore()->receiptData($receipt)->verifyReceipt();
        $receiptStatus = $receiptResponse->getStatus();
        if ($receiptStatus->isValid()) {
            $latestReceiptInfo = $receiptResponse->getLatestReceiptInfo();
            $receiptInfo = $latestReceiptInfo[0];
            // You can loop all of them or either get the first one (recently purchased).
            $expiresDate = $receiptInfo->getExpiresDate()->toDateTime();
            $data = [
                'user_id' => auth()->user()->id,
                'original_transaction_id' => $receiptInfo->getOriginalTransactionId(),
                'transaction_id' => $receiptInfo->getTransactionId(),
                'product_id' => $receiptInfo->getProductId(),
                'platform' => $data['platform'],
                'transaction_receipt' => $receipt,
                'status' => 1,
                'subscription_status' => 1,
                'auto_renew_status' => 1,
                'expires_date' => $expiresDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            // dd($data);
            return $data;
        } else {
            return response()->json(['message' => 'Receipt is invalid'], 500);
        }
    }

    public function validateGoogle($data)
    {
        try {
            $productReceipt = PurchasesSubscription::googlePlay()
                ->id(env('GOOGLE_PLAY_PRODDUCT_ID'))
                ->token($data['receipt'])               // this must be the actual client-side token
                ->get();
            return [
                'user_id' => auth()->user()->id,
                'original_transaction_id' => $productReceipt->getOrderId(),
                'transaction_id' => $productReceipt->getOrderId(),
                'product_id' => env('GOOGLE_PLAY_PRODDUCT_ID'),
                'platform' => 'google',
                'transaction_receipt' => $data['receipt'],
                'status' => 1,
                'subscription_status' => 1,
                'auto_renew_status' => 1,
                'expires_date' => Carbon::createFromTimestampMs(
                    $productReceipt->getExpiryTimeMillis()
                ),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        } catch (\Exception $e) {
            throw new \Exception('Google Play validation failed: ' . $e->getMessage());
        }
    }

    public function getSubscription()
    {
        try {
            $user_id = auth()->user()->id;
            $subscription = Subscription::where('user_id', $user_id)->first();
            if (!$subscription) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No Subscription found'
                ], 404);
            }
            // $response = $this->changeSubscriptionStatus($subscription);
            if ($subscription->platform === 'ios') {
                $response = $this->changeAppleSubscriptionStatus($subscription);
            } elseif ($subscription->platform === 'google') {
                $response = $this->changeGoogleSubscriptionStatus($subscription);
            } else {
                return response()->json(['message' => 'Unsupported platform'], 400);
            }
            return $response;
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function changeSubscriptionStatus(Subscription $subscription)
    {
        try {
            $token = $this->generateAppStoreJWT();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->get("https://api.storekit-sandbox.itunes.apple.com/inApps/v1/subscriptions/{$subscription->transaction_id}");
            // dd($response->json());
            if ($response->failed()) {
                return response()->json(['message' => 'Failed to fetch subscription data from App Store'], 500);
            }
            $responseData = $response->json();
            $transaction = $response['data'][0]['lastTransactions'][0];
            $decodedRenewalInfo = $this->decodeJwtPayload($transaction['signedRenewalInfo']);
            $decodedTransactionInfo = $this->decodeJwtPayload($transaction['signedTransactionInfo']);

            // Extract status and autoRenewStatus
            $status = $transaction['status'] ?? null;
            $autoRenewStatus = $decodedRenewalInfo['autoRenewStatus'] ?? null;

            Subscription::changeStatus($subscription, $status, $autoRenewStatus);

            $responseData['data'][0]['lastTransactions'][0]['decodedRenewalInfo'] = $decodedRenewalInfo;
            $responseData['data'][0]['lastTransactions'][0]['decodedTransactionInfo'] = $decodedTransactionInfo;
            unset(
                $responseData['data'][0]['lastTransactions'][0]['signedTransactionInfo'],
                $responseData['data'][0]['lastTransactions'][0]['signedRenewalInfo']
            );
            // dd($responseData);
            return $responseData;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function changeGoogleSubscriptionStatus(Subscription $subscription)
    {
        try {
            $googlePlayService = new GooglePlayService();
            // These values should be configured in your config/services.php
            $packageName = env('GOOGLE_PLAY_PACKAGE_NAME');
            $subscriptionId = $subscription->product_id; // e.g., 'organic_monthly_platinum'
            $purchaseToken = $subscription->transaction_receipt;

            $subscriptionInfo = $googlePlayService->getSubscription(
                $packageName,
                $subscriptionId,
                $purchaseToken
            );
            // dd($subscriptionInfo);
            // Map Google's status to your system's status
            $status = $subscriptionInfo->paymentState == null ? 2 : 1;
            $autoRenewStatus = (bool)$subscriptionInfo->autoRenewing == false ? 0 : 1 ;
            // Update subscription in database
            Subscription::changeStatus($subscription, $status, $autoRenewStatus);

            return [
                'data' => [
                    [
                        'lastTransactions' => [
                            [
                                'subscriptionInfo' => $subscriptionInfo,
                                'paymentState' => $subscriptionInfo->paymentState,
                                'autoRenewing' => $subscriptionInfo->autoRenewing,
                                'expiryTime' => Carbon::createFromTimestampMs($subscriptionInfo->expiryTimeMillis),
                            ]
                        ]
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            throw new \Exception('Google Play subscription check failed: ' . $th->getMessage());
        }
    }

    protected function mapGoogleStatus($subscriptionInfo)
    {
        $currentTime = Carbon::now()->timestamp * 1000; // Current time in milliseconds

        // 1. First check if subscription is expired
        if ($subscriptionInfo->expiryTimeMillis < $currentTime) {
            return 2; // Expired
        }

        // 2. Check cancellation state
        if ($subscriptionInfo->cancelReason !== null || $subscriptionInfo->userCancellationTimeMillis !== null) {
            return 2; // Cancelled counts as expired
        }

        // 3. Check payment state if available
        if ($subscriptionInfo->paymentState !== null && $subscriptionInfo->paymentState !== 1) {
            return 2; // Only paymentState=1 is considered active
        }

        // 4. Default to active if none of the above conditions met
        return 1; // Active
    }

    public function generateAppStoreJWT()
    {
        // 1. Point directly to root directory
        $keyPath = base_path('appstore_private_key.pem'); // Using your actual key filename

        // 2. Read key contents
        $keyContent = file_get_contents($keyPath);

        $keyId = 'U27S2F95YA'; // Your Key ID from App Store Connect
        $issuerId = '87eea8d3-7b1c-44e1-bd15-768b4ebaa392'; // Your Issuer ID
        $expiry = time() + 1200; // 20 minutes expiration

        $payload = [
            'iss' => $issuerId,
            'iat' => time(),
            'exp' => $expiry,
            'aud' => 'appstoreconnect-v1',
            'bid' => 'com.organicproduce.com' // Your app bundle ID
        ];

        return JWT::encode($payload, $keyContent, 'ES256', $keyId);
    }

    public function generateAppStoreConnectJWT()
    {
        $keyPath = base_path('appstore_private_key.pem'); // Your .p8 private key
        $keyContent = file_get_contents($keyPath);

        if ($keyContent === false) {
            throw new Exception("Failed to read private key at: " . $keyPath);
        }

        if (!str_contains($keyContent, 'BEGIN PRIVATE KEY')) {
            throw new Exception("Invalid key format - should be PKCS#8 .p8 format");
        }

        $keyId = 'U27S2F95YA'; // Your Key ID
        $issuerId = '87eea8d3-7b1c-44e1-bd15-768b4ebaa392'; // Your Issuer ID
        $expiry = time() + (20 * 60); // 20 minutes from now

        $payload = [
            'iss' => $issuerId,
            'exp' => $expiry,
            'aud' => 'appstoreconnect-v1',
        ];

        return JWT::encode($payload, $keyContent, 'ES256', $keyId);
    }

    public function decodeJwtPayload($jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;

        $payload = $parts[1];
        $decoded = base64_decode(strtr($payload, '-_', '+/'));
        return json_decode($decoded, true);
    }

    public function getFreeTrial()
    {
        try {
            $user = auth()->user();
            if ($user->is_free_trial) {
                return response()->json(['message' => 'Free trial already started'], 400);
            }
            $user->startFreeTrial();
            return response()->json(['message' => 'Free trial started successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function getSubscriptionPlans()
    {
        try {
            $memberships = Membership::all();
            return response()->json([
                'status_code' => 200,
                'memberships' => $memberships
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }
}
