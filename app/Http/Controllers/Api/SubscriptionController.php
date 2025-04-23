<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubscriptionRequest;
use App\Models\Subscription;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Imdhemy\AppStore\Receipts\Verifier as AppStore;
use Imdhemy\Purchases\Facades\Subscription as AppleSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Imdhemy\AppStore\ClientFactory;
use Imdhemy\Purchases\Facades\Product;

class SubscriptionController extends Controller
{
    public function isBase64($str)
    {
        return base64_encode(base64_decode($str, true)) === $str;
    }
    public function storeSubscription(StoreSubscriptionRequest $request)
    {
        // $current = Carbon::now() + 1200;
        // $expires_at = 1743059898;
        try {
            if ($request->platform === 'apple') {
                $subscription = $this->validateIos($request->all());
                Subscription::storeSubscription($subscription);
            } else {
                return response()->json(['message' => 'Google not implemented yet'], 200);
            }

            return response()->json(['message' => 'Subscription stored successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Subscription could not be stored', 'error' => $th->getMessage()], 500);
        }
    }

    public function validateIos($data)
    {
        $receipt = $data['receipt'];
        $receiptResponse = AppleSubscription::appStore()->receiptData($receipt)->verifyReceipt();
        $receiptStatus = $receiptResponse->getStatus();
        if ($receiptStatus->isValid()) {
            $latestReceiptInfo = $receiptResponse->getLatestReceiptInfo();
            $receiptInfo = $latestReceiptInfo[0];
            // You can loop all of them or either get the first one (recently purchased).
            $expiresDate = $receiptInfo->getExpiresDate()->toDateTime();

            $data = [
                'user_id' => auth()->user()->id,
                'transaction_id' => $receiptInfo->getTransactionId(),
                'product_id' => $receiptInfo->getProductId(),
                'platform' => $data['platform'],
                'transaction_receipt' => $receipt,
                'status' => 1,
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

    public function getSubscription()
    {
        try {
            $user_id = auth()->user()->id;
            $subscription = Subscription::where('user_id', $user_id)->first();
            $this->changeSubscriptionStatus($subscription);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function changeSubscriptionStatus(Subscription $subscription)
    {
        try {
            // dd($subscription);
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
            return response()->json($responseData, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function generateAppStoreJWT()
    {
        // 1. Point directly to root directory
        $keyPath = base_path('appstore_private_key.pem'); // Using your actual key filename

        // 2. Read key contents
        $keyContent = file_get_contents($keyPath);

        if ($keyContent === false) {
            throw new Exception("Failed to read private key at: " . $keyPath);
        }

        // 3. Verify key format
        if (!str_contains($keyContent, 'BEGIN PRIVATE KEY')) {
            throw new Exception("Invalid key format - should be PKCS#8 .p8 format");
        }
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

    public function decodeJwtPayload($jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;

        $payload = $parts[1];
        $decoded = base64_decode(strtr($payload, '-_', '+/'));
        return json_decode($decoded, true);
    }

    public function handleNotification(Request $request)
    {
        Log::info('ASSN V2 Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
        ]);

        return response()->json(['success' => true]);
    }
}
