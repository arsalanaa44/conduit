<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\WalletController;
use Illuminate\Support\Facades\DB;


class UserService
{
    public function create($username, $email, $password)
    {
        return DB::transaction(function () use ($username, $email, $password) {

            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            WalletController::createAndInitializeWallet($user);

            return $user;
        });

    }

    public function login($email, $password)
    {
        $credentials = ['email' => $email, 'password' => $password];


        $token = Auth::attempt($credentials);

        if (!$token) {
            return false;
        }

        $user = Auth::user();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
