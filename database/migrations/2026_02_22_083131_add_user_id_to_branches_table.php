<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('branches', 'user_id')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('branches', 'user_id')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};