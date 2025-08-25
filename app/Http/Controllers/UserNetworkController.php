<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserNetworkController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $levelData = $user->teamDataByLevel();

        return response()->json([
            'status' => true,
            'data' => $levelData,
        ]);
    }

}
