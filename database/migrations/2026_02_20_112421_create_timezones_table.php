<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('timezones')) {
            Schema::create('timezones', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('timezone')->unique(); // Asia/Dhaka, America/New_York, etc.
                $table->string('code')->nullable(); // BST, EST, PST, etc.
                $table->string('gmt_offset')->nullable(); // GMT+6, GMT-5, etc.
                $table->integer('offset_seconds')->nullable(); // 21600, -18000, etc.
                $table->decimal('offset_hours', 5, 2)->nullable(); // 6.00, -5.00, etc.
                $table->string('dst_offset')->nullable(); // Daylight saving time offset
                $table->boolean('has_dst')->default(0); // Has daylight saving time
                $table->string('country_code')->nullable(); // BD, US, GB, etc.
                $table->string('country_name')->nullable();
                $table->json('cities')->nullable(); // Major cities in this timezone
                $table->json('regions')->nullable(); // Regions using this timezone
                $table->boolean('is_default')->default(0);
                $table->boolean('is_active')->default(1);
                $table->integer('sort_order')->default(0);
                $table->string('status')->default('active');
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('timezone');
                $table->index('country_code');
                $table->index('is_active');
                $table->index('is_default');
                $table->index('gmt_offset');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('timezones');
    }
};