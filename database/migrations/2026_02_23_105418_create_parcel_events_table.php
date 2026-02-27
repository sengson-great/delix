<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelEventsTable extends Migration
{
    public function up()
    {
        Schema::create('parcel_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->unsignedBigInteger('pickup_man_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->text('cancel_note')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('third_party_id')->nullable();
            $table->unsignedBigInteger('transfer_delivery_man_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('parcel_id');
            $table->index('delivery_man_id');
            $table->index('pickup_man_id');
            $table->index('user_id');
            $table->index('title');
            $table->index('branch_id');
            $table->index('third_party_id');
            $table->index('transfer_delivery_man_id');
            $table->index('created_by');
            
            // Add foreign keys if the referenced tables exist
            if (Schema::hasTable('parcels')) {
                $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('cascade');
            }
            
            if (Schema::hasTable('delivery_men')) {
                $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onDelete('set null');
                $table->foreign('pickup_man_id')->references('id')->on('delivery_men')->onDelete('set null');
                $table->foreign('transfer_delivery_man_id')->references('id')->on('delivery_men')->onDelete('set null');
            }
            
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (Schema::hasTable('branches')) {
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            }
            
            if (Schema::hasTable('third_parties')) {
                $table->foreign('third_party_id')->references('id')->on('third_parties')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('parcel_events');
    }
}