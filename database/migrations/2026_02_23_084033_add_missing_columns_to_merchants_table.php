<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToMerchantsTable extends Migration
{
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Add all columns without specifying 'after' position
            if (!Schema::hasColumn('merchants', 'vat')) {
                $table->decimal('vat', 8, 2)->default(0)->nullable();
            }
            if (!Schema::hasColumn('merchants', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'zip')) {
                $table->string('zip')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'website')) {
                $table->string('website')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'billing_street')) {
                $table->string('billing_street')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'billing_city')) {
                $table->string('billing_city')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'billing_zip')) {
                $table->string('billing_zip')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'nid')) {
                $table->string('nid')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'trade_license')) {
                $table->string('trade_license')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'api_key')) {
                $table->string('api_key')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'secret_key')) {
                $table->string('secret_key')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'cod_charges')) {
                $table->json('cod_charges')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'charges')) {
                $table->json('charges')->nullable();
            }
            if (!Schema::hasColumn('merchants', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $columns = [
                'vat', 'city', 'zip', 'address', 'website', 
                'billing_street', 'billing_city', 'billing_zip',
                'nid', 'trade_license', 'api_key', 'secret_key',
                'cod_charges', 'charges', 'created_by'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('merchants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}