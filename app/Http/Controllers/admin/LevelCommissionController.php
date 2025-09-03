<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\LevelCommission;
use App\Http\Controllers\Controller;

class LevelCommissionController extends Controller
{
     public function index()
    {
        if (LevelCommission::count() == 0) {
            for ($i = 1; $i <= 10; $i++) {
                LevelCommission::create([
                    'level' => $i,
                    'level_name' => "Level $i",
                    'min_invest' => 0,
                    'direct_referral' => 0,
                    'commission' => 0,
                ]);
            }
        }

        $levels = LevelCommission::orderBy('level')->get();
        return view('admin.pages.settings.level-settings', compact('levels'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'level_name.*' => 'required|string',
            'min_invest.*' => 'required|numeric|min:0',
            'direct_referral.*' => 'required|integer|min:0',
            'commission.*' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->level_id as $index => $id) {
            LevelCommission::where('id', $id)->update([
                'level_name' => $request->level_name[$index],
                'min_invest' => $request->min_invest[$index],
                'direct_referral' => $request->direct_referral[$index],
                'commission' => $request->commission[$index],
            ]);
        }

        return redirect()->back()->with('success', 'Level commissions updated successfully!');
    }
}
