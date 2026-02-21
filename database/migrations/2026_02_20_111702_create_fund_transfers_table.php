<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fund_transfers')) {
            Schema::create('fund_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_number')->nullable()->unique();
                $table->string('reference')->nullable();
                
                // From account details
                $table->integer('from_account_id')->nullable();
                $table->string('from_account_type')->nullable(); // user, merchant, delivery_man, company
                $table->integer('from_account_holder_id')->nullable();
                $table->string('from_account_holder_name')->nullable();
                
                // To account details
                $table->integer('to_account_id')->nullable();
                $table->string('to_account_type')->nullable(); // user, merchant, delivery_man, company
                $table->integer('to_account_holder_id')->nullable();
                $table->string('to_account_holder_name')->nullable();
                
                // Transfer details
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('charge', 10, 2)->default(0);
                $table->decimal('net_amount', 10, 2)->default(0);
                $table->string('currency')->default('USD');
                $table->decimal('exchange_rate', 10, 4)->default(1);
                
                // Transfer information
                $table->string('transfer_type')->nullable(); // internal, external, bank, mobile
                $table->string('payment_method')->nullable(); // cash, bank, bkash, nagad, rocket
                $table->string('category')->nullable(); // salary, commission, loan, investment, etc.
                $table->text('description')->nullable();
                $table->text('notes')->nullable();
                
                // Status and tracking
                $table->string('status')->default('pending'); // pending, approved, completed, failed, cancelled, reversed
                $table->timestamp('transfer_date')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('reversed_at')->nullable();
                
                // Approval and audit
                $table->integer('requested_by')->nullable();
                $table->integer('approved_by')->nullable();
                $table->integer('processed_by')->nullable();
                $table->integer('reversed_by')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('reversal_reason')->nullable();
                
                // Attachments and references
                $table->string('attachment')->nullable();
                $table->json('metadata')->nullable(); // Additional data
                
                // Transaction references
                $table->string('transaction_id')->nullable();
                $table->string('external_reference')->nullable();
                
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('transfer_number');
                $table->index('from_account_id');
                $table->index('to_account_id');
                $table->index('status');
                $table->index('transfer_date');
                $table->index(['from_account_type', 'from_account_holder_id']);
                $table->index(['to_account_type', 'to_account_holder_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fund_transfers');
    }
};