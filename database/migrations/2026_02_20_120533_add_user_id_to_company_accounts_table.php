<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('company_accounts', 'user_id')) {
                $table->integer('user_id')->nullable()->after('id');
            }
            
            // Add index for better performance
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::table('company_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('company_accounts', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};