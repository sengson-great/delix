<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, get the actual columns from the settings table
        $columns = Schema::getColumnListing('settings');
        echo "Available columns: " . implode(', ', $columns) . "\n";

        $settings = [
            [
                'id' => 1,
                'title' => 'return_charge',
                'value' => '40',
                'lang' => NULL,
                'created_at' => '2021-07-23 11:22:58',
                'updated_at' => '2021-07-23 11:22:58'
            ],
            [
                'id' => 2,
                'title' => 'fragile_charge',
                'value' => '20',
                'lang' => NULL,
                'created_at' => '2021-07-23 11:22:58',
                'updated_at' => '2024-01-09 13:39:17'
            ],
            [
                'id' => 3,
                'title' => 'pickup_accept_start',
                'value' => '18',
                'lang' => NULL,
                'created_at' => '2021-07-23 11:23:04',
                'updated_at' => '2021-07-23 11:23:04'
            ],
            [
                'id' => 4,
                'title' => 'pickup_accept_end',
                'value' => '24',
                'lang' => NULL,
                'created_at' => '2021-07-23 11:23:04',
                'updated_at' => '2021-07-23 11:23:04'
            ],
            // Continue with all settings but REMOVE created_by and updated_by from each array
        ];

        // Filter each setting to only include columns that exist in the table
        foreach ($settings as $index => $setting) {
            $filteredSetting = [];
            foreach ($setting as $key => $value) {
                if (in_array($key, $columns)) {
                    $filteredSetting[$key] = $value;
                }
            }

            try {
                DB::table('settings')->updateOrInsert(
                    ['id' => $filteredSetting['id']],
                    $filteredSetting
                );
                echo "✅ Inserted setting ID: {$filteredSetting['id']}\n";
            } catch (Exception $e) {
                echo "❌ Failed to insert setting ID {$filteredSetting['id']}: " . $e->getMessage() . "\n";
            }
        }

        $services = [
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134138_original_service_image120.jpg","image_358X270":"images\/20240624134138image_358X270service_image492.jpg","image_80x80":"images\/20240624134138image_80x80service_image119.jpg"}',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'status' => 1,
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-25 10:27:35'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134204_original_service_image369.jpg","image_358X270":"images\/20240624134204image_358X270service_image232.jpg","image_80x80":"images\/20240624134204image_80x80service_image61.jpg"}',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            // ... include the rest without 'id' field
        ];

        foreach ($services as $service) {
            DB::table('website_services')->insert($service);
        }

        $serviceLanguages = [
            [
                'website_service_id' => 1,
                'lang' => 'en',
                'title' => 'Document Deliveries',
                'description' => 'Trax offers a secure and digitized process to send highly sensitive shipments like SIMs, debit and credit cards.',
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-24 13:41:39'
            ],
            [
                'website_service_id' => 2,
                'lang' => 'en',
                'title' => 'Bulk Movements',
                'description' => 'We can help you with moving heavy shipments across USA, which will take the weight off your shoulders.',
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-24 13:42:04'
            ],
            [
                'website_service_id' => 3,
                'lang' => 'en',
                'title' => 'International Logistics',
                'description' => 'Our global services span across 232 locations, enabling your business to reach new heights and expand your products worldwide.',
                'created_at' => '2024-06-24 13:42:26',
                'updated_at' => '2024-06-24 13:42:26'
            ],
            [
                'website_service_id' => 4,
                'lang' => 'en',
                'title' => 'Warehousing & Fulfillment',
                'description' => 'We provide a digital solution for managing inventory that includes pick-up, packing, and dispatch.',
                'created_at' => '2024-06-24 13:42:54',
                'updated_at' => '2024-06-24 13:42:54'
            ],
            [
                'website_service_id' => 5,
                'lang' => 'en',
                'title' => 'MOVIT/Moving & Packing',
                'description' => 'Our team of experts has the necessary knowledge to ensure that all your belongings are packed and transported safely to your new home or office.',
                'created_at' => '2024-06-24 13:43:29',
                'updated_at' => '2024-06-24 13:43:29'
            ],
            [
                'website_service_id' => 6,
                'lang' => 'en',
                'title' => 'E-Commerce Logistics',
                'description' => 'Managing eCommerce logistics in-house involves overseeing the supply chain, tracking shipments from the supplier to the warehouse',
                'created_at' => '2024-06-24 13:43:53',
                'updated_at' => '2024-06-24 13:43:53'
            ],
            [
                'website_service_id' => 6,
                'lang' => 'bn',
                'title' => 'ই-কমার্স লজিস্টিক্স',
                'description' => 'ই-কমার্স লজিস্টিক্স নিজেরাই পরিচালনা করা , সাপ্লাই চেইন তত্ত্বাবধান করা, সরবরাহকারী থেকে ওয়ারহাউজ পর্যন্ত চালান ট্র্যাক করা',
                'created_at' => '2024-06-25 08:58:03',
                'updated_at' => '2024-06-25 08:58:03'
            ],
            [
                'website_service_id' => 5,
                'lang' => 'bn',
                'title' => 'পরিবহন ও প্যাকিং',
                'description' => 'আমাদের বিশেষজ্ঞ দলের প্রয়োজনীয় জ্ঞান রয়েছে যাতে আপনার সমস্ত জিনিসপত্র নিরাপদে আপনার নতুন বাড়ি বা অফিসে প্যাক এবং পরিবহন করা হয়।',
                'created_at' => '2024-06-25 09:03:02',
                'updated_at' => '2024-06-25 09:03:02'
            ],
            [
                'website_service_id' => 4,
                'lang' => 'bn',
                'title' => 'ওয়ারহাউজ সংরক্ষণ',
                'description' => 'আমরা সংগ্রহ, প্যাকিং, এবং প্রেরণসহ ইনভেন্টরি ব্যবস্থাপনার জন্য একটি ডিজিটাল সমাধান প্রদান করি।',
                'created_at' => '2024-06-25 10:26:13',
                'updated_at' => '2024-06-25 10:26:13'
            ],
            [
                'website_service_id' => 3,
                'lang' => 'bn',
                'title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'description' => 'আমাদের বিশ্বব্যাপী সেবাগুলি ২৩২ স্থানে ছড়িয়ে পড়ে, যা আপনার ব্যবসাকে নতুন উচ্চারণে পৌঁছানো এবং আপনার পণ্যগুলি বিশ্বব্যাপী প্রসার করতে সহায়ক।',
                'created_at' => '2024-06-25 10:26:41',
                'updated_at' => '2024-06-25 10:26:41'
            ],
            [
                'website_service_id' => 2,
                'lang' => 'bn',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'created_at' => '2024-06-25 10:27:20',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            [
                'website_service_id' => 1,
                'lang' => 'bn',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'created_at' => '2024-06-25 10:27:35',
                'updated_at' => '2024-06-25 10:27:35'
            ]
        ];

        foreach ($serviceLanguages as $language) {
            DB::table('website_service_languages')->updateOrInsert(
                [
                    'website_service_id' => $language['website_service_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $testimonials = [
            [
                'name' => 'শর্মিলা ইসলাম',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'media_id' => NULL,
                'image' => '{"storage":"local","original_image":"images\/20240624142304_original_testimonial_image420.png","image_96X96":"images\/20240624142304image_96X96testimonial_image34.png","image_80x80":"images\/20240624142304image_80x80testimonial_image50.png"}',
                'rating' => '5',
                'status' => 1,
                'created_at' => '2024-06-24 14:23:05',
                'updated_at' => '2024-06-25 11:04:26'
            ],
            [
                'name' => 'হাসান মাহমুদ',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'media_id' => NULL,
                'image' => '{"storage":"local","original_image":"images\/20240624142415_original_testimonial_image174.png","image_96X96":"images\/20240624142415image_96X96testimonial_image38.png","image_80x80":"images\/20240624142415image_80x80testimonial_image12.png"}',
                'rating' => '5',
                'status' => 1,
                'created_at' => '2024-06-24 14:24:15',
                'updated_at' => '2024-06-25 11:00:51'
            ]
        ];

        foreach ($testimonials as $testimonial) {
            DB::table('website_testimonials')->updateOrInsert(
                [
                    'name' => $testimonial['name'],
                    'title' => $testimonial['title']
                ],
                $testimonial
            );
        }

        $aboutLanguages = [
            [
                'website_about_id' => 1,
                'lang' => 'en',
                'title' => 'Global Service',
                'description' => 'Tellus in hac habitasse platea dictumst vestibulum. Facilisi cras fermentum odio eu feugiat.',
                'created_at' => '2024-06-24 13:40:09',
                'updated_at' => '2024-06-24 14:09:20'
            ],
            [
                'website_about_id' => 2,
                'lang' => 'en',
                'title' => 'Domestic Service',
                'description' => 'Local logistics services" is a clear phrase, but we can make it more concise',
                'created_at' => '2024-06-24 13:40:34',
                'updated_at' => '2024-06-24 14:09:13'
            ],
            [
                'website_about_id' => 3,
                'lang' => 'en',
                'title' => 'Local Service',
                'description' => 'Local logistics services" is a clear phrase, but we can make it more concise.',
                'created_at' => '2024-06-24 13:40:54',
                'updated_at' => '2024-06-24 14:09:03'
            ],
            [
                'website_about_id' => 3,
                'lang' => 'bn',
                'title' => 'স্থানীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য স্থানীয় লজিস্টিক্স সেবা',
                'created_at' => '2024-06-25 08:33:12',
                'updated_at' => '2024-06-25 08:33:12'
            ],
            [
                'website_about_id' => 2,
                'lang' => 'bn',
                'title' => 'দেশীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য কানেক্টিভিটি সার্ভিস',
                'created_at' => '2024-06-25 08:34:10',
                'updated_at' => '2024-06-25 08:35:02'
            ],
            [
                'website_about_id' => 1,
                'lang' => 'bn',
                'title' => 'বৈশ্বিক সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য লজিস্টিক্স সেবা',
                'created_at' => '2024-06-25 08:35:46',
                'updated_at' => '2024-06-25 08:35:46'
            ]
        ];

        foreach ($aboutLanguages as $language) {
            DB::table('website_about_languages')->updateOrInsert(
                [
                    'website_about_id' => $language['website_about_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $testimonialLanguages = [
            [
                'testimonial_id' => 1,
                'lang' => 'en',
                'name' => 'Hilary Ouse',
                'title' => 'Fantastic service in and amazing support!',
                'designation' => 'E-commerce Entrepreneur',
                'description' => '“As a transportation coordinator, I appreciate the simplicity and power of SwiftShip\'s real-time features. Managing fleets, monitoring routes, and addressing issues proactively – all in one place. It\'s a game-changer for anyone in the logistics field.’’',
                'created_at' => '2024-06-24 14:23:05',
                'updated_at' => '2024-06-24 14:23:10'
            ],
            [
                'testimonial_id' => 2,
                'lang' => 'en',
                'name' => 'Hilary Ouse',
                'title' => 'Fantastic service in and amazing support!',
                'designation' => 'E-commerce Entrepreneur',
                'description' => '“As a transportation coordinator, I appreciate the simplicity and power of SwiftShip\'s real-time features. Managing fleets, monitoring routes, and addressing issues proactively – all in one place. It\'s a game-changer for anyone in the logistics field.’’',
                'created_at' => '2024-06-24 14:24:15',
                'updated_at' => '2024-06-24 14:24:15'
            ],
            [
                'testimonial_id' => 2,
                'lang' => 'bn',
                'name' => 'হাসান মাহমুদ',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'created_at' => '2024-06-25 11:00:51',
                'updated_at' => '2024-06-25 11:00:51'
            ],
            [
                'testimonial_id' => 1,
                'lang' => 'bn',
                'name' => 'শর্মিলা ইসলাম',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'created_at' => '2024-06-25 11:04:26',
                'updated_at' => '2024-10-09 15:18:24'
            ]
        ];

        foreach ($testimonialLanguages as $language) {
            DB::table('testimonial_languages')->updateOrInsert(
                [
                    'testimonial_id' => $language['testimonial_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $partnerLogos = [
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122510_original_partner_logo132.png","image_80X31":"images\/20241223122510image_80X31partner_logo388.png","image_80x80":"images\/20241223122510image_80x80partner_logo58.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:10',
                'updated_at' => '2024-12-23 12:25:10'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122516_original_partner_logo339.png","image_80X31":"images\/20241223122516image_80X31partner_logo455.png","image_80x80":"images\/20241223122516image_80x80partner_logo68.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:16',
                'updated_at' => '2024-12-23 12:25:16'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122522_original_partner_logo51.png","image_80X31":"images\/20241223122522image_80X31partner_logo37.png","image_80x80":"images\/20241223122522image_80x80partner_logo295.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:22',
                'updated_at' => '2024-12-23 12:25:22'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122524_original_partner_logo221.png","image_80X31":"images\/20241223122534image_80X31partner_logo428.png","image_80x80":"images\/20241223122534image_80x80partner_logo121.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:24',
                'updated_at' => '2024-12-23 12:25:34'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122542_original_partner_logo5.png","image_80X31":"images\/20241223122542image_80X31partner_logo285.png","image_80x80":"images\/20241223122542image_80x80partner_logo421.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:42',
                'updated_at' => '2024-12-23 12:25:42'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122550_original_partner_logo106.png","image_80X31":"images\/20241223122550image_80X31partner_logo174.png","image_80x80":"images\/20241223122550image_80x80partner_logo41.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:50',
                'updated_at' => '2024-12-23 12:25:50'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122600_original_partner_logo63.png","image_80X31":"images\/20241223122600image_80X31partner_logo273.png","image_80x80":"images\/20241223122600image_80x80partner_logo59.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:26:00',
                'updated_at' => '2024-12-23 12:26:00'
            ]
        ];

        foreach ($partnerLogos as $logo) {
            DB::table('website_partner_logos')->updateOrInsert(
                [
                    'image' => $logo['image']
                ],
                $logo
            );
        }

        $newsAndEventLanguages = [
            [
                'website_news_and_event_id' => 1,
                'lang' => 'en',
                'title' => 'Tech-Ops',
                'description' => 'Our operations are fully transparent and designed to be tech-friendly.',
                'created_at' => '2024-06-24 13:37:16',
                'updated_at' => '2024-06-24 13:37:16'
            ],
            [
                'website_news_and_event_id' => 2,
                'lang' => 'en',
                'title' => 'Verified Returns',
                'description' => 'We will confirm the status of each of your returns through Return Confirmation.',
                'created_at' => '2024-06-24 13:37:54',
                'updated_at' => '2024-06-24 13:37:54'
            ],
            [
                'website_news_and_event_id' => 3,
                'lang' => 'en',
                'title' => 'Live Tracking & Support',
                'description' => 'Keep track of your parcels in real-time as they move between cities, no matter where you are.',
                'created_at' => '2024-06-24 13:38:23',
                'updated_at' => '2024-06-24 13:38:23'
            ],
            [
                'website_news_and_event_id' => 4,
                'lang' => 'en',
                'title' => 'Daily Payment Processing',
                'description' => 'Receive your payments within 24 hours or less.',
                'created_at' => '2024-06-24 13:38:50',
                'updated_at' => '2024-06-24 13:38:50'
            ],
            [
                'website_news_and_event_id' => 4,
                'lang' => 'bn',
                'title' => 'ডেইলি পেমেন্ট প্রসেসিং',
                'description' => '২৪ ঘণ্টার মধ্যে বা তার কম সময়ে আপনার পেমেন্ট গ্রহণ করুন।',
                'created_at' => '2024-06-25 08:30:42',
                'updated_at' => '2024-06-25 08:30:42'
            ],
            [
                'website_news_and_event_id' => 3,
                'lang' => 'bn',
                'title' => 'লাইভ ট্র্যাকিং & সাপোর্ট',
                'description' => 'আপনার পার্সেলগুলো এক শহর থেকে অন্য শহরে চলার সময় রিয়েল-টাইমে ট্র্যাক করুন, আপনি যেখানেই থাকুন না কেন।',
                'created_at' => '2024-06-25 08:31:16',
                'updated_at' => '2024-06-25 08:31:16'
            ],
            [
                'website_news_and_event_id' => 2,
                'lang' => 'bn',
                'title' => 'অনুমোদিত রিটার্ন পলিসি',
                'description' => 'আমরা প্রত্যাবর্তন নিশ্চিতকরণের মাধ্যমে আপনার প্রত্যাবর্তনের প্রতিটি অবস্থান নিশ্চিত করব',
                'created_at' => '2024-06-25 08:31:35',
                'updated_at' => '2024-06-25 08:31:35'
            ],
            [
                'website_news_and_event_id' => 1,
                'lang' => 'bn',
                'title' => 'টেক অপারেশন',
                'description' => 'আমাদের কার্যক্রম সম্পূর্ণ স্বচ্ছ এবং প্রযুক্তি-বান্ধবভাবে ডিজাইন করা হয়েছে।',
                'created_at' => '2024-06-25 08:31:57',
                'updated_at' => '2024-06-25 08:31:57'
            ]
        ];

        foreach ($newsAndEventLanguages as $language) {
            DB::table('website_news_and_event_languages')->updateOrInsert(
                [
                    'website_news_and_event_id' => $language['website_news_and_event_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $newsAndEvents = [
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133716_original_news_event_image317.jpg","image_280X190":"images\/20240624133716image_280X190news_event_image233.jpg","image_80x80":"images\/20240624133716image_80x80news_event_image41.jpg"}',
                'title' => 'টেক অপারেশন',
                'description' => 'আমাদের কার্যক্রম সম্পূর্ণ স্বচ্ছ এবং প্রযুক্তি-বান্ধবভাবে ডিজাইন করা হয়েছে।',
                'status' => 1,
                'created_at' => '2024-06-24 13:37:16',
                'updated_at' => '2024-06-25 08:31:57'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133754_original_news_event_image363.jpg","image_280X190":"images\/20240624133754image_280X190news_event_image483.jpg","image_80x80":"images\/20240624133754image_80x80news_event_image470.jpg"}',
                'title' => 'অনুমোদিত রিটার্ন পলিসি',
                'description' => 'আমরা প্রত্যাবর্তন নিশ্চিতকরণের মাধ্যমে আপনার প্রত্যাবর্তনের প্রতিটি অবস্থান নিশ্চিত করব',
                'status' => 1,
                'created_at' => '2024-06-24 13:37:54',
                'updated_at' => '2024-06-25 08:31:35'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133823_original_news_event_image408.jpg","image_280X190":"images\/20240624133823image_280X190news_event_image291.jpg","image_80x80":"images\/20240624133823image_80x80news_event_image210.jpg"}',
                'title' => 'লাইভ ট্র্যাকিং & সাপোর্ট',
                'description' => 'আপনার পার্সেলগুলো এক শহর থেকে অন্য শহরে চলার সময় রিয়েল-টাইমে ট্র্যাক করুন, আপনি যেখানেই থাকুন না কেন।',
                'status' => 1,
                'created_at' => '2024-06-24 13:38:23',
                'updated_at' => '2024-06-25 08:31:16'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133850_original_news_event_image48.jpg","image_280X190":"images\/20240624133850image_280X190news_event_image299.jpg","image_80x80":"images\/20240624133850image_80x80news_event_image7.jpg"}',
                'title' => 'ডেইলি পেমেন্ট প্রসেসিং',
                'description' => '২৪ ঘণ্টার মধ্যে বা তার কম সময়ে আপনার পেমেন্ট গ্রহণ করুন।',
                'status' => 1,
                'created_at' => '2024-06-24 13:38:50',
                'updated_at' => '2024-06-25 08:30:42'
            ]
        ];

        foreach ($newsAndEvents as $event) {
            DB::table('website_news_and_events')->updateOrInsert(
                [
                    'title' => $event['title']
                ],
                $event
            );
        }

        $abouts = [
            [
                'icon' => '{"storage":"local","original_image":"images\/20240624140920_original_about_icon207.png","image_44X44":"images\/20240624140920image_44X44about_icon14.png","image_80x80":"images\/20240624140920image_80x80about_icon13.png"}',
                'title' => 'বৈশ্বিক সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য লজিস্টিক্স সেবা',
                'status' => 1,
                'created_at' => '2024-06-24 13:40:09',
                'updated_at' => '2024-06-25 08:35:46'
            ],
            [
                'icon' => '{"storage":"local","original_image":"images\/20240624140913_original_about_icon66.png","image_44X44":"images\/20240624140913image_44X44about_icon327.png","image_80x80":"images\/20240624140913image_80x80about_icon254.png"}',
                'title' => 'দেশীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য কানেক্টিভিটি সার্ভিস',
                'status' => 1,
                'created_at' => '2024-06-24 13:40:34',
                'updated_at' => '2024-06-25 08:35:02'
            ],
            [
                'icon' => '{"storage":"local","original_image":"images\/20240624140903_original_about_icon411.png","image_44X44":"images\/20240624140903image_44X44about_icon418.png","image_80x80":"images\/20240624140903image_80x80about_icon47.png"}',
                'title' => 'স্থানীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য স্থানীয় লজিস্টিক্স সেবা',
                'status' => 1,
                'created_at' => '2024-06-24 13:40:54',
                'updated_at' => '2024-06-25 08:33:12'
            ]
        ];

        foreach ($abouts as $about) {
            DB::table('website_abouts')->updateOrInsert(
                [
                    'title' => $about['title']
                ],
                $about
            );
        }

        $services = [
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134138_original_service_image120.jpg","image_358X270":"images\/20240624134138image_358X270service_image492.jpg","image_80x80":"images\/20240624134138image_80x80service_image119.jpg"}',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'status' => 1,
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-25 10:27:35'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134204_original_service_image369.jpg","image_358X270":"images\/20240624134204image_358X270service_image232.jpg","image_80x80":"images\/20240624134204image_80x80service_image61.jpg"}',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134226_original_service_image399.jpg","image_358X270":"images\/20240624134226image_358X270service_image208.jpg","image_80x80":"images\/20240624134226image_80x80service_image58.jpg"}',
                'title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'description' => 'আমাদের বিশ্বব্যাপী সেবাগুলি ২৩২ স্থানে ছড়িয়ে পড়ে, যা আপনার ব্যবসাকে নতুন উচ্চারণে পৌঁছানো এবং আপনার পণ্যগুলি বিশ্বব্যাপী প্রসার করতে সহায়ক।',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:26',
                'updated_at' => '2024-06-25 10:26:41'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134254_original_service_image388.jpg","image_358X270":"images\/20240624134254image_358X270service_image372.jpg","image_80x80":"images\/20240624134254image_80x80service_image33.jpg"}',
                'title' => 'ওয়ারহাউজ সংরক্ষণ',
                'description' => 'আমরা সংগ্রহ, প্যাকিং, এবং প্রেরণসহ ইনভেন্টরি ব্যবস্থাপনার জন্য একটি ডিজিটাল সমাধান প্রদান করি।',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:54',
                'updated_at' => '2024-06-25 10:26:13'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134329_original_service_image358.jpg","image_358X270":"images\/20240624134329image_358X270service_image31.jpg","image_80x80":"images\/20240624134329image_80x80service_image287.jpg"}',
                'title' => 'পরিবহন ও প্যাকিং',
                'description' => 'আমাদের বিশেষজ্ঞ দলের প্রয়োজনীয় জ্ঞান রয়েছে যাতে আপনার সমস্ত জিনিসপত্র নিরাপদে আপনার নতুন বাড়ি বা অফিসে প্যাক এবং পরিবহন করা হয়।',
                'status' => 1,
                'created_at' => '2024-06-24 13:43:29',
                'updated_at' => '2024-06-25 09:03:02'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134353_original_service_image449.jpg","image_358X270":"images\/20240624134353image_358X270service_image107.jpg","image_80x80":"images\/20240624134353image_80x80service_image347.jpg"}',
                'title' => 'ই-কমার্স লজিস্টিক্স',
                'description' => 'ই-কমার্স লজিস্টিক্স নিজেরাই পরিচালনা করা , সাপ্লাই চেইন তত্ত্বাবধান করা, সরবরাহকারী থেকে ওয়ারহাউজ পর্যন্ত চালান ট্র্যাক করা',
                'status' => 1,
                'created_at' => '2024-06-24 13:43:53',
                'updated_at' => '2024-06-25 08:58:03'
            ]
        ];

        foreach ($services as $service) {
            DB::table('website_services')->updateOrInsert(
                [
                    'title' => $service['title']
                ],
                $service
            );
        }

        // First, get the service IDs that were inserted
        $services = DB::table('website_services')->get()->keyBy('title');

        $serviceLanguages = [
            [
                'service_title' => 'ডকুমেন্ট ডেলিভারি',
                'lang' => 'en',
                'title' => 'Document Deliveries',
                'description' => 'Trax offers a secure and digitized process to send highly sensitive shipments like SIMs, debit and credit cards.',
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-24 13:41:39'
            ],
            [
                'service_title' => 'বাল্ক মুভমেন্ট',
                'lang' => 'en',
                'title' => 'Bulk Movements',
                'description' => 'We can help you with moving heavy shipments across USA, which will take the weight off your shoulders.',
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-24 13:42:04'
            ],
            [
                'service_title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'lang' => 'en',
                'title' => 'International Logistics',
                'description' => 'Our global services span across 232 locations, enabling your business to reach new heights and expand your products worldwide.',
                'created_at' => '2024-06-24 13:42:26',
                'updated_at' => '2024-06-24 13:42:26'
            ],
            [
                'service_title' => 'ওয়ারহাউজ সংরক্ষণ',
                'lang' => 'en',
                'title' => 'Warehousing & Fulfillment',
                'description' => 'We provide a digital solution for managing inventory that includes pick-up, packing, and dispatch.',
                'created_at' => '2024-06-24 13:42:54',
                'updated_at' => '2024-06-24 13:42:54'
            ],
            [
                'service_title' => 'পরিবহন ও প্যাকিং',
                'lang' => 'en',
                'title' => 'MOVIT/Moving & Packing',
                'description' => 'Our team of experts has the necessary knowledge to ensure that all your belongings are packed and transported safely to your new home or office.',
                'created_at' => '2024-06-24 13:43:29',
                'updated_at' => '2024-06-24 13:43:29'
            ],
            [
                'service_title' => 'ই-কমার্স লজিস্টিক্স',
                'lang' => 'en',
                'title' => 'E-Commerce Logistics',
                'description' => 'Managing eCommerce logistics in-house involves overseeing the supply chain, tracking shipments from the supplier to the warehouse',
                'created_at' => '2024-06-24 13:43:53',
                'updated_at' => '2024-06-24 13:43:53'
            ],
            [
                'service_title' => 'ই-কমার্স লজিস্টিক্স',
                'lang' => 'bn',
                'title' => 'ই-কমার্স লজিস্টিক্স',
                'description' => 'ই-কমার্স লজিস্টিক্স নিজেরাই পরিচালনা করা , সাপ্লাই চেইন তত্ত্বাবধান করা, সরবরাহকারী থেকে ওয়ারহাউজ পর্যন্ত চালান ট্র্যাক করা',
                'created_at' => '2024-06-25 08:58:03',
                'updated_at' => '2024-06-25 08:58:03'
            ],
            [
                'service_title' => 'পরিবহন ও প্যাকিং',
                'lang' => 'bn',
                'title' => 'পরিবহন ও প্যাকিং',
                'description' => 'আমাদের বিশেষজ্ঞ দলের প্রয়োজনীয় জ্ঞান রয়েছে যাতে আপনার সমস্ত জিনিসপত্র নিরাপদে আপনার নতুন বাড়ি বা অফিসে প্যাক এবং পরিবহন করা হয়।',
                'created_at' => '2024-06-25 09:03:02',
                'updated_at' => '2024-06-25 09:03:02'
            ],
            [
                'service_title' => 'ওয়ারহাউজ সংরক্ষণ',
                'lang' => 'bn',
                'title' => 'ওয়ারহাউজ সংরক্ষণ',
                'description' => 'আমরা সংগ্রহ, প্যাকিং, এবং প্রেরণসহ ইনভেন্টরি ব্যবস্থাপনার জন্য একটি ডিজিটাল সমাধান প্রদান করি।',
                'created_at' => '2024-06-25 10:26:13',
                'updated_at' => '2024-06-25 10:26:13'
            ],
            [
                'service_title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'lang' => 'bn',
                'title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'description' => 'আমাদের বিশ্বব্যাপী সেবাগুলি ২৩২ স্থানে ছড়িয়ে পড়ে, যা আপনার ব্যবসাকে নতুন উচ্চারণে পৌঁছানো এবং আপনার পণ্যগুলি বিশ্বব্যাপী প্রসার করতে সহায়ক।',
                'created_at' => '2024-06-25 10:26:41',
                'updated_at' => '2024-06-25 10:26:41'
            ],
            [
                'service_title' => 'বাল্ক মুভমেন্ট',
                'lang' => 'bn',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'created_at' => '2024-06-25 10:27:20',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            [
                'service_title' => 'ডকুমেন্ট ডেলিভারি',
                'lang' => 'bn',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'created_at' => '2024-06-25 10:27:35',
                'updated_at' => '2024-06-25 10:27:35'
            ]
        ];

        foreach ($serviceLanguages as $language) {
            $service = $services[$language['service_title']] ?? null;

            if ($service) {
                DB::table('website_service_languages')->updateOrInsert(
                    [
                        'website_service_id' => $service->id,
                        'lang' => $language['lang']
                    ],
                    [
                        'website_service_id' => $service->id,
                        'lang' => $language['lang'],
                        'title' => $language['title'],
                        'description' => $language['description'],
                        'created_at' => $language['created_at'],
                        'updated_at' => $language['updated_at']
                    ]
                );
            }
        }
    }
}
