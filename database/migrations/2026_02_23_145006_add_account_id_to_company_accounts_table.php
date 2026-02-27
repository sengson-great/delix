<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToCompanyAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('user_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
}