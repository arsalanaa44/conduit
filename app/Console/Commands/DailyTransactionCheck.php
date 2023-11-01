<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Log;


class DailyTransactionCheck extends Command
{
    protected $signature = 'daily:transaction-check';
    protected $description = 'Check daily transactions and log discrepancies';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {

            $transactionSum = DB::table('transactions')
                ->where('wallet_id', $user->wallet->id)
                ->sum('amount');

            if ($transactionSum != $user->wallet->balance) {
                Log::channel('transactions')->info("User {$user->name} (ID: {$user->id}) has a transaction balance mismatch.");
                Log::channel('transactions')->info("Wallet Balance: {$user->wallet->balance}, Transaction Sum: {$transactionSum}");
            }
        }

        Log::channel('transactions')->info('Daily transaction check completed.');
    }
}
