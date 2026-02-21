<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('parcels')) {
    Schema::create('parcels', function ($table) {
        $table->id();
        $table->string('parcel_id')->nullable()->unique();
        $table->integer('merchant_id')->nullable();
        $table->integer('branch_id')->nullable();
        $table->integer('delivery_man_id')->nullable();
        $table->integer('pickup_man_id')->nullable();
        
        // Customer Information
        $table->string('customer_name')->nullable();
        $table->string('customer_phone')->nullable();
        $table->string('customer_address')->nullable();
        $table->integer('customer_country_id')->nullable();
        $table->integer('customer_city_id')->nullable();
        $table->integer('customer_zone_id')->nullable();
        
        // Recipient Information
        $table->string('recipient_name')->nullable();
        $table->string('recipient_phone')->nullable();
        $table->string('recipient_address')->nullable();
        $table->integer('recipient_country_id')->nullable();
        $table->integer('recipient_city_id')->nullable();
        $table->integer('recipient_zone_id')->nullable();
        
        // Product Information
        $table->string('product_category')->nullable();
        $table->string('product_type')->nullable();
        $table->string('product_name')->nullable();
        $table->text('product_description')->nullable();
        $table->decimal('weight', 10, 2)->default(0);
        $table->integer('quantity')->default(1);
        $table->decimal('price', 10, 2)->default(0);
        
        // Delivery Information
        $table->string('delivery_type')->nullable(); // same_day, next_day, sub_city, outside_city
        $table->decimal('delivery_charge', 10, 2)->default(0);
        $table->decimal('cod_charge', 10, 2)->default(0);
        $table->decimal('vat', 10, 2)->default(0);
        $table->decimal('total_charge', 10, 2)->default(0);
        
        // Status Information
        $table->string('status')->default('pending');
        $table->text('status_history')->nullable();
        $table->string('payment_status')->default('unpaid');
        $table->string('delivery_status')->default('pending');
        
        // Tracking Information
        $table->string('tracking_code')->nullable()->unique();
        $table->text('tracking_history')->nullable();
        
        // Timestamps
        $table->timestamp('pickup_date')->nullable();
        $table->timestamp('delivery_date')->nullable();
        $table->timestamp('expected_delivery_date')->nullable();
        
        $table->integer('created_by')->nullable();
        $table->integer('updated_by')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
    echo "✅ Created parcels table\n";
} else {
    echo "✅ parcels table already exists\n";
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
