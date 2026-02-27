<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('secret')->nullable();
            $table->text('permissions')->nullable();
            $table->enum('type', ['public', 'private', 'third_party'])->default('public');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('key');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_keys');
    }
};