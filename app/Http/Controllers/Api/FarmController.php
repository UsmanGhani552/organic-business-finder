<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SaveFarmRequest;
use App\Http\Requests\Api\StoreFarmRequest;
use App\Http\Requests\Api\UpdateFarmRequest;
use App\Models\Category;
use App\Models\DeliveryOption;
use App\Models\Farm;
use App\Models\FarmDay;
use App\Models\Payment;
use App\Models\Service;
use App\Services\FirebaseService;
use Carbon\Carbon;
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
            $user = auth()->user();
            $deviceTokens = $user->deviceTokens;
            $title = "Farm Created";
            $body = "Congratulations! Your listing is now live.";
            $firebaseService = app(FirebaseService::class);
            $res = $firebaseService->sendNotificationToMultipleDevices($deviceTokens, $title, $body);
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
            $farms = Farm::with('users', 'categories', 'days', 'payments', 'delivery_option', 'services', 'products')
                ->where('user_id', $user->id)
                ->get();
            // return response($farms);
            // dd($farms[7]->days->toArray()); 
            // $farmArray = Farm::getFarmRelatedData($farms);

            return response()->json([
                'status_code' => 200,
                'farms' => $farms,
                'base_url_farms' => asset('images/farm'),
                'base_url_products' => asset('images/product'),
                'base_url_user_certificate' => asset('images/user/certificates'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getFarmRelatedData()
    {
        try {
            $categories = Category::select('id', 'name', 'icon')->get();
            $payments = Payment::select('id', 'name', 'icon')->get();
            $delivery_options = DeliveryOption::select('id', 'name')->get();
            $services = Service::select('id', 'name')->get();
            return response()->json([
                'status_code' => 200,
                'categories' => $categories,
                'payments' => $payments,
                'delivery_options' => $delivery_options,
                'services' => $services,
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
            $service_id = $request->query('service_id');
            $user = auth()->user();
            $userId = $user ? $user->id : null;

            $farms = Farm::with('users', 'categories', 'days', 'payments', 'products', 'users', 'delivery_option', 'services')
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

            // Apply farm name or product name filter if provided
            if ($farm_name) {
                $farms->where('name', 'LIKE', "%$farm_name%") // Filter by farm name
                    ->orWhere(function ($query) use ($farm_name) { // OR filter related products
                        $query->whereHas('products', function ($query) use ($farm_name) {
                            $query->where('name', 'LIKE', "%$farm_name%");
                        });
                    });
            }

            // Apply category filter if provided
            if ($category_id) {
                $farms->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            }

            // Apply services filter if provided
            if ($service_id) {
                $farms->whereHas('services', function ($query) use ($service_id) {
                    $query->where('service_id', $service_id);
                });
            }

            // Get the filtered and processed farms
            $farms = $farms->get();
            // $farmArray = Farm::getFarmRelatedData($farms);
            return response()->json([
                'status_code' => 200,
                'farms' => $farms,
                'base_url_farms' => asset('images/farm'),
                'base_url_products' => asset('images/product'),
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
            $day_ids =  $request->query('day_ids');
            $user = auth()->user();
            $userId = $user ? $user->id : null;
            $farms = FarmDay::with('farms')->whereIn('day_id', $day_ids)->selectRaw("
                farm_days.*,
                (6371 * acos(cos(radians($latitude)) * 
                cos(radians(farm_days.lat)) * 
                cos(radians(farm_days.lng) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(farm_days.lat)))) AS distance,
                CASE 
                    WHEN saved_farms.user_id = ? THEN 1
                    ELSE 0
                END AS is_save
            ", [$userId])
                ->leftJoin('saved_farms', function ($join) use ($userId) {
                    $join->on('farm_days.farm_id', '=', 'saved_farms.farm_id')
                        ->where('saved_farms.user_id', '=', $userId);
                })
                ->having("distance", "<", 100)
                ->orderBy("distance")
                ->get();
            // Extract farms from FarmDay and include distance
            $farmsWithDistance = $farms->map(function ($farmDay) {
                $farm = $farmDay->farms;
                $farm->distance = $farmDay->distance;
                return $farm->load('categories', 'days', 'payments', 'products', 'users', 'delivery_option', 'services');
            });
            // dd($farmsWithDistance);
            // $farmArray = Farm::getFarmRelatedData($farmsWithDistance);
            return response()->json([
                'status_code' => 200,
                'farms' => $farmsWithDistance,
                // 'farms' => $farmArray,
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

    // public function getNearByFarms(Request $request)
    // {
    //     try {
    //         $latitude = $request->query('latitude');
    //         $longitude = $request->query('longitude');
    //         $user = auth()->user();
    //         $userId = $user ? $user->id : null;
    //         $farms = Farm::with('users','categories', 'days', 'payments', 'products', 'users')
    //             ->selectRaw("
    //             farms.*,
    //             (6371 * acos(cos(radians($latitude)) * 
    //             cos(radians(farms.lat)) * 
    //             cos(radians(farms.lng) - radians($longitude)) + 
    //             sin(radians($latitude)) * 
    //             sin(radians(farms.lat)))) AS distance,
    //             CASE 
    //                 WHEN saved_farms.user_id = ? THEN 1
    //                 ELSE 0
    //             END AS is_save
    //         ", [$userId])
    //             ->leftJoin('saved_farms', function ($join) use ($userId) {
    //                 $join->on('farms.id', '=', 'saved_farms.farm_id')
    //                     ->where('saved_farms.user_id', '=', $userId);
    //             })
    //             ->having("distance", "<", 10)
    //             ->orderBy("distance")
    //             ->get();
    //         $farmArray = Farm::getFarmRelatedData($farms);
    //         return response()->json([
    //             'status_code' => 200,
    //             // 'farms' => $farms,
    //             'farms' => $farmArray,
    //             'base_url_farms' => asset('farm'),
    //             'base_url_products' => asset('product'),
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status_code' => 400,
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }

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
                ->with('users', 'categories', 'days', 'payments', 'products', 'users', 'delivery_option', 'services')
                ->get()
                ->groupBy(function ($farm) {
                    return $farm->categories->first() ->name ?? 'Uncategorized';
                });
            // $farmArray = Farm::getFarmRelatedData($farms, 2);
            return response()->json([
                'status_code' => 200,
                // 'farms' => $farmArray,
                'farms' => $farms,
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
