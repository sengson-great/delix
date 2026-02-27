<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchIdToMerchantWithdrawsTable extends Migration
{
    public function up()
    {
        Schema::table('merchant_withdraws', function (Blueprint $table) {
            // Add just ONE column - choose which naming convention you want to use
            $table->unsignedBigInteger('withdraw_batch_id')->nullable()->after('withdraw_id');
            
            // Add foreign key constraint
            $table->foreign('withdraw_batch_id')
                  ->references('id')
                  ->on('withdraw_batches')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('merchant_withdraws', function (Blueprint $table) {
            // Drop foreign key first, then the column
            $table->dropForeign(['withdraw_batch_id']);
            $table->dropColumn('withdraw_batch_id');
        });
    }
}