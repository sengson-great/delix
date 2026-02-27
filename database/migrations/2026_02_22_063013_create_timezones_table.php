<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->nullable();
            $table->string('timezone')->unique();  // Fixed typo
            $table->float('gmt_offset')->nullable();
            $table->float('dst_offset')->nullable();
            $table->float('raw_offset')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            // Indexes
            $table->index('country_code');
            $table->index('timezone');
        });
    }

    public function down()
    {
        Schema::dropIfExists('timezones');
    }
};