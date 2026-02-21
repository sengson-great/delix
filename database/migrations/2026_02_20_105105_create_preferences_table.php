<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('preferences')) {
            Schema::create('preferences', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('title')->nullable();
                $table->text('value')->nullable();
                $table->string('type')->default('text'); // text, number, boolean, select, image, etc.
                $table->string('category')->nullable(); // general, notification, system, etc.
                $table->text('options')->nullable(); // JSON array for select options
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_public')->default(0); // visible to users
                $table->boolean('is_system')->default(0); // system preference, cannot be deleted
                $table->boolean('is_editable')->default(1);
                $table->string('data_type')->default('string'); // string, integer, float, boolean, json
                $table->string('validation_rules')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('category');
                $table->index('is_public');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('preferences');
    }
};