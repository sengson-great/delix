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
        ];
        
        foreach ($tables as $table => $default) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'status')) {
                // Convert any numeric values
                DB::table($table)
                    ->where('status', '1')
                    ->orWhere('status', 1)
                    ->update(['status' => 'active']);
                    
                DB::table($table)
                    ->where('status', '0')
                    ->orWhere('status', 0)
                    ->update(['status' => 'inactive']);
                
                // Change column type to enum if possible
                try {
                    DB::statement("ALTER TABLE `$table` MODIFY `status` ENUM('active', 'inactive', 'pending', 'approved', 'rejected') DEFAULT '$default'");
                } catch (\Exception $e) {
                    // Just update the data, leave column as string
                    echo "Note: Could not modify $table.status column type\n";
                }
            }
        }
    }

    public function down()
    {
        // No need to revert data changes
    }
};