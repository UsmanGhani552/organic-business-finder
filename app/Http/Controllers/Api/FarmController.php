<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SaveFarmRequest;
use App\Http\Requests\Api\StoreFarmRequest;
use App\Http\Requests\Api\UpdateFarmRequest;
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
            $farms = Farm::with('categories', 'days', 'payments', 'products')->where('user_id', Auth::user()->id)->get();
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

    public function getFeaturedFarms(Request $request)
    {
        try {
            $farm_name = $request->query('farm_name');
            $category_id = $request->query('category_id');

            $farms = Farm::with('products');
            if ($farm_name) {
                $farms->where('name', 'LIKE', "%$farm_name%");
            }
            if ($category_id) {
                $farms->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            }
            $farms = $farms->get();
            return response()->json([
                'status_code' => 200,
                'farm' => $farms,
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

            $farms = Farm::with('products')
                ->selectRaw("
                *,
                (6371 * acos(cos(radians($latitude)) * 
                cos(radians(farms.lat)) * 
                cos(radians(farms.lng) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(farms.lat)))) AS distance
            ")
                // Filter farms by distance (within a max distance)
                ->having("distance", "<", 10000)
                ->orderBy("distance")
                ->paginate(10); // Paginate the results for efficiency
            return response()->json([
                'status_code' => 200,
                'farm' => $farms,
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
                ->with('categories') 
                ->get()
                ->groupBy(function ($farm) {
                    return $farm->categories->first()->name ?? 'Uncategorized'; 
                });
            return response()->json([
                'status_code' => 200,
                'farms' => $farms,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
