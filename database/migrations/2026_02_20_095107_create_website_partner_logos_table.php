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
        if (!Schema::hasTable('website_partner_logos')) {
        Schema::create('website_partner_logos', function ($table) {
            $table->id();
            $table->text('image')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
        echo "✅ Created website_partner_logos table\n";
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_partner_logos');
    }
};
