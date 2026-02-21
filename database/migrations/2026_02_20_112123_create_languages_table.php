<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('native_name')->nullable();
                $table->string('locale')->unique(); // en, bn, ar, etc.
                $table->string('code')->nullable(); // en-US, bn-BD, etc.
                $table->string('flag')->nullable(); // flag icon or image
                $table->string('flag_code')->nullable(); // us, bd, sa, etc.
                $table->string('direction')->default('ltr'); // ltr, rtl
                $table->boolean('is_default')->default(0);
                $table->boolean('is_active')->default(1);
                $table->boolean('is_rtl')->default(0);
                $table->integer('sort_order')->default(0);
                
                // Date and time formats
                $table->string('date_format')->default('Y-m-d');
                $table->string('time_format')->default('H:i:s');
                $table->string('datetime_format')->default('Y-m-d H:i:s');
                
                // Number formats
                $table->string('decimal_separator')->default('.');
                $table->string('thousand_separator')->default(',');
                $table->integer('decimal_places')->default(2);
                
                // Currency format
                $table->string('currency_symbol')->default('$');
                $table->string('currency_position')->default('before'); // before, after
                $table->string('currency_separator')->default('');
                
                // Files and paths
                $table->string('flag_path')->nullable();
                $table->string('icon_path')->nullable();
                $table->string('translation_file')->nullable();
                
                // Statistics
                $table->integer('total_translated')->default(0);
                $table->integer('total_missing')->default(0);
                $table->float('translation_progress')->default(0);
                
                // Meta
                $table->string('meta_title')->nullable();
                $table->string('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                
                // Fallback
                $table->string('fallback_locale')->nullable();
                
                // System
                $table->boolean('is_system')->default(0);
                $table->boolean('is_editable')->default(1);
                
                // Audit
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('locale');
                $table->index('is_active');
                $table->index('is_default');
                $table->index('sort_order');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('languages');
    }
};