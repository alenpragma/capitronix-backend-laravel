<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->filter;
        $page = $request->get('page', 1);
        $cacheKey = "users_{$filter}_page_{$page}";

        $users = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filter) {
            $query = User::query()->where('role', 'user');

            switch ($filter) {
                case 'active':
                    $query->where('is_active', 1);
                    break;
                case 'inactive':
                    $query->where('is_active', 0);
                    break;
                case 'blocked':
                    $query->where('is_block', 1);
                    break;
                case 'unblocked':
                    $query->where('is_block', 0);
                    break;
            }

            return $query->paginate(10);
        });

        return view('admin.pages.users.index', compact('users'));
    }
     public function show($id)
    {
        $user = User::with('referrals.investors')->findOrFail($id);
        $teamData = $user->teamDataByLevel();
        return view('admin.pages.users.show', compact('user','teamData'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'password'=>'nullable|string|min:6'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password){
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return redirect()->back()->with('success','User updated successfully.');
    }

    public function updateWallet(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'wallet_type' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'action' => 'required|in:add,reduce',
        ]);

        $user = User::findOrFail($request->user_id);
        $wallet = $request->wallet_type;
        $amount = floatval($request->amount);

        if (!in_array($wallet, ['deposit_wallet','active_wallet','profit_wallet'])) {
            return redirect()->back()->with('error', 'Invalid wallet type.');
        }

        if ($request->action === 'add') {

            if (in_array($wallet, ['deposit_wallet','active_wallet'])) {
                $user->$wallet += $amount;
                $user->save();

                Deposit::create([
                    'transaction_id' => Transactions::generateTransactionId(),
                    'user_id'        => $user->id,
                    'amount'         => $amount,
                    'status'         => true,
                    'wallet_type'    => $wallet === 'deposit_wallet' ? 'deposit' : 'active',
                    'remark'         => 'manual',
                ]);
            }

            if ($wallet === 'profit_wallet') {
                $user->$wallet += $amount;
                $user->save();

                Transactions::create([
                    'transaction_id' => Transactions::generateTransactionId(),
                    'user_id'        => $user->id,
                    'amount'         => $amount,
                    'type'           => '+',
                    'status'         => 'completed',
                    'details'        => '$'.$amount.' added to profit wallet by Admin',
                    'remark'         => 'deposit',
                ]);
            }

            $message = 'Balance added successfully.';
        } 
        else {
            if ($user->$wallet < $amount) {
                return redirect()->back()->with('error', 'Insufficient balance.');
            }

            $user->$wallet -= $amount;
            $user->save();

            Transactions::create([
                'transaction_id' => Transactions::generateTransactionId(),
                'user_id'        => $user->id,
                'amount'         => $amount,
                'type'           => '-',
                'status'         => 'completed',
                'details'        => '$'.$amount.' deducted from '.$wallet.' by Admin',
                'remark'         => 'deduct',
            ]);

            $message = 'Balance reduced successfully.';
        }

        return redirect()->back()->with('success', $message);
    }




    public function toggleBlock(User $user)
    {
        $user->is_block = !$user->is_block;
        $user->save();

        return redirect()->back()->with('success',$user->is_block ? 'User Blocked' : 'User Unblocked');
    }

}
