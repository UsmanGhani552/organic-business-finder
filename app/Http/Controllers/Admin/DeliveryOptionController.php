<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryOption\StoreDeliveryOptionRequest;
use App\Http\Requests\Admin\DeliveryOption\UpdateDeliveryOptionRequest;
use App\Models\DeliveryOption;
use Exception;
use Illuminate\Http\Request;

class DeliveryOptionController extends Controller
{
    public function index() {
        $deliveryOptions = DeliveryOption::all();
        return view('admin.delivery-option.index',[
            'deliveryOptions' => $deliveryOptions
        ]);
    }

    public function create() {
        return view('admin.delivery-option.create');
    }

    public function store(StoreDeliveryOptionRequest $request) {
        try {
            DeliveryOption::storedeliveryOption($request->validated());
            return redirect()->route('admin.delivery-option.index')->with('success','Delivery Option Stored Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
    public function edit(deliveryOption $deliveryOption) {
        return view('admin.delivery-option.edit',[
            'deliveryOption' => $deliveryOption
        ]);
    }

    public function update(UpdateDeliveryOptionRequest $request,DeliveryOption $deliveryOption) {
        try {
            DeliveryOption::updatedeliveryOption($request->validated(),$deliveryOption);
            return redirect()->route('admin.delivery-option.index')->with('success','Delivery Option Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function delete(DeliveryOption $deliveryOption) {
        try {
            $deliveryOption->delete();
            return redirect()->route('admin.delivery-option.index')->with('success','Delivery Option Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
