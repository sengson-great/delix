<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('branches', 'phone_number')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('phone_number')->nullable()->after('address');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('branches', 'phone_number')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('phone_number');
            });
        }
    }
};