<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('govt_vats')) {
            Schema::create('govt_vats', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('type')->nullable(); // income, expense
                $table->string('source')->nullable(); // parcel_delivery, parcel_return, etc.
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('percentage', 5, 2)->default(0); // VAT percentage
                $table->decimal('total', 10, 2)->default(0); // total with VAT
                $table->integer('parcel_id')->nullable();
                $table->integer('merchant_id')->nullable();
                $table->integer('branch_id')->nullable();
                $table->date('date')->nullable();
                $table->text('description')->nullable();
                $table->string('reference_number')->nullable();
                $table->string('status')->default('paid'); // paid, due, cancelled
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('govt_vats');
    }
};