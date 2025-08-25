<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transactions;
use App\Models\withdraw_settings;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {


        $dashboardData = Cache::remember('admin_dashboard_data', now()->hour(1), function () {

            $withdrawSettings = withdraw_settings::first();
            $chargePercent = $withdrawSettings ? $withdrawSettings->charge : 0;
            $totalNetWithdrawals = Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->sum('amount');
            $withdrawChargeAmount = $chargePercent > 0 ? $totalNetWithdrawals * $chargePercent / (100 - $chargePercent) : 0;

            return [

                // user
                'totalUser' => User::where('role', 'user')->count(),
                'activeUser' => User::where('is_active', 1)->where('role', 'user')->count(),
                'blockUser' => User::where('is_block', 1)->where('role', 'user')->count(),
                'newUser' => User::where('created_at', '>=', now()->startOfDay()->addHours(5))->where('role', 'user')->count(),

                // deposit
                // 'totalDeposits' => Transactions::where('remark', 'deposit')->where('status', 'Completed')->sum('amount'),
                // 'todayDeposits' => Transactions::where('remark', 'deposit')->where('status', 'Completed')->whereDate('created_at', today())->sum('amount'),
                // 'last7DaysDeposits' => Transactions::where('remark', 'deposit')->where('status', 'Completed')->whereBetween('created_at', [now()->subDays(7), today()])->sum('amount'),
                // 'last30DaysDeposits' => Transactions::where('remark', 'deposit')->where('status', 'Completed')->whereBetween('created_at', [now()->subDays(30), today()])->sum('amount'),

                'totalDeposits' => Deposit::sum('amount'),
                'todayDeposits' => Deposit::whereDate('created_at', today())->sum('amount'),
                'last7DaysDeposits' => Deposit::whereBetween('created_at', [now()->subDays(7), today()])->sum('amount'),
                'last30DaysDeposits' => Deposit::whereBetween('created_at', [now()->subDays(30), today()])->sum('amount'),

                // withdrawal
                'totalWithdrawals' => Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->sum('amount'),
                'todayWithdrawals' => Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->whereDate('created_at', today())->sum('amount'),
                'last30DaysWithdrawals' => Transactions::where('remark', 'withdrawal')->where('status', 'Paid')->whereBetween('created_at', [now()->subDays(30), today()])->sum('amount'),
               'withdrawChargeAmount' => $withdrawChargeAmount,

            ];
        });

        return view('admin.dashboard', compact('dashboardData'));
    }
}
