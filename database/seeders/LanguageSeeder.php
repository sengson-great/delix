<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            // English (default)
            [
                'name' => 'English',
                'native_name' => 'English',
                'locale' => 'en',
                'code' => 'en-US',
                'flag' => 'us',
                'flag_code' => 'us',
                'direction' => 'ltr',
                'is_default' => 1,
                'is_active' => 1,
                'is_rtl' => 0,
                'sort_order' => 1,
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'datetime_format' => 'Y-m-d H:i:s',
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'decimal_places' => 2,
                'currency_symbol' => '$',
                'currency_position' => 'before',
                'currency_separator' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Khmer (Cambodian)
            [
                'name' => 'Khmer',
                'native_name' => 'ភាសាខ្មែរ',
                'locale' => 'km',
                'code' => 'km-KH',
                'flag' => 'kh',
                'flag_code' => 'kh',
                'direction' => 'ltr',
                'is_default' => 0,
                'is_active' => 1,
                'is_rtl' => 0,
                'sort_order' => 2,
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'datetime_format' => 'Y-m-d H:i:s',
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'decimal_places' => 2,
                'currency_symbol' => '៛',
                'currency_position' => 'after',
                'currency_separator' => ' ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($languages as $language) {
            DB::table('languages')->updateOrInsert(
                ['locale' => $language['locale']],
                $language
            );
        }
    }
}