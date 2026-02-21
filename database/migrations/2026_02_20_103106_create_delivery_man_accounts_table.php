<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('delivery_man_accounts')) {
            Schema::create('delivery_man_accounts', function (Blueprint $table) {
                $table->id();
                $table->integer('delivery_man_id')->nullable();
                $table->string('type')->nullable(); // income, expense
                $table->string('source')->nullable(); // pickup_commission, parcel_delivery, parcel_return, cash_given_to_staff, etc.
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
                $table->index(['delivery_man_id', 'date']);
                $table->index('source');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('delivery_man_accounts');
    }
};