<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\enum\TransactionTypeEnum;


class WalletController extends Controller
{
    public static function createAndInitializeWallet(User $user)
    {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);
    }

    public function increaseBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();

        return DB::transaction(function () use ($user, $request) {
            $wallet = $user->wallet()->lockForUpdate()->firstOrFail();
            $amount = $request->input('amount');

            $transaction = new Transaction([
                'amount' => $amount,
                'action' => TransactionTypeEnum::CHARGE,
                'description' => 'Wallet recharge',
                'meta_data' => '',
            ]);
            $wallet->transactions()->save($transaction);

            $wallet->balance += $amount;
            $wallet->save();

            return response()->json(['message' => 'Balance increased successfully', 'data' => $wallet]);
        });
    }
    public function transfer(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $sender = auth()->user();
        $receiverUsername = $request->input('username');
        $amount = $request->input('amount');

        return DB::transaction(function () use ($sender, $receiverUsername, $amount) {
            $senderWallet = $sender->wallet()->lockForUpdate()->first();

            if (!$senderWallet) {
                return response()->json(['message' => 'Sender wallet not found'], 404);
            }

            $receiver = User::where('username', $receiverUsername)->first();

            if (!$receiver) {
                return response()->json(['message' => 'Receiver not found'], 404);
            }

            $receiverWallet = $receiver->wallet()->lockForUpdate()->first();

            if (!$receiverWallet) {
                return response()->json(['message' => 'Receiver wallet not found'], 404);
            }

            if ($senderWallet->balance >= $amount) {

                $transaction = new Transaction([
                    'amount' => -$amount,
                    'action' => TransactionTypeEnum::SEND,
                    'description' => 'send with transfer method',
                    'meta_data' => 'send to:'.$receiverWallet->user->name,
                ]);
                $senderWallet->transactions()->save($transaction);

                $transaction = new Transaction([
                    'amount' => $amount,
                    'action' => TransactionTypeEnum::RECEIVE,
                    'description' => 'receive with transfer method',
                    'meta_data' => 'receive from:'.$senderWallet->user->name,
                ]);
                $receiverWallet->transactions()->save($transaction);

                $senderWallet->decrement('balance', $amount);
                $receiverWallet->increment('balance', $amount);

                return response()->json(['message' => 'Transaction completed successfully']);
            } else {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }
        });

}




}
