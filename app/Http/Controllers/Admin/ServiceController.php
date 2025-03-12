<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\StoreServiceRequest;
use App\Http\Requests\Admin\Service\UpdateServiceRequest;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index() {
        $services = Service::all();
        return view('admin.services.index',[
            'services' => $services
        ]);
    }

    public function create() {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request) {
        try {
            Service::storeService($request->validated());
            return redirect()->route('admin.service.index')->with('success','Service Stored Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
    public function edit(Service $service) {
        return view('admin.services.edit',[
            'service' => $service
        ]);
    }

    public function update(UpdateServiceRequest $request,Service $service) {
        try {
            Service::updateService($request->validated(),$service);
            return redirect()->route('admin.service.index')->with('success','Service Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function delete(Service $Service) {
        try {
            $Service->delete();
            return redirect()->route('admin.service.index')->with('success','Service Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
