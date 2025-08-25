<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Service\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConvertController extends Controller
{
    public TransactionService $transactionService;
    public function __construct(TransactionService $transactionService){
        $this->transactionService = $transactionService;
    }

    public function convert(Request $request){
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
        $user = $request->user();
        $amount = $request->input('amount');
        if($user->is_block == 1){
            return response()->json([
                'status' => false,
                'message' => 'Sorry, you cannot make a transaction because it is blocked'
            ],401);
        }

        if($user->profit_wallet < $amount){
            return response()->json([
                'status' => false,
                'message' => 'You do not have enough wallet to make a transaction'
            ]);
        }else{
            $user->profit_wallet -= $amount;
            $this->transactionService->addNewTransaction("$user->id", "$amount", "convert","-","-$amount Convert BIZT  to USDT Wallet");
            $user->wallet += $amount*0.02;
            $this->transactionService->addNewTransaction("$user->id","$amount","convert","+","+$amount Converted BIZT to Main USDT Wallet");
            $user->save();
            Cache::forget('admin_dashboard_data');
            return response()->json([
                'status' => true,
                'message' => 'Converted successfully',
                'wallet' => $user->wallet,
            ]);
        }
    }
}
