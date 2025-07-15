<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Traits\ImageUploadTrait;
use Exception;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    use ImageUploadTrait;
    public function index() {
        $farms = Farm::all();
        return view('admin.farm.index',[
            'farms' => $farms
        ]);
    }

    public function create() {
        return view('admin.farm.create');
    }

    public function show($id) {
        $farm = Farm::with('payments','products','users','categories','days')->findOrFail($id);
        $farm->image = asset('images/farm/'.$farm->image);
        return response()->json([
            'status_code' => 200,
            'farm' => $farm
        ]);
    }

    public function delete(Farm $farm) {
        try {
            $farm->delete();
            $farm->deleteImage( "images/farm/{$farm->image}");
            return redirect()->route('admin.farm.index')->with('success','Farm Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
