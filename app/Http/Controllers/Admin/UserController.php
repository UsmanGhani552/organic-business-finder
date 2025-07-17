<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $users = User::orderBy('id','desc')->get();
        return view('admin.user.index',[
            'users' => $users
        ]);
    }

    public function create() {
        return view('admin.user.create');
    }

    public function store(StoreUserRequest $request) {
        try {
            User::storeUser($request->validated());
            return redirect()->route('admin.user.index')->with('success','User Stored Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
    public function edit(User $user) {
        return view('admin.user.edit',[
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request,User $user) {
        try {
            User::updateUser($request->validated(),$user);
            return redirect()->route('admin.user.index')->with('success','User Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function delete(User $user) {
        try {
            $user->delete();
            return redirect()->route('admin.user.index')->with('success','User Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}

