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
        if (!Schema::hasTable('website_features')) {
    Schema::create('website_features', function ($table) {
        $table->id();
        $table->text('image')->nullable();
        $table->string('title')->nullable();
        $table->text('description')->nullable();
        $table->boolean('status')->default(1);
        $table->timestamps();
    });
    echo "✅ Created website_features table\n";
} else {
    echo "✅ website_features table already exists\n";
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_features');
    }
};
