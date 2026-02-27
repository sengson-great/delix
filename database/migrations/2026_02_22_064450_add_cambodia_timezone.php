<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('timezones')->updateOrInsert(
            ['timezone' => 'Asia/Phnom_Penh'],
            [
                'country_code' => 'KH',
                'timezone' => 'Asia/Phnom_Penh',
                'gmt_offset' => 7.0,
                'dst_offset' => 7.0,
                'raw_offset' => 7.0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down()
    {
        DB::table('timezones')->where('timezone', 'Asia/Phnom_Penh')->delete();
    }
};