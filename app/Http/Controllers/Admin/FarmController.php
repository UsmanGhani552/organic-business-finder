<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
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
        $farm->image = asset('farm/'.$farm->image);
        return response()->json([
            'status_code' => 200,
            'farm' => $farm
        ]);
    }
}
