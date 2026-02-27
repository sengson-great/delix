<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountsTableSeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // Change this to your admin user ID
        $now = Carbon::now();

        // Insert accounts one by one to avoid column mismatch
        $accounts = [
            // Cash Account
            [
                'user_id' => $userId,
                'account_holder_name' => 'Admin Cash',
                'account_name' => 'Cash Account',
                'account_type' => 'cash',
                'category' => 'current_asset',
                'opening_balance' => 0,
                'current_balance' => 10000,
                'balance' => 10000,
                'total_debit' => 0,
                'total_credit' => 0,
                'currency' => 'USD',
                'is_active' => 1,
                'is_system' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Bank Account
            [
                'user_id' => $userId,
                'account_holder_name' => 'Business Bank',
                'account_name' => 'Bank Account',
                'account_type' => 'bank',
                'category' => 'current_asset',
                'bank_name' => 'Example Bank',
                'bank_branch' => 'Main Branch',
                'account_number' => '1234567890',
                'routing_number' => '021000021',
                'swift_code' => 'EXMPLUS33',
                'opening_balance' => 0,
                'current_balance' => 50000,
                'balance' => 50000,
                'total_debit' => 0,
                'total_credit' => 0,
                'currency' => 'USD',
                'is_active' => 1,
                'is_system' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Mobile Banking Account
            [
                'user_id' => $userId,
                'account_holder_name' => 'Mobile Payment',
                'account_name' => 'Mobile Money',
                'account_type' => 'mobile_banking',
                'category' => 'current_asset',
                'account_number' => '0987654321',
                'opening_balance' => 0,
                'current_balance' => 5000,
                'balance' => 5000,
                'total_debit' => 0,
                'total_credit' => 0,
                'currency' => 'USD',
                'is_active' => 1,
                'is_system' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert each account individually
        foreach ($accounts as $account) {
            DB::table('accounts')->insert($account);
        }
        
        $this->command->info('Accounts seeded successfully!');
    }
}