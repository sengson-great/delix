<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWithdrawIdToParcelsTable extends Migration
{
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (!Schema::hasColumn('parcels', 'withdraw_id')) {
                $table->unsignedBigInteger('withdraw_id')->nullable();
                $table->foreign('withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (Schema::hasColumn('parcels', 'withdraw_id')) {
                $table->dropForeign(['withdraw_id']);
                $table->dropColumn('withdraw_id');
            }
        });
    }
}