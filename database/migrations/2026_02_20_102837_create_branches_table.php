<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('branch_code')->nullable()->unique();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('phone_country_id')->nullable();
                $table->string('address')->nullable();
                $table->integer('country_id')->nullable();
                $table->integer('city_id')->nullable();
                $table->integer('zone_id')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('manager_name')->nullable();
                $table->string('manager_phone')->nullable();
                $table->string('manager_email')->nullable();
                $table->string('status')->default('active'); // active, inactive
                $table->integer('total_staff')->default(0);
                $table->integer('total_delivery_men')->default(0);
                $table->integer('total_parcels')->default(0);
                $table->integer('total_merchants')->default(0);
                $table->text('description')->nullable();
                $table->string('opening_time')->nullable();
                $table->string('closing_time')->nullable();
                $table->json('working_days')->nullable(); // ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
};