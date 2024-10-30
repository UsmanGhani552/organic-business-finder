<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFarmRequest;
use App\Models\Farm;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FarmController extends Controller
{
    public function storeFarm(StoreFarmRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated_data = array_merge($request->validated(), ['request' => $request]);
            $farm = Farm::storeFarm($validated_data);
            DB::commit();
            $farm->load('products');
            return response()->json([
                'status_code' => 200,
                'farm' => $farm,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getFarms()
    {
        try {
            $farms = Farm::with('days', 'payments', 'products')->get();
            $farmArray = $farms->toArray();

            foreach ($farmArray as &$farm) {
                $farm['days'] = Arr::pluck($farm['days'], 'name');
                $farm['payments'] = Arr::pluck($farm['payments'], 'name');
            }

            return response()->json([
                'status_code' => 200,
                'farm' => $farmArray,
                'base_url_users' => asset('user/images'),
                'base_url_products' => asset('product/images'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
