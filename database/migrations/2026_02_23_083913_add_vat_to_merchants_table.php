<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatToMerchantsTable extends Migration
{
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->decimal('vat', 8, 2)->default(0)->after('company');
        });
    }

    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('vat');
        });
    }
}