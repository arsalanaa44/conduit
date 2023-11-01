<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use DB;

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
                $this->info("User {$user->name} (ID: {$user->id}) has a transaction balance mismatch.");
                $this->info("Wallet Balance: {$user->wallet->balance}, Transaction Sum: {$transactionSum}");
            }
        }

        $this->info('Daily transaction check completed.');
    }
}
