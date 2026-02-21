<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_code')->nullable()->unique();
                $table->string('account_name')->nullable();
                $table->string('account_type')->nullable(); // asset, liability, equity, income, expense
                $table->string('category')->nullable(); // current_asset, fixed_asset, current_liability, etc.
                $table->string('sub_category')->nullable();
                $table->text('description')->nullable();
                $table->decimal('opening_balance', 10, 2)->default(0);
                $table->decimal('current_balance', 10, 2)->default(0);
                $table->decimal('total_debit', 10, 2)->default(0);
                $table->decimal('total_credit', 10, 2)->default(0);
                $table->string('currency')->default('USD');
                $table->boolean('is_active')->default(1);
                $table->boolean('is_system')->default(0); // system account, cannot be deleted
                $table->integer('parent_account_id')->nullable(); // for sub-accounts
                $table->string('bank_name')->nullable();
                $table->string('bank_branch')->nullable();
                $table->string('account_number')->nullable();
                $table->string('routing_number')->nullable();
                $table->string('swift_code')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('contact_email')->nullable();
                $table->text('address')->nullable();
                $table->string('notes')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('account_type');
                $table->index('is_active');
                $table->index('parent_account_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};