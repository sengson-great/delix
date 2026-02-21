<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('merchant_withdraws')) {
            Schema::create('merchant_withdraws', function (Blueprint $table) {
                $table->id();
                $table->integer('merchant_id')->nullable();
                $table->string('withdraw_id')->nullable()->unique();
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('charge', 10, 2)->default(0);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->string('payment_method')->nullable(); // bank, bkash, nagad, rocket, etc.
                $table->string('account_number')->nullable();
                $table->string('account_holder_name')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('routing_number')->nullable();
                $table->string('mobile_banking_number')->nullable();
                $table->string('transaction_id')->nullable();
                $table->date('date')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('pending'); // pending, approved, processed, rejected, cancelled
                $table->text('rejection_reason')->nullable();
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->integer('processed_by')->nullable();
                $table->integer('rejected_by')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index(['merchant_id', 'status']);
                $table->index('date');
                $table->index('status');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('merchant_withdraws');
    }
};