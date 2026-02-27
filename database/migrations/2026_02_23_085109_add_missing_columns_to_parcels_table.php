<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToParcelsTable extends Migration
{
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            // Add is_partially_delivered if it doesn't exist
            if (!Schema::hasColumn('parcels', 'is_partially_delivered')) {
                $table->boolean('is_partially_delivered')->default(0)->after('status');
            }
            
            // Add is_paid if it doesn't exist
            if (!Schema::hasColumn('parcels', 'is_paid')) {
                $table->boolean('is_paid')->default(0)->after('is_partially_delivered');
            }
            
            // Add withdraw_id if it doesn't exist
            if (!Schema::hasColumn('parcels', 'withdraw_id')) {
                $table->unsignedBigInteger('withdraw_id')->nullable()->after('is_paid');
                // Add foreign key if the merchant_withdraws table exists
                if (Schema::hasTable('merchant_withdraws')) {
                    $table->foreign('withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('set null');
                }
            }
            
            // Add withdraw_batch_id if it doesn't exist (alternative to withdraw_id)
            if (!Schema::hasColumn('parcels', 'withdraw_batch_id')) {
                $table->unsignedBigInteger('withdraw_batch_id')->nullable()->after('withdraw_id');
            }
            
            // Add pickup_branch_id if it doesn't exist
            if (!Schema::hasColumn('parcels', 'pickup_branch_id')) {
                $table->unsignedBigInteger('pickup_branch_id')->nullable()->after('withdraw_batch_id');
            }
            
            // Add delivery_branch_id if it doesn't exist
            if (!Schema::hasColumn('parcels', 'delivery_branch_id')) {
                $table->unsignedBigInteger('delivery_branch_id')->nullable()->after('pickup_branch_id');
            }
            
            // Add pickup_man_id if it doesn't exist
            if (!Schema::hasColumn('parcels', 'pickup_man_id')) {
                $table->unsignedBigInteger('pickup_man_id')->nullable()->after('delivery_branch_id');
            }
            
            // Add delivery_man_id if it doesn't exist
            if (!Schema::hasColumn('parcels', 'delivery_man_id')) {
                $table->unsignedBigInteger('delivery_man_id')->nullable()->after('pickup_man_id');
            }

            if (!Schema::hasColumn('parcels', 'customer_invoice_no')) {
                $table->unsignedBigInteger('customer_invoice_no')->nullable()->after('delivery_man_id');
            }
        });
    }

    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $columns = [
                'is_partially_delivered',
                'is_paid',
                'withdraw_id',
                'withdraw_batch_id',
                'pickup_branch_id',
                'delivery_branch_id',
                'pickup_man_id',
                'delivery_man_id'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('parcels', $column)) {
                    // Drop foreign keys first
                    if (in_array($column, ['withdraw_id', 'pickup_branch_id', 'delivery_branch_id', 'pickup_man_id', 'delivery_man_id'])) {
                        try {
                            $table->dropForeign([$column]);
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