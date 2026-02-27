<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            // Cambodia
            [
                'name' => 'Cambodia',
                'iso3' => 'KHM',
                'iso2' => 'KH',
                'phonecode' => '855',
                'capital' => 'Phnom Penh',
                'currency' => 'KHR',
                'currency_symbol' => '៛',
                'tld' => '.kh',
                'native' => 'កម្ពុជា',
                'region' => 'Asia',
                'subregion' => 'South-Eastern Asia',
                'timezones' => json_encode([['zoneName' => 'Asia/Phnom_Penh', 'gmtOffset' => 25200, 'gmtOffsetName' => 'UTC+07:00', 'abbreviation' => 'ICT', 'tzName' => 'Indochina Time']]),
                'translations' => json_encode([
                    'kr' => '캄보디아',
                    'pt-BR' => 'Camboja',
                    'pt' => 'Camboja',
                    'nl' => 'Cambodja',
                    'hr' => 'Kambodža',
                    'fa' => 'کامبوج',
                    'de' => 'Kambodscha',
                    'es' => 'Camboya',
                    'fr' => 'Cambodge',
                    'ja' => 'カンボジア',
                    'it' => 'Cambogia',
                    'cn' => '柬埔寨',
                    'tr' => 'Kamboçya'
                ]),
                'latitude' => 11.55,
                'longitude' => 104.9167,
                'emoji' => '🇰🇭',
                'emojiU' => 'U+1F1F0 U+1F1ED',
                'flag' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Add more countries as needed
            [
                'name' => 'United States',
                'iso3' => 'USA',
                'iso2' => 'US',
                'phonecode' => '1',
                'capital' => 'Washington D.C.',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'tld' => '.us',
                'native' => 'United States',
                'region' => 'Americas',
                'subregion' => 'Northern America',
                'timezones' => json_encode([
                    ['zoneName' => 'America/New_York', 'gmtOffset' => -18000, 'gmtOffsetName' => 'UTC-05:00', 'abbreviation' => 'EST', 'tzName' => 'Eastern Standard Time'],
                    ['zoneName' => 'America/Chicago', 'gmtOffset' => -21600, 'gmtOffsetName' => 'UTC-06:00', 'abbreviation' => 'CST', 'tzName' => 'Central Standard Time'],
                    ['zoneName' => 'America/Denver', 'gmtOffset' => -25200, 'gmtOffsetName' => 'UTC-07:00', 'abbreviation' => 'MST', 'tzName' => 'Mountain Standard Time'],
                    ['zoneName' => 'America/Los_Angeles', 'gmtOffset' => -28800, 'gmtOffsetName' => 'UTC-08:00', 'abbreviation' => 'PST', 'tzName' => 'Pacific Standard Time'],
                ]),
                'latitude' => 38.0,
                'longitude' => -97.0,
                'emoji' => '🇺🇸',
                'emojiU' => 'U+1F1FA U+1F1F8',
                'flag' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // United Kingdom
            [
                'name' => 'United Kingdom',
                'iso3' => 'GBR',
                'iso2' => 'GB',
                'phonecode' => '44',
                'capital' => 'London',
                'currency' => 'GBP',
                'currency_symbol' => '£',
                'tld' => '.uk',
                'native' => 'United Kingdom',
                'region' => 'Europe',
                'subregion' => 'Northern Europe',
                'timezones' => json_encode([['zoneName' => 'Europe/London', 'gmtOffset' => 0, 'gmtOffsetName' => 'UTC+00:00', 'abbreviation' => 'GMT', 'tzName' => 'Greenwich Mean Time']]),
                'latitude' => 54.0,
                'longitude' => -2.0,
                'emoji' => '🇬🇧',
                'emojiU' => 'U+1F1EC U+1F1E7',
                'flag' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['iso2' => $country['iso2']],
                $country
            );
        }
    }
}