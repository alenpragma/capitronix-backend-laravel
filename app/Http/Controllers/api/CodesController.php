<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Service\TransactionService;
use Illuminate\Http\Request;


class CodesController extends Controller
{
    protected TransactionService $transactionService;
    public function __construct(TransactionService $transactionService){
        $this->transactionService = $transactionService;
    }

    public function buyCode(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $validated['quantity'];
        $user = $request->user();
        $costPerCode = 25;
        $totalCost = $costPerCode * $quantity;

        // Check if the user has sufficient balance
        if ($user->active_wallet < $totalCost) {
            return response()->json([
                'status' => false,
                'message' => "Insufficient balance to purchase {$quantity} code(s)."
            ]);
        }

        // Deduct the total cost from the user's wallet
        $user->active_wallet -= $totalCost;
        $user->save();

        $generatedCodes = [];

        // Generate the codes
        for ($i = 0; $i < $quantity; $i++) {
            $code = new Code();
            $code->code = $code->generateCode();
            $code->code_owner = $user->id;
            $code->status = 'active';
            $code->save();

            $generatedCodes[] = $code->code;

            $this->transactionService->addNewTransaction(
                $user->id,
                $costPerCode,
                'activation',
                '-',
                'Account activation via code purchase'
            );
        }

        return response()->json([
            'status' => true,
            'message' => "{$quantity} code(s) generated successfully.",
            'codes' => $generatedCodes,
        ]);
    }

    public function codeHistory(Request $request)
    {
        $user = $request->user();
        $codes = Code::where('code_owner', $user->id)
            ->join('users', 'codes.user_id', '=', 'users.id')
            ->select(
                'codes.id',
                'codes.code',
                'codes.status',
                'codes.code_owner',
                'users.name as user_name',
                'codes.created_at'
            )
            ->orderBy('codes.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'codes' => $codes,
        ]);
    }


}
