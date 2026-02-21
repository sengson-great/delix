<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('delivery_men')) {
            Schema::create('delivery_men', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('phone_country_id')->nullable();
                $table->string('address')->nullable();
                $table->integer('country_id')->nullable();
                $table->integer('city_id')->nullable();
                $table->integer('zone_id')->nullable();
                $table->string('nid_number')->nullable();
                $table->string('nid_image')->nullable();
                $table->string('driving_license')->nullable();
                $table->string('vehicle_type')->nullable(); // bike, cycle, car, etc.
                $table->string('vehicle_number')->nullable();
                $table->string('profile_image')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('status')->default('active'); // active, inactive, busy, offline
                $table->boolean('is_online')->default(false);
                $table->boolean('is_available')->default(true);
                $table->decimal('balance', 10, 2)->default(0);
                $table->decimal('total_earned', 10, 2)->default(0);
                $table->integer('total_deliveries')->default(0);
                $table->integer('total_ratings')->default(0);
                $table->decimal('average_rating', 3, 2)->default(0);
                $table->timestamp('last_activity')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('delivery_men');
    }
};