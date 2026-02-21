<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('merchant_accounts')) {
            Schema::create('merchant_accounts', function (Blueprint $table) {
                $table->id();
                $table->integer('merchant_id')->nullable();
                $table->string('type')->nullable(); // income, expense
                $table->string('source')->nullable(); // parcel_return, vat_adjustment, delivery_charge, cod_charge, etc.
                $table->string('details')->nullable(); // govt_vat_for_parcel_return, govt_vat_for_parcel_return_reversed, etc.
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('balance', 10, 2)->default(0); // running balance
                $table->integer('parcel_id')->nullable();
                $table->integer('reference_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->date('date')->nullable();
                $table->text('description')->nullable();
                $table->string('transaction_id')->nullable()->unique();
                $table->string('status')->default('completed'); // pending, completed, cancelled
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes for faster queries
                $table->index(['merchant_id', 'date']);
                $table->index(['source', 'details']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('merchant_accounts');
    }
};