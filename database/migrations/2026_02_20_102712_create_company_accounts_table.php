<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('company_accounts')) {
            Schema::create('company_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('type')->nullable(); // income, expense
                $table->string('create_type')->default('user_defined'); // user_defined, system
                $table->string('source')->nullable(); // delivery_charge_receive_from_merchant, cash_receive_from_delivery_man, etc.
                $table->decimal('amount', 10, 2)->default(0);
                $table->text('description')->nullable();
                $table->integer('reference_id')->nullable(); // parcel_id, merchant_id, etc.
                $table->string('reference_type')->nullable(); // parcel, merchant, etc.
                $table->date('transaction_date')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('company_accounts');
    }
};