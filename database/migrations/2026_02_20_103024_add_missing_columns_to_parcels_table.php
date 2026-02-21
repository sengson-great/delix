<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            // Missing columns from the query
            if (!Schema::hasColumn('parcels', 'total_delivery_charge')) {
                $table->decimal('total_delivery_charge', 10, 2)->default(0)->after('delivery_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'fragile_charge')) {
                $table->decimal('fragile_charge', 10, 2)->default(0)->after('total_delivery_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'packaging_charge')) {
                $table->decimal('packaging_charge', 10, 2)->default(0)->after('fragile_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'payable')) {
                $table->decimal('payable', 10, 2)->default(0)->after('price');
            }
            
            if (!Schema::hasColumn('parcels', 'is_partially_delivered')) {
                $table->boolean('is_partially_delivered')->default(0)->after('delivery_status');
            }
            
            if (!Schema::hasColumn('parcels', 'date')) {
                $table->date('date')->nullable()->after('created_at');
            }
            
            // Additional columns that might be needed
            if (!Schema::hasColumn('parcels', 'cod_amount')) {
                $table->decimal('cod_amount', 10, 2)->default(0)->after('price');
            }
            
            if (!Schema::hasColumn('parcels', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('cod_amount');
            }
            
            if (!Schema::hasColumn('parcels', 'total_charge')) {
                $table->decimal('total_charge', 10, 2)->default(0)->after('vat_amount');
            }
            
            if (!Schema::hasColumn('parcels', 'merchant_payable')) {
                $table->decimal('merchant_payable', 10, 2)->default(0)->after('total_charge');
            }
            
            if (!Schema::hasColumn('parcels', 'paid_to_merchant')) {
                $table->boolean('paid_to_merchant')->default(0)->after('merchant_payable');
            }
        });
    }

    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $columns = [
                'total_delivery_charge',
                'fragile_charge',
                'packaging_charge',
                'payable',
                'is_partially_delivered',
                'date',
                'cod_amount',
                'vat_amount',
                'total_charge',
                'merchant_payable',
                'paid_to_merchant'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('parcels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};