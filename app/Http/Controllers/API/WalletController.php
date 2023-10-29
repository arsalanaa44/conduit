<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;


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
            $wallet = $user->wallet()->lockForUpdate()->first();

            $wallet->balance += $request->input('amount');
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
//                Transaction::create([
//                    'sender_wallet_id' => $senderWallet->id,
//                    'receiver_wallet_id' => $receiverWallet->id,
//                    'amount' => $amount,
//                ]);

                $senderWallet->decrement('balance', $amount);
                $receiverWallet->increment('balance', $amount);

                return response()->json(['message' => 'Transaction completed successfully']);
            } else {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }
        });

}




}
