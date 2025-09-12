<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Service\TransactionService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ConvertController extends Controller
{
    public TransactionService $transactionService;
    public function __construct(TransactionService $transactionService){
        $this->transactionService = $transactionService;
    }

    public function convert(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $amount = $request->input('amount');

        if ($user->is_block == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, you cannot make a transaction because you are blocked'
            ], 401);
        }

        if ($user->profit_wallet < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have enough balance in Profit Wallet'
            ]);
        }

        DB::beginTransaction();
        try {
            $user->profit_wallet -= $amount;
            $this->transactionService->addNewTransaction(
                $user->id,
                $amount,
                "convert",
                "-",
                "-$amount Converted from Profit Wallet to Deposit Wallet"
            );

            $user->deposit_wallet += $amount;
            $this->transactionService->addNewTransaction(
                $user->id,
                $amount,
                "convert",
                "+",
                "+$amount Added to Deposit Wallet from Profit Wallet"
            );

            $user->save();

            Cache::forget('admin_dashboard_data');
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Converted successfully',
                'profit_wallet' => $user->profit_wallet,
                'deposit_wallet' => $user->deposit_wallet,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

}
