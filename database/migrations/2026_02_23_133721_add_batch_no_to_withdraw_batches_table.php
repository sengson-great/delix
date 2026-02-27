<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchNoToWithdrawBatchesTable extends Migration
{
    public function up()
    {
        Schema::table('withdraw_batches', function (Blueprint $table) {
            $table->string('batch_no')->after('title')->nullable();
        });
    }

    public function down()
    {
        Schema::table('withdraw_batches', function (Blueprint $table) {
            $table->dropColumn('batch_no');
        });
    }
}