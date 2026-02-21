<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('withdraw_sms_templates')) {
            Schema::create('withdraw_sms_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->string('type')->default('withdraw'); // withdraw, deposit, transfer
                $table->string('user_type')->nullable(); // merchant, delivery_man, staff
                $table->string('category')->nullable(); // request, approval, processing, completion, rejection
                $table->string('event_type')->nullable(); // withdraw_requested, withdraw_approved, withdraw_processed, withdraw_completed, withdraw_rejected, withdraw_cancelled
                
                // Template content
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
                $table->text('description')->nullable();
                $table->json('variables')->nullable(); // Available variables: {user_name}, {amount}, {charge}, {payable}, {method}, {account}, {transaction_id}, {date}, {batch_number}, etc.
                $table->json('conditions')->nullable(); // Conditions when this template should be used
                
                // SMS configuration
                $table->string('sender_id')->nullable();
                $table->integer('character_limit')->default(160);
                $table->boolean('is_unicode')->default(0);
                $table->string('language')->default('en');
                
                // Status and settings
                $table->boolean('is_active')->default(1);
                $table->boolean('is_default')->default(0);
                $table->boolean('is_editable')->default(1);
                $table->boolean('is_system')->default(0);
                
                // Priority
                $table->integer('priority')->default(0);
                
                // Usage statistics
                $table->integer('total_sent')->default(0);
                $table->integer('total_delivered')->default(0);
                $table->integer('total_failed')->default(0);
                $table->timestamp('last_used_at')->nullable();
                
                // Version control
                $table->integer('version')->default(1);
                $table->integer('parent_id')->nullable();
                
                // Audit
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('slug');
                $table->index('event_type');
                $table->index('user_type');
                $table->index('category');
                $table->index('is_active');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('withdraw_sms_templates');
    }
};