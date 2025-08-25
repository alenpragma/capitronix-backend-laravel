<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\Investor;
use App\Models\User;
use App\Service\TransactionService;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

class UserController extends Controller
{
    protected UserService $userService;
    protected TransactionService $transactionService;

    public function __construct(UserService $userService, TransactionService $transactionService)
    {
        $this->userService = $userService;
        $this->transactionService = $transactionService;
    }

    public function UserProfile(Request $request): JsonResponse
    {
        return $this->userService->UserProfile($request);
    }

    public function team(Request $request): JsonResponse
    {
        $user = $request->user();
        $team = $this->getTeamRecursive($user);
        return response()->json([
            'status' => true,
            'user' => $user->only(['email', 'name', 'is_active', 'created_at']),
            'team' => $team
        ]);
    }


    public function getDirectReferrals(Request $request): JsonResponse
    {
        $user = $request->user();
        $directReferrals = $user->referrals()
            ->select('users.id', 'users.name', 'users.refer_by', 'users.email', 'users.is_active', 'users.created_at')
            ->selectRaw('COALESCE(SUM(investors.investment), 0) as investment')
            ->leftJoin('investors', 'investors.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.refer_by', 'users.email', 'users.is_active', 'users.created_at')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $directReferrals->items(),
            'total' => $directReferrals->total(),
            'per_page' => $directReferrals->perPage(),
            'page' => $directReferrals->currentPage(),
            'current_page' => $directReferrals->currentPage(),
            'last_page' => $directReferrals->lastPage(),
            'from' => $directReferrals->firstItem(),
        ]);
    }

    private function getTeamRecursive(User $user, int $level = 1): array
    {

        $user->load('referrals');
        $team = [];
        foreach ($user->referrals as $referral) {
            $team[] = [
                'level' => $level,
                'email' => $referral->email,
                'name' => $referral->name,
                'is_active' => $referral->is_active,
                'created_at' => $referral->created_at,
                'investment' => Investor::where('user_id', $referral->id)->sum('investment'),
                'team' => $this->getTeamRecursive($referral, $level + 1)
            ];
        }
        return $team;
    }


    public function kyc(Request $request): JsonResponse
    {
        return $this->userService->UserKyc($request);
    }


    public function activeAccount(Request $request): JsonResponse
    {
        $validate = $request->validate([
            'code' => 'required',
        ]);

        $code = Code::where('code', $validate['code'])->where('status', 'active')->first();

        if (!$code) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid code'
            ]);
        }

        $user = $request->user();

        if($user->is_active == 1){
            return response()->json([
                'status' => false,
                'message' => 'Your account is already active'
            ]);
            }

            // Check for referral bonus
            $level1 = $user->referredBy()->first();
            if ($level1 && $level1->is_active) {
                $bonus = 3.75;
                $level1->increment('profit_wallet', $bonus);
                $this->transactionService->addNewTransaction(
                    "$level1->id",
                    "$bonus",
                    "referral_commission",
                    "+",
                    "Activation Bonus From $user->name"
                );
            }
            $user->is_active = 1;
            $user->save();
            $code->status = 'used';
            $code->user_id = $user->id;
            $code->save();

            return response()->json([
                'status' => true,
                'message' => 'Account activated successfully',
            ]);
    }


}
