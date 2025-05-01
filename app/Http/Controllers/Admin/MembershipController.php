<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Membership\StoreMembershipRequest;
use App\Http\Requests\Admin\Membership\UpdateMembershipRequest;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index() {
        $memberships = Membership::all();
        return view('admin.membership.index',[
            'memberships' => $memberships,
        ]);
    }
    public function create() {
        return view('admin.membership.create');
    }
    public function store(StoreMembershipRequest $request) {
        try {
            Membership::storeMembership($request->all());
        } catch (\Throwable $th) {
            return redirect()->route('admin.membership.index')->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.membership.index')->with('success', 'Membership created successfully');
        
    }

    public function edit(Membership $membership) {
        return view('admin.membership.edit',[
            'membership' => $membership,
        ]);
    }

    public function update(UpdateMembershipRequest $request, Membership $membership) {
        try {
            $membership->updateMembership($request->all());
        } catch (\Throwable $th) {
            return redirect()->route('admin.membership.index')->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.membership.index')->with('success', 'Membership updated successfully');
        
    }

    public function destroy(Membership $membership) {
        try {
            $membership->delete();
        } catch (\Throwable $th) {
            return redirect()->route('admin.membership.index')->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.membership.index')->with('success', 'Membership deleted successfully');
        
    }
}
