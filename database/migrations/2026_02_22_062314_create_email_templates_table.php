<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('email_type')->unique();
            $table->longText('body');
            $table->text('variables')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('language')->default('en');
            $table->timestamps();
            
            // Indexes
            $table->index('email_type');
            $table->index('status');
            $table->index('language');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
};