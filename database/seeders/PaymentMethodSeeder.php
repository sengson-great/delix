<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Enums\PaymentMethodType;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            // Cambodian Mobile Banking / E-Wallets
            [
                'name' => 'ABA Bank',
                'type' => PaymentMethodType::MOBILE_BANKING,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Wing',
                'type' => PaymentMethodType::MOBILE_BANKING,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ACLEDA Bank',
                'type' => PaymentMethodType::MOBILE_BANKING,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pi Pay',
                'type' => PaymentMethodType::MOBILE_BANKING,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'TrueMoney',
                'type' => PaymentMethodType::MOBILE_BANKING,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'eMoney',
                'type' => PaymentMethodType::MOBILE_BANKING,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Traditional Banks
            [
                'name' => 'Bank',
                'type' => PaymentMethodType::BANK,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sathapana Bank',
                'type' => PaymentMethodType::BANK,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Prince Bank',
                'type' => PaymentMethodType::BANK,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Cash
            [
                'name' => 'Cash',
                'type' => PaymentMethodType::CASH,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Offline',
                'type' => PaymentMethodType::CASH,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        PaymentMethod::insert($methods);
    }
}