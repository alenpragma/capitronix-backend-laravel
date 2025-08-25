<?php

namespace App\Service;
use App\Models\Transactions;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function addNewTransaction($userID,$amount,$remark,$type,$details,$status ='Completed',$currency = 'BIZT'):bool
    {
        try {
            Transactions::create([
                'user_id' => $userID,
                'transaction_id' => Transactions::generateTransactionId(),
                'remark' => $remark,
                'type' => $type,
                'amount' => $amount,
                'status' => $status,
                'details' => $details,
                'currency' => $currency,
            ]);
            return true;
        }catch (\Exception $exception){
            Log::error('Transaction Creation Failed: ' . $exception->getMessage());
            return false;
        }
    }
}
