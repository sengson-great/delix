<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToDeliveryMenTable extends Migration
{
    public function up()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_men', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('delivery_men', 'city')) {
                $table->string('city')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('delivery_men', 'zip')) {
                $table->string('zip')->nullable()->after('city');
            }
            if (!Schema::hasColumn('delivery_men', 'address')) {
                $table->text('address')->nullable()->after('zip');
            }
        });
    }

    public function down()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $columns = ['phone_number', 'city', 'zip', 'address'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('delivery_men', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}