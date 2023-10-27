<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\User;


class WalletController extends Controller
{
    public static function createAndInitializeWallet(User $user)
    {

        if (!$user->wallet) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);
        }

    }
    public function increaseBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();

        $wallet = $user->wallet ;

        $wallet->balance += $request->input('amount');
        $wallet->save();

        return response()->json(['message' => 'Balance increased successfully', 'data' => $wallet]);
    }



}
