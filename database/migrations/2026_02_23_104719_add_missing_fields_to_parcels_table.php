<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToParcelsTable extends Migration
{
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            // First, check if the table has the required columns before adding new ones
            
            // Basic Information
            if (!Schema::hasColumn('parcels', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('merchant_id');
            }
            
            if (!Schema::hasColumn('parcels', 'transfer_to_branch_id')) {
                $table->unsignedBigInteger('transfer_to_branch_id')->nullable()->after('pickup_branch_id');
            }
            
            if (!Schema::hasColumn('parcels', 'delivery_branch_id')) {
                $table->unsignedBigInteger('delivery_branch_id')->nullable()->after('transfer_to_branch_id');
            }
            
            // Delivery Man Information
            if (!Schema::hasColumn('parcels', 'pickup_man_id')) {
                $table->unsignedBigInteger('pickup_man_id')->nullable()->after('delivery_branch_id');
            }
            
            if (!Schema::hasColumn('parcels', 'delivery_man_id')) {
                $table->unsignedBigInteger('delivery_man_id')->nullable()->after('pickup_man_id');
            }
            
            if (!Schema::hasColumn('parcels', 'return_delivery_man_id')) {
                $table->unsignedBigInteger('return_delivery_man_id')->nullable()->after('delivery_man_id');
            }
            
            if (!Schema::hasColumn('parcels', 'transfer_delivery_man_id')) {
                $table->unsignedBigInteger('transfer_delivery_man_id')->nullable()->after('return_delivery_man_id');
            }
            
            // Check if delivery_time exists before adding columns after it
            if (!Schema::hasColumn('parcels', 'delivered_date')) {
                // If delivery_time doesn't exist, don't use after()
                if (Schema::hasColumn('parcels', 'delivery_time')) {
                    $table->date('delivered_date')->nullable()->after('delivery_time');
                } else {
                    $table->date('delivered_date')->nullable();
                }
            }
            
            if (!Schema::hasColumn('parcels', 'returned_date')) {
                if (Schema::hasColumn('parcels', 'delivered_date')) {
                    $table->date('returned_date')->nullable()->after('delivered_date');
                } else {
                    $table->date('returned_date')->nullable();
                }
            }
            
            // Status Information
            if (!Schema::hasColumn('parcels', 'status')) {
                $table->string('status')->default('pending')->after('returned_date');
            }
            
            if (!Schema::hasColumn('parcels', 'status_before_cancel')) {
                if (Schema::hasColumn('parcels', 'status')) {
                    $table->string('status_before_cancel')->nullable()->after('status');
                } else {
                    $table->string('status_before_cancel')->nullable();
                }
            }
            
            if (!Schema::hasColumn('parcels', 'is_partially_delivered')) {
                $table->boolean('is_partially_delivered')->default(false)->after('status_before_cancel');
            }
            
            if (!Schema::hasColumn('parcels', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('is_partially_delivered');
            }
            
            if (!Schema::hasColumn('parcels', 'paid_to_merchant')) {
                $table->boolean('paid_to_merchant')->default(false)->after('is_paid');
            }
            
            // Third Party
            if (!Schema::hasColumn('parcels', 'third_party_id')) {
                if (Schema::hasColumn('parcels', 'paid_to_merchant')) {
                    $table->unsignedBigInteger('third_party_id')->nullable()->after('paid_to_merchant');
                } else {
                    $table->unsignedBigInteger('third_party_id')->nullable();
                }
            }
            
            // Withdraw Information
            if (!Schema::hasColumn('parcels', 'withdraw_id')) {
                $table->unsignedBigInteger('withdraw_id')->nullable()->after('third_party_id');
            }
            
            if (!Schema::hasColumn('parcels', 'withdraw_batch_id')) {
                $table->unsignedBigInteger('withdraw_batch_id')->nullable()->after('withdraw_id');
            }
            
            // Financial Fields
            if (!Schema::hasColumn('parcels', 'cod_amount')) {
                $table->decimal('cod_amount', 10, 2)->default(0)->after('payable');
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
            
            if (!Schema::hasColumn('parcels', 'price_before_delivery')) {
                if (Schema::hasColumn('parcels', 'merchant_payable')) {
                    $table->decimal('price_before_delivery', 10, 2)->default(0)->after('merchant_payable');
                } else {
                    $table->decimal('price_before_delivery', 10, 2)->default(0);
                }
            }
            
            // Location Fields
            if (!Schema::hasColumn('parcels', 'pickup_latitude')) {
                if (Schema::hasColumn('parcels', 'pickup_address')) {
                    $table->decimal('pickup_latitude', 10, 8)->nullable()->after('pickup_address');
                } else {
                    $table->decimal('pickup_latitude', 10, 8)->nullable();
                }
            }
            
            if (!Schema::hasColumn('parcels', 'pickup_longitude')) {
                if (Schema::hasColumn('parcels', 'pickup_latitude')) {
                    $table->decimal('pickup_longitude', 11, 8)->nullable()->after('pickup_latitude');
                } else {
                    $table->decimal('pickup_longitude', 11, 8)->nullable();
                }
            }
            
            if (!Schema::hasColumn('parcels', 'delivery_latitude')) {
                if (Schema::hasColumn('parcels', 'customer_address')) {
                    $table->decimal('delivery_latitude', 10, 8)->nullable()->after('customer_address');
                } else {
                    $table->decimal('delivery_latitude', 10, 8)->nullable();
                }
            }
            
            if (!Schema::hasColumn('parcels', 'delivery_longitude')) {
                if (Schema::hasColumn('parcels', 'delivery_latitude')) {
                    $table->decimal('delivery_longitude', 11, 8)->nullable()->after('delivery_latitude');
                } else {
                    $table->decimal('delivery_longitude', 11, 8)->nullable();
                }
            }
            
            // Audit Fields
            if (!Schema::hasColumn('parcels', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            }
            
            if (!Schema::hasColumn('parcels', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
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
                'branch_id',
                'transfer_to_branch_id',
                'delivery_branch_id',
                'pickup_man_id',
                'delivery_man_id',
                'return_delivery_man_id',
                'transfer_delivery_man_id',
                'delivered_date',
                'returned_date',
                'status',
                'status_before_cancel',
                'is_partially_delivered',
                'is_paid',
                'paid_to_merchant',
                'third_party_id',
                'withdraw_id',
                'withdraw_batch_id',
                'cod_amount',
                'vat_amount',
                'total_charge',
                'merchant_payable',
                'price_before_delivery',
                'pickup_latitude',
                'pickup_longitude',
                'delivery_latitude',
                'delivery_longitude',
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