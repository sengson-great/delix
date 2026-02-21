<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Add company column if it doesn't exist
            if (!Schema::hasColumn('merchants', 'company')) {
                $table->string('company')->nullable()->after('user_id');
            }
            
            // Add default_account_id column if it doesn't exist
            if (!Schema::hasColumn('merchants', 'default_account_id')) {
                $table->integer('default_account_id')->nullable()->after('phone_number');
            }
            
            // Ensure user_id column exists
            if (!Schema::hasColumn('merchants', 'user_id')) {
                $table->integer('user_id')->nullable()->after('id');
            }
            
            // Ensure phone_number column exists
            if (!Schema::hasColumn('merchants', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('company');
            }
        });
    }

    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $columns = ['company', 'default_account_id'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('merchants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};