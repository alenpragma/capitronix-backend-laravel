<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Models\User;
use App\Models\withdraw_settings;
use App\Service\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function transactions(Request $request)
    {
        $keyword = $request->get('keyword');
        if ($keyword) {
            $transactions = Transactions::where('user_id', $request->user()->id)->where('remark', '=', $keyword)->orderBy('id', 'desc')->paginate(20);
            return response()->json([
                'status' => true,
                'data' => $transactions->items(),
                'total' => $transactions->total(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
            ]);
        }
        $transactions = Transactions::where('user_id', $request->user()->id)->orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'status' => true,
            'data' => $transactions->items(),
            'total' => $transactions->total(),
            'last_page' => $transactions->lastPage(),
            'current_page' => $transactions->currentPage(),
            'per_page' => $transactions->perPage(),
            'from' => $transactions->firstItem(),
        ]);
    }


    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'wallet' => 'required|string|in:active,deposit',
            'email'  => 'required|exists:users,email',
        ]);

        $sender   = $request->user();
        $receiver = User::where('email', $validated['email'])->first();

        if ($sender->id === $receiver->id) {
            return response()->json([
                'status'  => false,
                'message' => "You cannot transfer to yourself",
            ], 400);
        }

        $walletMap = [
            'active'  => 'active_wallet',
            'deposit' => 'deposit_wallet',
        ];
        $walletColumn = $walletMap[$validated['wallet']];

        try {
            DB::beginTransaction();

            if ($sender->{$walletColumn} < $validated['amount']) {
                return response()->json([
                    'status'  => false,
                    'message' => "You don't have enough balance in {$validated['wallet']} wallet",
                ], 400);
            }

            $sender->decrement($walletColumn, $validated['amount']);

            $receiver->increment($walletColumn, $validated['amount']);

            Transactions::create([
                'user_id'     => $sender->id,
                'amount'      => $validated['amount'],
                'wallet_type' => $validated['wallet'],
                'type'        => '-',
                'status'      => 'Completed',
                'details'     => "Transfer to {$receiver->email}",
            ]);

            Transactions::create([
                'user_id'     => $receiver->id,
                'amount'      => $validated['amount'],
                'wallet_type' => $validated['wallet'],
                'remark'=> 'transfer',
                'type'        => '+',
                'status'      => 'Completed',
                'details'     => "Received from {$sender->email}",
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "Transaction successful from {$validated['wallet']} wallet",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => "Transaction failed",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }




    // public function withdraw(Request $request)
    // {
    //     $withdrawSettings = withdraw_settings::first();

    //     if($withdrawSettings->status == 0){
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Withdrawals are temporarily disabled. Please contact support'
    //         ]);
    //     }

    //     if (!$withdrawSettings) {
    //         return back()->with('error', 'Withdraw settings not found.');
    //     }

    //     $min = $withdrawSettings->min_withdraw;
    //     $max = $withdrawSettings->max_withdraw;
    //     $charge = $withdrawSettings->charge;

    //     $validatedData = $request->validate([
    //         'amount' => ['required', 'numeric', "min:$min", "max:$max"],
    //         'wallet' => ['required', 'string', 'min:10', 'max:70'],
    //     ]);

    //     $user = $request->user();
    //     $amount = $validatedData['amount'];
    //     $amount = $amount - ($amount * $charge / 100);
    //     $wallet = $validatedData['wallet'];


    //     if ($user->wallet < $amount) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Insufficient balance',
    //         ], 400);
    //     } else {

    //         $response = Http::post('https://evm.blockmaster.info/api/payout',[
    //             'amount' => $amount,
    //             'type' => 'token',
    //             'to' => $wallet,
    //             'token_address' => env('TOKEN'),
    //             'chain_id' => env('CHAIN_ID'),
    //             'rpc_url' => env('RPC'),
    //             'user_id' => 2
    //         ]);

    //         $response = json_decode($response->body());

    //         if ($response->status && $response->txHash != null) {
    //             $this->transactionService->addNewTransaction(
    //                 "$user->id",
    //                 $request->input('amount'),
    //                 "withdrawal",
    //                 "-",
    //                 "Withdraw success Tx Hash: $response->txHash",
    //                 'Paid',
    //                 'USDT'
    //             );
    //             $user->wallet -= $validatedData['amount'];
    //             $user->save();
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Your withdrawal successfully',
    //                 'wallet_balance' => $user->wallet,
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'withdrawal amount low please contact support',
    //             ]);
    //         }

    //     }
    // }


    public function withdraw(Request $request)
    {
        $withdrawSettings = withdraw_settings::first();

        if($withdrawSettings->status == 0){
            return response()->json([
                'status' => false,
                'message' => 'Withdrawals are temporarily disabled. Please contact support'
            ]);
        }

        if (!$withdrawSettings) {
            return back()->with('error', 'Withdraw settings not found.');
        }

        $min = $withdrawSettings->min_withdraw;
        $max = $withdrawSettings->max_withdraw;
        $charge = $withdrawSettings->charge;

        $validatedData = $request->validate([
            'amount' => ['required', 'numeric', "min:$min", "max:$max"],
            'wallet' => ['required', 'string', 'min:10', 'max:70'],
        ]);

        $user = $request->user();
        $amount = $validatedData['amount'];
        $amount = $amount + ($amount * $charge / 100);
        $wallet = $validatedData['wallet'];



        if ($user->wallet < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance',
            ], 400);
        } else {
            $this->transactionService->addNewTransaction(
                "$user->id",
                "$amount",
                "withdrawal",
                "-",
                "$wallet", 'Pending');
            $user->wallet -= $amount;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Your withdrawal request has been received and is currently pending.',
                'wallet_balance' => $user->wallet,
            ]);
        }
    }

}
