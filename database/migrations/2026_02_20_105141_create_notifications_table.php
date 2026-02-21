<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->string('type')->default('info'); // info, success, warning, danger
                $table->string('icon')->nullable();
                $table->string('action_url')->nullable();
                $table->string('action_text')->nullable();
                $table->string('image')->nullable();
                $table->integer('sender_id')->nullable(); // user who sent the notification
                $table->string('sender_type')->nullable(); // system, admin, etc.
                $table->boolean('is_global')->default(0);
                $table->string('user_type')->nullable(); // staff, merchant, delivery_man
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->integer('priority')->default(0); // 0: normal, 1: high, 2: urgent
                $table->json('data')->nullable(); // additional data
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index(['user_type', 'created_at']);
                $table->index('priority');
                $table->index('expires_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};