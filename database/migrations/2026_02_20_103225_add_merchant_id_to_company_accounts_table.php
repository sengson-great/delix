<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            // Add merchant_id column if it doesn't exist
            if (!Schema::hasColumn('company_accounts', 'merchant_id')) {
                $table->integer('merchant_id')->nullable()->after('reference_type');
            }
            
            // Add any other missing columns that might be needed
            if (!Schema::hasColumn('company_accounts', 'date')) {
                $table->date('date')->nullable()->after('updated_at');
            }
            
            if (!Schema::hasColumn('company_accounts', 'source')) {
                $table->string('source')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('company_accounts', 'create_type')) {
                $table->string('create_type')->default('user_defined')->after('source');
            }
        });
    }

    public function down()
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $columns = ['merchant_id', 'date', 'source', 'create_type'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('company_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};