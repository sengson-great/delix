<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPaymentAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('merchant_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_ac_name')->nullable();
            $table->string('bank_ac_number')->nullable();
            $table->string('routing_no')->nullable();
            $table->string('bkash_number')->nullable();
            $table->string('bkash_ac_type')->nullable();
            $table->string('rocket_number')->nullable();
            $table->string('rocket_ac_type')->nullable();
            $table->string('nogod_number')->nullable();
            $table->string('nogod_ac_type')->nullable();
            $table->string('selected_bank')->nullable();
            $table->enum('type', ['bank', 'mfs'])->default('bank');
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchant_payment_accounts');
    }
}