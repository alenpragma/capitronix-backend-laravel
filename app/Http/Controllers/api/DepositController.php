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
            "webhook_url" => "https://webhook.site/27c9cb42-9919-41a0-bf36-b8d796c37b44",
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
            'message' => 'SUCCESS',
            'invoice_id' => $response['data']['invoice_id']
        ]);

    }


    public function checkDeposit(): JsonResponse
    {
        $client = new Client();
        $jobs = DB::table('check_deposit_job')->get();

        if ($jobs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No deposit jobs found.',
                'data' => [],
            ]);
        }

        $successCount = 0;
        $errors = [];

        foreach ($jobs as $job) {
            $jobCreateTime = Carbon::parse($job->job_created_at); // Updated naming convention

            // Delete old jobs (>= 5 minutes)
            if ($jobCreateTime->diffInMinutes(Carbon::now()) >= 5) {
                DB::table('check_deposit_job')->where('userId', $job->userId)->delete();
                continue;
            }

            $user = User::find($job->userId);
            if (!$user) {
                $errors[] = "User not found with ID: {$job->userId}";
                continue;
            }

            $wallet = UserWalletData::where('user_id', $user->id)
                ->select('wallet_address', 'meta')
                ->first();

            if (!$wallet || empty($wallet->wallet_address)) {
                $errors[] = "Wallet address not found for user ID: {$user->id}";
                continue;
            }

            try {
                $response = $client->post(env('DEPOSIT_URL'), [
                    'json' => [
                        'type' => 'token',
                        'chain_id' => '56',
                        'user_id' => '2',
                        'to' => $wallet->wallet_address,
                        'token_address' => '0x55d398326f99059fF775485246999027B3197955',
                    ],
                    'headers' => [
                        'Accept' => 'application/json',
                        'Bearer-Token' => $wallet->meta
                    ],
                    'timeout' => 20,
                ]);

                $responseData = json_decode($response->getBody(), true);

                if (!is_array($responseData)) {
                    $errors[] = "response: $responseData";
                    continue;
                }

                if (isset($responseData['status']) && $responseData['status'] === false) {
                    $errors[] = $responseData['message'] ?? "Unknown error for user ID:";
                    continue;
                }

                $txHash = $responseData['txHash'] ?? null;
                $amount = $responseData['amount'] ?? null;

                if ($txHash === null || $amount === null) {
                    $errors[] = "Missing txHash or amount for user ID: {$user->id}";
                    continue;
                }

                if (Deposit::where('transaction_id', $txHash)->exists()) {
                    $errors[] = "Duplicate transaction for txHash: {$txHash}";
                    continue;
                }

                DB::beginTransaction();

                Deposit::create([
                    'transaction_id' => $txHash,
                    'amount' => $amount,
                    'user_id' => $user->id,
                ]);
                $userEmail = $user->email;
                $user->wallet += $amount;
                $user->save();
                DB::table('check_deposit_job')->where('userId', $job->userId)->delete();
                DB::commit();
                try {
                    Mail::send('mail.transaction-success', [
                        'logo_url' => 'https://www.biznode.io/_next/image?url=%2Flogo.png&w=640&q=75',
                        'txHash' => $txHash,
                        'nonce' => $responseData['nonce'],
                        'contract_address' => $responseData['contract_address'],
                        'amount' => $amount,
                    ], function ($message) use ($userEmail) {
                        $message->to($userEmail)
                            ->subject('Your Transaction Was Successful');
                    });
                }catch (\Exception $exception){}
                sleep(2);
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Exception for user ID {$user->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$successCount} job(s) processed successfully.",
            'errors' => $errors,
        ]);
    }




    public function history(Request $request)
    {
        $user = $request->user();
        $history = Deposit::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(20);
        try {
            $this->checkDeposit($request);
        }catch (\Exception $exception){

        }
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
