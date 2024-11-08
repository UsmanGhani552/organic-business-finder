<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFarmRequest;
use App\Http\Requests\Api\UpdateFarmRequest;
use App\Models\Farm;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
                'message' => 'Farm Stored Successfully',
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
    public function updateFarm(UpdateFarmRequest $request, Farm $farm)
    {
        try {
            DB::beginTransaction();
            $validated_data = array_merge($request->validated(), ['request' => $request]);
            $farm = Farm::updateFarm($validated_data,$farm);
            $farm->load('products');
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Farm Updated Successfully',
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
            $farms = Farm::with('categories','days', 'payments', 'products')->where('user_id',Auth::user()->id)->get();
            $farmArray = $farms->toArray();

            foreach ($farmArray as &$farm) {
                $farm['categories'] = Arr::pluck($farm['categories'], 'name');
                $farm['days'] = Arr::pluck($farm['days'], 'name');
                $farm['payments'] = Arr::pluck($farm['payments'], 'name');
            }

            return response()->json([
                'status_code' => 200,
                'farm' => $farmArray,
                'base_url_farms' => asset('farm'),
                'base_url_products' => asset('product'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
