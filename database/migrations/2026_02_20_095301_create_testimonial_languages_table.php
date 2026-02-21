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
        Schema::create('testimonial_languages', function ($table) {
        $table->id();
        $table->integer('testimonial_id');
        $table->string('lang', 10)->default('en');
        $table->string('name')->nullable();
        $table->string('title')->nullable();
        $table->string('designation')->nullable();
        $table->text('description')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonial_languages');
    }
};
