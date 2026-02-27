<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToMerchantAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('merchant_accounts', function (Blueprint $table) {
            // Add payment_withdraw_id if it doesn't exist
            if (!Schema::hasColumn('merchant_accounts', 'payment_withdraw_id')) {
                $table->unsignedBigInteger('payment_withdraw_id')->nullable()->after('amount');
                // Add foreign key if the payment_withdraws table exists
                if (Schema::hasTable('payment_withdraws')) {
                    $table->foreign('payment_withdraw_id')->references('id')->on('payment_withdraws')->onDelete('set null');
                }
            }
            
            // Add is_paid if it doesn't exist
            if (!Schema::hasColumn('merchant_accounts', 'is_paid')) {
                $table->boolean('is_paid')->default(0)->after('payment_withdraw_id');
            }
            
            // Add date if it doesn't exist
            if (!Schema::hasColumn('merchant_accounts', 'date')) {
                $table->date('date')->nullable()->after('is_paid');
            }
            
            // Add created_by if it doesn't exist
            if (!Schema::hasColumn('merchant_accounts', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('date');
            }
            
            // Add updated_by if it doesn't exist
            if (!Schema::hasColumn('merchant_accounts', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down()
    {
        Schema::table('merchant_accounts', function (Blueprint $table) {
            $columns = ['payment_withdraw_id', 'is_paid', 'date', 'created_by', 'updated_by'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('merchant_accounts', $column)) {
                    // Drop foreign keys first
                    if ($column === 'payment_withdraw_id') {
                        try {
                            $table->dropForeign(['payment_withdraw_id']);
                        } catch (\Exception $e) {
                            // Foreign key might not exist
                        }
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
}