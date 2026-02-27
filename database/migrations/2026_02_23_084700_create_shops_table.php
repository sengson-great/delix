<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->string('shop_name');
            $table->string('contact_number')->nullable();
            $table->unsignedBigInteger('pickup_branch_id')->nullable();
            $table->string('shop_phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->boolean('default')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('pickup_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('merchant_id');
            $table->index('pickup_branch_id');
            $table->index('default');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shops');
    }
}