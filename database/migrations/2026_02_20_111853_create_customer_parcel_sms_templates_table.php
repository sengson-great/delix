<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customer_parcel_sms_templates')) {
            Schema::create('customer_parcel_sms_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->string('type')->default('customer'); // customer, merchant, delivery
                $table->string('category')->nullable(); // pickup, delivery, return, delay, etc.
                $table->string('event_type')->nullable(); // parcel_created, parcel_picked_up, parcel_delivered, etc.
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
                $table->text('description')->nullable();
                $table->json('variables')->nullable(); // Available variables: {customer_name}, {parcel_id}, {tracking_id}, etc.
                $table->json('conditions')->nullable(); // Conditions when this template should be used
                
                // SMS configuration
                $table->string('sender_id')->nullable();
                $table->integer('character_limit')->default(160);
                $table->boolean('is_unicode')->default(0);
                $table->string('language')->default('en');
                
                // Timing settings
                $table->boolean('send_immediately')->default(1);
                $table->integer('delay_minutes')->default(0); // Delay before sending
                $table->string('send_time_from')->nullable(); // Only send between these hours
                $table->string('send_time_to')->nullable();
                $table->json('send_days')->nullable(); // ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']
                
                // Status and settings
                $table->boolean('is_active')->default(1);
                $table->boolean('is_default')->default(0);
                $table->boolean('is_editable')->default(1);
                $table->boolean('is_system')->default(0);
                
                // Priority
                $table->integer('priority')->default(0); // Higher priority templates are tried first
                
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
                $table->index('category');
                $table->index('is_active');
                $table->index('language');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('customer_parcel_sms_templates');
    }
};