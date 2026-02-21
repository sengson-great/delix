<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    if (!Schema::hasTable('merchants')) {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('pickup_address')->nullable();
            $table->string('return_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_country_id')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('trade_license')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bkash_number')->nullable();
            $table->string('nagad_number')->nullable();
            $table->string('rocket_number')->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('due', 10, 2)->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('total_parcels')->default(0);
            $table->integer('total_delivered')->default(0);
            $table->integer('total_returned')->default(0);
            $table->integer('total_cancelled')->default(0);
            $table->string('status')->default('active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }
}

public function down()
{
    Schema::dropIfExists('merchants');
}
};
