<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryFeeFieldsToDeliveryMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            // Add missing columns
            //$table->text('address')->nullable()->after('zip');
            $table->decimal('delivery_fee', 10, 2)->default(0.00)->after('balance');
            $table->decimal('pick_up_fee', 10, 2)->default(0.00)->after('delivery_fee');
            $table->decimal('return_fee', 10, 2)->default(0.00)->after('pick_up_fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropColumn(['address', 'delivery_fee', 'pick_up_fee', 'return_fee']);
        });
    }
}