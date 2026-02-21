<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'users' => 'active',
            'merchants' => 'active',
            'delivery_men' => 'active',
            'branches' => 'active',
            'parcels' => 'pending',
            'payment_methods' => 'active',
            'languages' => 'active',
            'timezones' => 'active',
            'third_parties' => 'inactive',
            'notifications' => 'active',
            'company_accounts' => 'active',
            'fund_transfers' => 'pending',
            'withdraw_requests' => 'pending',
            'withdraw_batches' => 'draft',
        ];
        
        foreach ($tables as $table => $default) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'status')) {
                // Fix empty and null values
                $fixed = DB::table($table)
                    ->where(function($query) {
                        $query->where('status', '')
                              ->orWhereNull('status')
                              ->orWhere('status', 'null')
                              ->orWhere('status', 'NULL');
                    })
                    ->update(['status' => $default]);
                
                if ($fixed > 0) {
                    echo "Fixed $fixed records in $table\n";
                }
                
                // Also fix numeric values
                DB::table($table)
                    ->where('status', '1')
                    ->orWhere('status', 1)
                    ->update(['status' => 'active']);
                
                DB::table($table)
                    ->where('status', '0')
                    ->orWhere('status', 0)
                    ->update(['status' => 'inactive']);
            }
        }
    }

    public function down()
    {
        // No need to revert
    }
};