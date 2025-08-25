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

    public function buyCode(Request $request){
        $user =  $request->user();
        if ($user->active_wallet < 25){
            return response()->json([
                'status' => false,
                'message' => "You don't have enough balance"
            ]);
        }else{
            $user->active_wallet -= 25;
            $code = new Code();
            $newCode = $code->generateCode();
            $code->code_owner = $user->id;
            $code->code = $newCode;
            $code->status = 'active';
            $user->save();
            $code->save();
            $this->transactionService->addNewTransaction(
                "$user->id",
                "25",
                "activation",
                "-",
                "For Activation Your Account"
            );
            return response()->json([
                'status' => true,
                'message' => "Code generated successfully",
                'code' => $newCode,
            ]);
        }
    }

}
