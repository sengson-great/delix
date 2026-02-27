<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('timezones', 'raw_offset')) {
            Schema::table('timezones', function (Blueprint $table) {
                $table->float('raw_offset')->nullable()->after('dst_offset');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('timezones', 'raw_offset')) {
            Schema::table('timezones', function (Blueprint $table) {
                $table->dropColumn('raw_offset');
            });
        }
    }
};