<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->string('code')->nullable(); // bkash, nagad, rocket, bank, cash, etc.
                $table->string('type')->nullable(); // mobile_banking, bank, cash, card, online
                $table->string('category')->nullable(); // withdrawal, deposit, both
                $table->text('description')->nullable();
                $table->string('logo')->nullable();
                $table->string('icon')->nullable();
                
                // Provider information
                $table->string('provider')->nullable(); // bKash, Nagad, Rocket, etc.
                $table->string('provider_code')->nullable(); // bk, ng, rk, etc.
                $table->string('api_key')->nullable();
                $table->string('api_secret')->nullable();
                $table->string('merchant_number')->nullable();
                $table->string('merchant_wallet')->nullable();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                
                // Fees and charges
                $table->decimal('fixed_charge', 10, 2)->default(0);
                $table->decimal('percentage_charge', 5, 2)->default(0);
                $table->decimal('min_charge', 10, 2)->default(0);
                $table->decimal('max_charge', 10, 2)->nullable();
                $table->decimal('min_amount', 10, 2)->default(0);
                $table->decimal('max_amount', 10, 2)->nullable();
                
                // Settings
                $table->json('config')->nullable(); // Additional configuration
                $table->json('instructions')->nullable(); // Payment instructions
                $table->json('fields')->nullable(); // Required fields for this method
                
                // Availability
                $table->boolean('is_active')->default(1);
                $table->boolean('is_default')->default(0);
                $table->boolean('is_instant')->default(1); // Instant payment or manual
                $table->boolean('is_offline')->default(0); // Offline payment method
                $table->boolean('is_system')->default(0);
                
                // Limits and restrictions
                $table->json('user_types')->nullable(); // ['merchant', 'delivery_man', 'customer']
                $table->json('countries')->nullable(); // Available countries
                $table->json('currencies')->nullable(); // Supported currencies
                
                // Display
                $table->integer('sort_order')->default(0);
                $table->string('status')->default('active');
                
                // Audit
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('slug');
                $table->index('type');
                $table->index('code');
                $table->index('is_active');
                $table->index('is_default');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};