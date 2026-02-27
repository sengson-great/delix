<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsAndCompanyAccountIdToDeliveryManAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_man_accounts', function (Blueprint $table) {
            // Add details column if it doesn't exist (as alias for description)
            if (!Schema::hasColumn('delivery_man_accounts', 'details')) {
                $table->text('details')->nullable()->after('description');
            }
            
            // Add company_account_id if it doesn't exist
            if (!Schema::hasColumn('delivery_man_accounts', 'company_account_id')) {
                $table->integer('company_account_id')->nullable()->after('delivery_man_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_man_accounts', function (Blueprint $table) {
            $table->dropColumn(['details', 'company_account_id']);
        });
    }
}