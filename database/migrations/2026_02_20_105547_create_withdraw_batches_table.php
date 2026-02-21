<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('withdraw_batches')) {
            Schema::create('withdraw_batches', function (Blueprint $table) {
                $table->id();
                $table->string('batch_number')->nullable()->unique();
                $table->string('title')->nullable();
                $table->string('type')->nullable(); // merchant, delivery_man, staff
                $table->date('batch_date')->nullable();
                $table->integer('total_requests')->default(0);
                $table->integer('total_processed')->default(0);
                $table->integer('total_pending')->default(0);
                $table->integer('total_rejected')->default(0);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->decimal('total_charge', 10, 2)->default(0);
                $table->decimal('total_payable', 10, 2)->default(0);
                $table->decimal('total_processed_amount', 10, 2)->default(0);
                $table->decimal('total_pending_amount', 10, 2)->default(0);
                $table->decimal('total_rejected_amount', 10, 2)->default(0);
                $table->string('status')->default('draft'); // draft, processing, completed, cancelled
                $table->timestamp('processed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->string('payment_method')->nullable(); // bank, bkash, nagad, rocket
                $table->string('file_path')->nullable(); // batch file for bank transfer
                $table->text('notes')->nullable();
                $table->json('summary')->nullable(); // JSON summary of batch
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->integer('processed_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('batch_number');
                $table->index('status');
                $table->index('type');
                $table->index('batch_date');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('withdraw_batches');
    }
};