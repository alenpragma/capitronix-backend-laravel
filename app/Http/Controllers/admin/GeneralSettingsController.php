<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $generalSettings = GeneralSetting::first();
        return view('admin.pages.settings.general_settings', compact('generalSettings'));
    }
    public function update(Request $request)
    {
        $request->validate([

            'app_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            // 'facebook_url' => 'nullable|url|max:255',
            // 'twitter_url' => 'nullable|url|max:255',
            // 'instagram_url' => 'nullable|url|max:255',
            // 'youtube_url' => 'nullable|url|max:255',
            // 'linkedin_url' => 'nullable|url|max:255',
            // 'tiktok_url' => 'nullable|url|max:255',

        ]);

        $generalSettings = GeneralSetting::first();

        $data = $request->only([
             'app_name',
            //  'facebook_url',
            // 'twitter_url',
            // 'instagram_url',
            // 'youtube_url',
            // 'linkedin_url',
            // 'tiktok_url'
        ]);


        if ($request->hasFile('logo')) {
            if ($generalSettings->logo) {
                Storage::disk('public')->delete($generalSettings->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
            $data['logo'] = str_replace('public/', '', $data['logo']);
        }

        if ($request->hasFile('favicon')) {
            if ($generalSettings->favicon) {
                Storage::disk('public')->delete($generalSettings->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('favicons', 'public');
            $data['favicon'] = str_replace('public/', '', $data['favicon']);
        }

        $generalSettings->update($data);

        return redirect()->route('admin.general.settings')->with('success', 'Settings updated successfully!');
    }
}
