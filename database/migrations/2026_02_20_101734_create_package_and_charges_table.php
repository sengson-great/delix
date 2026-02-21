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
        if (!Schema::hasTable('package_and_charges')) {
    Schema::create('package_and_charges', function ($table) {
        $table->id();
        $table->string('package_name')->nullable();
        $table->string('slug')->nullable();
        $table->decimal('weight', 10, 2)->default(0);
        $table->decimal('same_day', 10, 2)->default(0);
        $table->decimal('next_day', 10, 2)->default(0);
        $table->decimal('sub_city', 10, 2)->default(0);
        $table->decimal('outside_city', 10, 2)->default(0);
        $table->boolean('status')->default(1);
        $table->timestamps();
    });
    echo "✅ Created package_and_charges table\n";
} else {
    echo "✅ package_and_charges table already exists\n";
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_and_charges');
    }
};
