<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transactions;
use App\Models\withdraw_settings;
use App\Models\Code;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index()
    {


        $dashboardData = Cache::remember('admin_dashboard_data', now()->hour(1), function () {

            $withdrawSettings = withdraw_settings::first();
            $chargePercent = $withdrawSettings ? $withdrawSettings->charge : 0;
            $totalNetWithdrawals = Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->sum('amount');
            $withdrawChargeAmount = $chargePercent > 0 ? $totalNetWithdrawals * $chargePercent / (100 - $chargePercent) : 0;

              $costPerCode = 25;

            // Codes info
            $totalCodes   = Code::count();
            $usedCodes    = Code::where('status', 'used')->count();
            $unusedCodes  = Code::where('status', 'active')->count();
            $totalPurchased = $totalCodes * $costPerCode;

            return [

                // user
                'totalUser' => User::where('role', 'user')->count(),
                'activeUser' => User::where('is_active', 1)->where('role', 'user')->count(),
                'blockUser' => User::where('is_block', 1)->where('role', 'user')->count(),
                'newUser' => User::where('created_at', '>=', now()->startOfDay()->addHours(5))->where('role', 'user')->count(),


                //deposit wallet
                'totalDeposits' => User::sum('deposit_wallet'),
                'todayDeposits' => User::whereDate('created_at', today())->sum('deposit_wallet'),
                'last7DaysDeposits' => User::whereBetween('created_at', [now()->subDays(7), today()])->sum('deposit_wallet'),
                'last30DaysDeposits' => User::whereBetween('created_at', [now()->subDays(30), today()])->sum('deposit_wallet'),

                //active wallet
                'totalActiveDeposits' => User::sum('active_wallet'),
                'todayActiveDeposits' => User::whereDate('created_at', today())->sum('active_wallet'),
                'last7DaysActiveDeposits' => User::whereBetween('created_at', [now()->subDays(7), today()])->sum('active_wallet'),
                'last30DaysActiveDeposits' => User::whereBetween('created_at', [now()->subDays(30), today()])->sum('active_wallet'),



                // 'totalDeposits' => Deposit::sum('amount'),
                // 'todayDeposits' => Deposit::whereDate('created_at', today())->sum('amount'),
                // 'last7DaysDeposits' => Deposit::whereBetween('created_at', [now()->subDays(7), today()])->sum('amount'),
                // 'last30DaysDeposits' => Deposit::whereBetween('created_at', [now()->subDays(30), today()])->sum('amount'),

                // withdrawal
                'totalWithdrawals' => Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->sum('amount'),
                'todayWithdrawals' => Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->whereDate('created_at', today())->sum('amount'),
                'last30DaysWithdrawals' => Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->whereBetween('created_at', [now()->subDays(30), today()])->sum('amount'),
               'withdrawChargeAmount' => $withdrawChargeAmount,




                // codes
                'totalCodes'       => $totalCodes,
                'usedCodes'        => $usedCodes,
                'unusedCodes'      => $unusedCodes,
                'totalPurchased'   => $totalPurchased,

            ];
        });

        $countryData = User::select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->get();

        $countries = $countryData->pluck('country');      // ['US','BD',...]
        $countryCounts = $countryData->pluck('total');

        return view('admin.dashboard', compact('dashboardData', 'countries', 'countryCounts'));
    }
}
