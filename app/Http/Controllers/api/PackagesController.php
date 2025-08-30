<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Package;
use App\Models\referrals_settings;
use App\Service\TransactionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function getPackages()
    {
        $packages = Package::where('active', 1)->get();
        return response()->json([
            'status' => true,
            'data' => $packages,
        ]);
    }


    public function BuyPackage(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'package_id' => 'required|exists:package,id',
        ]);

        $package = Package::find($validatedData['package_id']);
        $user = $request->user();
        $packageName = $package->name;

        if ($user->is_active == 0){
            return response()->json([
                'status' => false,
                'message' => 'Your account is not activated yet.',
            ]);
        }

        if($user->is_block == 1){
            return response()->json([
                'status' => false,
                'message' => 'Sorry, you cannot make a transaction because it is blocked'
            ],401);
        }

        if ($user->deposit_wallet < $package->price) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient funds',
            ]);
        }

        DB::beginTransaction();

        try {
            // deduct balance
            $user->deposit_wallet -= $package->price;
            $user->save();

            // add transaction
            $this->transactionService->addNewTransaction(
                $user->id,
                $package->price,
                "package_purchased",
                "-",
                "$packageName Package Purchased",
                "Completed",
                "USDT"
            );

            // create investor
            Investor::create([
                'user_id' => $user->id,
                'package_name' => $packageName,
                'return_type' => $package->return_type,
                'package_id' => $validatedData['package_id'],
                'investment' => $package->price,
                'duration' => $package->duration ?? null,
                'total_due_day' => $package->duration,
                'start_date' => now(),
                'next_cron' => now()->addDay(),
                'last_cron' => now(),
            ]);

            DB::commit();

            /**
             * Referral Bonus Distribution (3 levels)
             */
            $bonusPercents = [5, 2, 1]; // level 1, 2, 3
            $referrer = $user->referredBy()->first(); // 1st level parent

            foreach ($bonusPercents as $level => $percent) {
                if (!$referrer) {
                    break;
                }

                $bonus = $package->price * $percent / 100;
                $checkIsInvestor = Investor::where('user_id', $referrer->id)->where('package_id', $package->id)->count();
                if ($checkIsInvestor > 0) {
                    $referrer->increment('profit_wallet', $bonus);

                    $this->transactionService->addNewTransaction(
                        $referrer->id,
                        $bonus,
                        "referral_commission",
                        "+",
                        "Level " . ($level+1) . " Referral From $user->name"
                    );
                }

                // move to next upline
                $referrer = $referrer->referredBy()->first();
            }

            // clear cache
            Cache::forget('admin_dashboard_data');
            Cache::forget('packages_active_page_1');
            Cache::forget('packages_inactive_page_1');

            $package->increment('total_sell');

            return response()->json([
                'status' => true,
                'message' => 'Package purchased successfully',
                'wallet_balance' => $user->deposit_wallet,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    public function InvestHistory(Request $request): JsonResponse{
        $user = $request->user();
        $investorData = Investor::where('user_id', $user->id)
            ->join('package', 'investors.package_id', '=', 'package.id')
            ->select('investors.*', 'package.interest_rate')
            ->orderBy('investors.created_at', 'desc')->paginate(10);
        $investorData->getCollection()->transform(function ($item) {
            //dd($item);
            $item->daily_roi = ($item->investment * $item->interest_rate/100) / 0.02;
            return $item;
        });
        return response()->json([
            'status' => true,
            'data' => $investorData->items(),
            'total' => $investorData->total(),
            'current_page' => $investorData->currentPage(),
            'last_page' => $investorData->lastPage(),

        ]);
    }

    public function cancelInvest(Request $request): JsonResponse
    {
        $user = $request->user();
        $validatedData = $request->validate([
            'id' => 'required|exists:investors,id'
        ]);

        $invest = Investor::where('id', $validatedData['id'])->where('user_id', $user->id)->where('status', 1)->first();
        if($invest){
            $invest->status = 0;
            $invest->save();
            $user->wallet += $invest->investment;
            $user->save();
            $this->transactionService->addNewTransaction(
                $user->id,
                $invest->investment,
                "package_purchased",
                "+",
                "$invest->package_name Package Cancelled"
            );
            return response()->json([
                'status' => true,
                'message' => 'Package cancelled successfully',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Investor not found',
            ],422);
        }
    }

}
