<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transactions;
use App\Models\User;
use App\Models\UserWalletData;
use App\Service\TransactionService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DepositController extends Controller
{
    protected TransactionService $transactionService;
    public function __construct(TransactionService $transactionService){
        $this->transactionService = $transactionService;
    }


    public function Store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'wallet' => 'required',
            'amount' => 'required|min:1',
        ]);

        $client = new Client();

        $headers = [
            'x-api-key'      => '9S3WW3P-JRB43DN-PBCJ23E-P9W96H3',
            'Content-Type'   => 'application/json',
        ];

        $payload = [
            "amount"      => $request->amount,
            "chain_id"    => 9996,
            "type"        => "native",
            "token_name"  => "USDT",
            "user_id"     => 27,
            "webhook_url" => "https://admin.capitronix.com/api/deposit-check",
        ];


        $payment = $client->request('POST', 'https://evm.blockmaster.info/api/create_invoice', [
            'headers' => $headers,
            'json'    => $payload
        ]);


        $response = json_decode($payment->getBody()->getContents(), true);


        // save to db
        $deposit = new Deposit();
        $deposit->user_id    = $user->id;
        $deposit->amount     = $request->amount;
        $deposit->wallet_type      = $request->wallet;
        $deposit->transaction_id = $response['data']['invoice_id'];
        $deposit->save();

        // instead of return string, redirect to show page
        return response()->json([
            'status' => true,
            'message' => 'success',
            'invoice_id' => $response['data']['invoice_id']
        ]);

    }

    public function webHook(Request $request){
        $data = $request->input();

        // যদি response এ status true থাকে
        if(isset($data['status']) && $data['status'] === true || $data['status'] === "completed"){

            $invoice = new Deposit();

            $customerData = $invoice->where('transaction_id', $data['invoice_id'])->where('status', 0)->first();

            if(!$customerData){
                return "no data";
            }

            $customerData->amount = $data['amount'];
            $customerData->status = 1;
            $customerData->save();

            $user = User::where('id', $customerData->user_id)->first();
            if ($customerData->wallet_type === "active"){
                $user->active_wallet += $data['amount'];
                $user->save();
            }elseif ($customerData->wallet_type === "deposit"){
                $user->deposit_wallet += $data['amount'];
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Deposit added successfully.',
                'deposit_id' => $customerData->id
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Deposit not added, status is false.'
        ]);
    }




    public function history(Request $request)
    {
        $user = $request->user();
        $history = Deposit::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $history->items(),
            'total' => $history->total(),
            'current' => $history->currentPage(),
            'next' => $history->nextPageUrl(),
            'previous' => $history->previousPageUrl(),
        ]);
    }
}
