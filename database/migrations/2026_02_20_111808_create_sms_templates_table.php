<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sms_templates')) {
            Schema::create('sms_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->string('category')->nullable(); // parcel, merchant, delivery, otp, promotion, alert
                $table->string('type')->default('transactional'); // transactional, promotional, alert, otp
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
                $table->text('description')->nullable();
                $table->json('variables')->nullable(); // Available variables for the template
                $table->integer('character_limit')->default(160);
                $table->boolean('is_unicode')->default(0);
                $table->string('sender_id')->nullable(); // Custom sender ID
                $table->string('language')->default('en');
                
                // Status and settings
                $table->boolean('is_active')->default(1);
                $table->boolean('is_default')->default(0);
                $table->boolean('is_editable')->default(1);
                $table->boolean('is_system')->default(0); // System template, cannot be deleted
                
                // Usage statistics
                $table->integer('total_sent')->default(0);
                $table->integer('total_delivered')->default(0);
                $table->integer('total_failed')->default(0);
                $table->timestamp('last_used_at')->nullable();
                
                // Versioning
                $table->integer('version')->default(1);
                $table->integer('parent_id')->nullable(); // For template versions
                
                // Approval (if needed)
                $table->boolean('is_approved')->default(1);
                $table->integer('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                
                // Audit
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('slug');
                $table->index('category');
                $table->index('type');
                $table->index('is_active');
                $table->index('language');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sms_templates');
    }
};