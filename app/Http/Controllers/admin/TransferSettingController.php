<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\TransferSetting;
use App\Http\Controllers\Controller;

class TransferSettingController extends Controller
{
     public function index()
    {
        $setting = TransferSetting::first();
        return view('admin.pages.settings.transfer_settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'min_transfer' => 'required|numeric|min:0',
            'max_transfer' => 'required|numeric|gte:min_transfer',
            'charge' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ]);

        $setting = TransferSetting::first();
        $setting->update([
            'min_transfer' => $request->min_transfer,
            'max_transfer' => $request->max_transfer,
            'charge' => $request->charge,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Transfer settings updated successfully.');
    }
}
