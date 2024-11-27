<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SaveFarmRequest;
use App\Http\Requests\Api\StoreFarmRequest;
use App\Http\Requests\Api\UpdateFarmRequest;
use App\Models\Category;
use App\Models\Farm;
use Exception;
use Illuminate\Http\Request;
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
            $farm = Farm::updateFarm($validated_data, $farm);
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
            $user = auth::user();
            $farms = $user->savedFarms()
                ->with('categories', 'days', 'payments', 'products')
                ->get();


            $farmArray = Farm::getFarmRelatedData($farms);

            return response()->json([
                'status_code' => 200,
                'farms' => $farmArray,
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

    public function getCategories()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'status_code' => 200,
                'categories' => $categories,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getFeaturedFarms(Request $request)
    {
        try {
            $farm_name = $request->query('farm_name');
            $category_id = $request->query('category_id');
            $user = auth()->user();
            $userId = $user ? $user->id : null; // Null if no user is logged in

            $farms = Farm::with('categories', 'days', 'payments', 'products')
                ->selectRaw("
                farms.*,
                CASE 
                    WHEN saved_farms.user_id = ? THEN 1
                    ELSE 0
                END AS is_save
            ", [$userId]) // Dynamically set userId for the query
                ->leftJoin('saved_farms', function ($join) use ($userId) {
                    $join->on('farms.id', '=', 'saved_farms.farm_id')
                        ->where('saved_farms.user_id', '=', $userId);
                });

            // Apply farm name filter if provided
            if ($farm_name) {
                $farms->where('farms.name', 'LIKE', "%$farm_name%");
            }

            // Apply category filter if provided
            if ($category_id) {
                $farms->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            }

            // Get the filtered and processed farms
            $farms = $farms->get();
            $farmArray = Farm::getFarmRelatedData($farms);
            return response()->json([
                'status_code' => 200,
                'farms' => $farmArray,
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


    public function getNearByFarms(Request $request)
    {
        try {
            $latitude = $request->query('latitude');
            $longitude = $request->query('longitude');
            $user = auth()->user();
            $userId = $user ? $user->id : null; // Null if no user is logged in
            $farms = Farm::with('categories', 'days', 'payments', 'products')
                ->selectRaw("
                farms.*,
                (6371 * acos(cos(radians($latitude)) * 
                cos(radians(farms.lat)) * 
                cos(radians(farms.lng) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(farms.lat)))) AS distance,
                CASE 
                    WHEN saved_farms.user_id = ? THEN 1
                    ELSE 0
                END AS is_save
            ", [$userId]) // Pass the user ID to the query
                ->leftJoin('saved_farms', function ($join) use ($userId) {
                    $join->on('farms.id', '=', 'saved_farms.farm_id')
                        ->where('saved_farms.user_id', '=', $userId);
                })
                ->having("distance", "<", 10000)
                ->orderBy("distance")
                ->get();
            $farmArray = Farm::getFarmRelatedData($farms);
            return response()->json([
                'status_code' => 200,
                // 'farms' => $farms,
                'farms' => $farmArray,
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

    public function toggleSavedFarm(SaveFarmRequest $request)
    {
        try {
            $user = auth()->user();
            $save = $request->save;
            Farm::toggleSavedFarm($request->validated(), $user, $save);
            $message = $save ? 'Farm saved successfully.' : 'Farm removed from saved list.';
            return response()->json([
                'status_code' => 200,
                'message' => $message,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getSavedFarms()
    {
        try {
            $user = auth()->user();
            $farms = $user->savedFarms()
                ->with('categories', 'days', 'payments', 'products')
                ->get()
                ->groupBy(function ($farm) {
                    return $farm->categories->first()->name ?? 'Uncategorized';
                });
            $farmArray = Farm::getFarmRelatedData($farms, 2);
            return response()->json([
                'status_code' => 200,
                'farms' => $farmArray,
                // 'farms' => $farms,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function deleteFarm(Farm $farm)
    {
        try {
            DB::beginTransaction();
            $farm->delete();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Farm Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
