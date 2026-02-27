<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder
{
    public function run()
    {
        $timezones = [
            // Cambodia
            [
                'country_code' => 'KH', 
                'timezone' => 'Asia/Phnom_Penh', 
                'gmt_offset' => 7.0, 
                'dst_offset' => 7.0, 
                'raw_offset' => 7.0
            ],
            
            // Other timezones...
            ['country_code' => 'US', 'timezone' => 'America/New_York', 'gmt_offset' => -5.0, 'dst_offset' => -4.0, 'raw_offset' => -5.0],
            ['country_code' => 'GB', 'timezone' => 'Europe/London', 'gmt_offset' => 0.0, 'dst_offset' => 1.0, 'raw_offset' => 0.0],
            ['country_code' => 'JP', 'timezone' => 'Asia/Tokyo', 'gmt_offset' => 9.0, 'dst_offset' => 9.0, 'raw_offset' => 9.0],
            // ... rest of your timezones
        ];

        foreach ($timezones as $timezone) {
            DB::table('timezones')->updateOrInsert(
                ['timezone' => $timezone['timezone']],
                array_merge($timezone, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}