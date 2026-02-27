<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('timezones', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('timezones', 'country_code')) {
                $table->string('country_code', 2)->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('timezones', 'timezone')) {
                $table->string('timezone')->nullable()->after('country_code');
            }
            
            if (!Schema::hasColumn('timezones', 'gmt_offset')) {
                $table->float('gmt_offset')->nullable()->after('timezone');
            }
            
            if (!Schema::hasColumn('timezones', 'dst_offset')) {
                $table->float('dst_offset')->nullable()->after('gmt_offset');
            }
            
            if (!Schema::hasColumn('timezones', 'raw_offset')) {
                $table->float('raw_offset')->nullable()->after('dst_offset');
            }
            
            if (!Schema::hasColumn('timezones', 'status')) {
                $table->boolean('status')->default(1)->after('raw_offset');
            }
        });
    }

    public function down()
    {
        Schema::table('timezones', function (Blueprint $table) {
            $columns = ['country_code', 'timezone', 'gmt_offset', 'dst_offset', 'raw_offset', 'status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('timezones', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};