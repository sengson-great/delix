<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_news_and_events', function ($table) {
        $table->id();
        $table->text('image')->nullable();
        $table->string('title')->nullable();
        $table->text('description')->nullable();
        $table->boolean('status')->default(1);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_news_and_events');
    }
};
