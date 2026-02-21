<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('cod_charges')) {
            Schema::create('cod_charges', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->text('description')->nullable();
                $table->string('charge_type')->default('percentage'); // percentage, fixed
                $table->decimal('charge_amount', 10, 2)->default(0);
                $table->decimal('min_charge', 10, 2)->default(0); // minimum charge amount
                $table->decimal('max_charge', 10, 2)->nullable(); // maximum charge amount
                $table->decimal('min_order_amount', 10, 2)->default(0); // minimum order amount to apply
                $table->decimal('max_order_amount', 10, 2)->nullable(); // maximum order amount
                $table->string('category')->nullable(); // default, special, promotional
                $table->string('applicable_to')->default('all'); // all, merchant, customer
                $table->integer('merchant_id')->nullable(); // specific merchant
                $table->integer('customer_category_id')->nullable(); // specific customer category
                $table->json('excluded_merchants')->nullable(); // merchants excluded from this charge
                $table->json('excluded_categories')->nullable(); // product categories excluded
                $table->date('valid_from')->nullable();
                $table->date('valid_to')->nullable();
                $table->boolean('is_active')->default(1);
                $table->boolean('is_default')->default(0);
                $table->integer('priority')->default(0); // higher priority applies first
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('is_active');
                $table->index(['valid_from', 'valid_to']);
                $table->index('category');
                $table->index('merchant_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cod_charges');
    }
};