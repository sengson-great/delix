<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_men', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            //
        });
    }
};
