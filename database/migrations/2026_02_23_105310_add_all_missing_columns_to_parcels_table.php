<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllMissingColumnsToParcelsTable extends Migration
{
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            // Check and add parcel_no if missing
            if (!Schema::hasColumn('parcels', 'parcel_no')) {
                $table->string('parcel_no')->unique()->after('id');
            }
            
            // Check and add short_url if missing
            if (!Schema::hasColumn('parcels', 'short_url')) {
                $table->string('short_url')->nullable()->after('parcel_no');
            }
            
            // Check and add merchant_id if missing
            if (!Schema::hasColumn('parcels', 'merchant_id')) {
                $table->unsignedBigInteger('merchant_id')->nullable()->after('short_url');
            }
            
            // Check and add user_id if missing
            if (!Schema::hasColumn('parcels', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('merchant_id');
            }
            
            // Check and add shop_id if missing
            if (!Schema::hasColumn('parcels', 'shop_id')) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('user_id');
            }
            
            // Customer Information
            if (!Schema::hasColumn('parcels', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('shop_id');
            }
            
            if (!Schema::hasColumn('parcels', 'customer_invoice_no')) {
                $table->string('customer_invoice_no')->nullable()->after('customer_name');
            }
            
            if (!Schema::hasColumn('parcels', 'customer_phone_number')) {
                $table->string('customer_phone_number')->nullable()->after('customer_invoice_no');
            }
            
            if (!Schema::hasColumn('parcels', 'customer_address')) {
                $table->text('customer_address')->nullable()->after('customer_phone_number');
            }
            
            // Parcel Details
            if (!Schema::hasColumn('parcels', 'weight')) {
                $table->string('weight')->nullable()->after('customer_address');
            }
            
            if (!Schema::hasColumn('parcels', 'parcel_type')) {
                $table->string('parcel_type')->nullable()->after('weight');
            }
            
            if (!Schema::hasColumn('parcels', 'location')) {
                $table->string('location')->nullable()->after('parcel_type');
            }
            
            if (!Schema::hasColumn('parcels', 'note')) {
                $table->text('note')->nullable()->after('location');
            }
            
            // Financial Fields
            if (!Schema::hasColumn('parcels', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('note');
            }
            
            if (!Schema::hasColumn('parcels', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->default(0)->after('price');
            }
            
            if (!Schema::hasColumn('parcels', 'charge')) {
                $table->decimal('charge', 10, 2)->default(0)->after('selling_price');
            }
            
            if (!Schema::hasColumn('parcels', 'cod_charge')) {
                $table->decimal('cod_charge', 10, 2)->default(0)->after('charge');
            }
            
            if (!Schema::hasColumn('parcels', 'vat')) {
                $table->decimal('vat', 10, 2)->default(0)->after('cod_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'total_delivery_charge')) {
                $table->decimal('total_delivery_charge', 10, 2)->default(0)->after('vat');
            }
            
            if (!Schema::hasColumn('parcels', 'payable')) {
                $table->decimal('payable', 10, 2)->default(0)->after('total_delivery_charge');
            }
            
            // Extra Charges
            if (!Schema::hasColumn('parcels', 'fragile')) {
                $table->boolean('fragile')->default(0)->after('payable');
            }
            
            if (!Schema::hasColumn('parcels', 'fragile_charge')) {
                $table->decimal('fragile_charge', 10, 2)->default(0)->after('fragile');
            }
            
            if (!Schema::hasColumn('parcels', 'packaging')) {
                $table->string('packaging')->default('no')->after('fragile_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'packaging_charge')) {
                $table->decimal('packaging_charge', 10, 2)->default(0)->after('packaging');
            }
            
            if (!Schema::hasColumn('parcels', 'open_box')) {
                $table->boolean('open_box')->default(0)->after('packaging_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'home_delivery')) {
                $table->boolean('home_delivery')->default(1)->after('open_box');
            }
            
            // Pickup Information
            if (!Schema::hasColumn('parcels', 'pickup_branch_id')) {
                $table->unsignedBigInteger('pickup_branch_id')->nullable()->after('home_delivery');
            }
            
            if (!Schema::hasColumn('parcels', 'pickup_shop_phone_number')) {
                $table->string('pickup_shop_phone_number')->nullable()->after('pickup_branch_id');
            }
            
            if (!Schema::hasColumn('parcels', 'pickup_address')) {
                $table->text('pickup_address')->nullable()->after('pickup_shop_phone_number');
            }
            
            // Dates
            if (!Schema::hasColumn('parcels', 'pickup_date')) {
                $table->date('pickup_date')->nullable()->after('pickup_address');
            }
            
            if (!Schema::hasColumn('parcels', 'date')) {
                $table->date('date')->nullable()->after('pickup_date');
            }
            
            if (!Schema::hasColumn('parcels', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('date');
            }
            
            // Audit Fields
            if (!Schema::hasColumn('parcels', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('delivery_date');
            }
            
            if (!Schema::hasColumn('parcels', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
            
            if (!Schema::hasColumn('parcels', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $columns = [
                'parcel_no',
                'short_url',
                'merchant_id',
                'user_id',
                'shop_id',
                'customer_name',
                'customer_invoice_no',
                'customer_phone_number',
                'customer_address',
                'weight',
                'parcel_type',
                'location',
                'note',
                'price',
                'selling_price',
                'charge',
                'cod_charge',
                'vat',
                'total_delivery_charge',
                'payable',
                'fragile',
                'fragile_charge',
                'packaging',
                'packaging_charge',
                'open_box',
                'home_delivery',
                'pickup_branch_id',
                'pickup_shop_phone_number',
                'pickup_address',
                'pickup_date',
                'date',
                'delivery_date',
                'created_by',
                'updated_by'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('parcels', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            if (Schema::hasColumn('parcels', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}