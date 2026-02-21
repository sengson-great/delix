<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('languages', function (Blueprint $table) {
            // First add the status column
            if (!Schema::hasColumn('languages', 'status')) {
                $table->string('status')->default('active')->after('is_active');
            }
        });

        // Then update the status values in a separate step
        // This runs after the column is added
        if (Schema::hasColumn('languages', 'status') && Schema::hasColumn('languages', 'is_active')) {
            DB::table('languages')
                ->where('is_active', 1)
                ->update(['status' => 'active']);
                
            DB::table('languages')
                ->where('is_active', 0)
                ->update(['status' => 'inactive']);
        }
    }

    public function down()
    {
        Schema::table('languages', function (Blueprint $table) {
            if (Schema::hasColumn('languages', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};