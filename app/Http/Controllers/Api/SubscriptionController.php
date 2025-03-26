<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubscriptionRequest;
use App\Models\Subscription;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Imdhemy\AppStore\Receipts\Verifier as AppStore;
use Imdhemy\Purchases\Facades\Subscription as AppleSubscription;
use Illuminate\Http\Request;
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
        $user_id = auth()->user()->id;
        $subscriptions = Subscription::where('user_id', $user_id)->get();
        return response()->json($subscriptions, 200);
    }
}
