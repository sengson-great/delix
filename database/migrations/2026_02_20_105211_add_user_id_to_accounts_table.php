<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('accounts', 'user_id')) {
                $table->integer('user_id')->nullable()->after('id');
            }
            
            // Add other user-related columns that might be needed
            if (!Schema::hasColumn('accounts', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('accounts', 'account_holder_type')) {
                $table->string('account_holder_type')->nullable()->after('account_holder_name'); // user, merchant, delivery_man
            }
            
            if (!Schema::hasColumn('accounts', 'account_holder_id')) {
                $table->integer('account_holder_id')->nullable()->after('account_holder_type');
            }
        });
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $columns = ['user_id', 'account_holder_name', 'account_holder_type', 'account_holder_id'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};