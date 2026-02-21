<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notification_users')) {
            Schema::create('notification_users', function (Blueprint $table) {
                $table->id();
                $table->integer('notification_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->boolean('is_read')->default(0);
                $table->timestamp('read_at')->nullable();
                $table->boolean('is_archived')->default(0);
                $table->timestamp('archived_at')->nullable();
                $table->boolean('is_deleted')->default(0);
                $table->timestamps();
                
                // Indexes
                $table->index(['user_id', 'is_read']);
                $table->index(['user_id', 'is_archived']);
                $table->index('notification_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notification_users');
    }
};