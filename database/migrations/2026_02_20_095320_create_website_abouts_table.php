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
        Schema::create('website_abouts', function ($table) {
        $table->id();
        $table->text('icon')->nullable();
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
        Schema::dropIfExists('website_abouts');
    }
};
