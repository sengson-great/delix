<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('third_parties')) {
            Schema::create('third_parties', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->string('type')->nullable(); // sms, email, payment, map, tracking, etc.
                $table->string('provider')->nullable(); // twilio, nexmo, sslcommerz, stripe, etc.
                $table->text('description')->nullable();
                $table->string('api_key')->nullable();
                $table->string('api_secret')->nullable();
                $table->string('api_url')->nullable();
                $table->string('api_version')->nullable();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->string('merchant_id')->nullable();
                $table->string('store_id')->nullable();
                $table->string('signature_key')->nullable();
                $table->json('credentials')->nullable(); // Additional credentials in JSON format
                $table->json('config')->nullable(); // Configuration settings
                $table->json('options')->nullable(); // Additional options
                $table->string('status')->default('inactive'); // active, inactive, test
                $table->boolean('is_default')->default(0);
                $table->boolean('is_test_mode')->default(0);
                $table->integer('priority')->default(0);
                $table->string('logo')->nullable();
                $table->string('website')->nullable();
                $table->string('support_email')->nullable();
                $table->string('support_phone')->nullable();
                $table->text('notes')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('type');
                $table->index('provider');
                $table->index('status');
                $table->index('is_default');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('third_parties');
    }
};