<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Add balance column if it doesn't exist
            if (!Schema::hasColumn('accounts', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0)->after('current_balance');
            }
            
            // Also ensure other related columns exist
            if (!Schema::hasColumn('accounts', 'current_balance')) {
                $table->decimal('current_balance', 10, 2)->default(0)->after('opening_balance');
            }
            
            if (!Schema::hasColumn('accounts', 'total_debit')) {
                $table->decimal('total_debit', 10, 2)->default(0)->after('current_balance');
            }
            
            if (!Schema::hasColumn('accounts', 'total_credit')) {
                $table->decimal('total_credit', 10, 2)->default(0)->after('total_debit');
            }
        });
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $columns = ['balance', 'current_balance', 'total_debit', 'total_credit'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};