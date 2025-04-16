<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index() {
        $settings = Setting::all();
        return view('admin.settings.index', [
            'settings' => $settings
        ]);
    }

    public function edit() {
        $settings = Setting::all();
        return view('admin.settings.edit', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request, Setting $setting) {
        $request->validate([
            'settings' => 'required|array',
        ]);
        foreach ($request->input('settings') as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully.');
        
    }
}
