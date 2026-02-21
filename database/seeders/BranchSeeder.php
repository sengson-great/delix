<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\RoleUser;
use App\Enums\UserTypeEnum;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('en_BD'); // Set the locale to Bangladesh
        $roleSeeder = new RoleSeeder();
        $pass_has =  bcrypt(123456);

        $branches = [
            [
                'user_id'       => NULL,
                'name'          => 'Khilkhet - Master Hub',
                'address'       => 'Raj',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 1,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Mirpur Hub',
                'address'       => 'Mirpur-13',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Mugdha Hub',
                'address'       => 'Mugdha',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Dhanmondi Hub',
                'address'       => 'Dhanmondi',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Chattogram Hub',
                'address'       => 'Chaitagong',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Cox\'s Bazar',
                'address'       => 'Cox-Bazar',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Outsite City',
                'address'       => 'dhaka',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Uttara',
                'address'       => 'uttara',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => NULL,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Patuakhali Branch',
                'address'       => 'House#30,Sadar Road, Patuakhali Sadar,Patuakhali',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => 1,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       =>NULL,
                'name'          => 'Kalshi Dhaka',
                'address'       => 'Hiuse#30,Road#20,Kalshi,Mirpur',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => 1,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Barishal Sadar',
                'address'       => 'House#30,Sadar Road,barishal Sadar',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => 1,
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'user_id'       => NULL,
                'name'          => 'Kafrul Branch',
                'address'       => 'House#152,Road#30,School Road,Kafrul,Dhaka',
                'phone_number'  => generateBangladeshPhoneNumber(),
                'default'       => 0,
                'created_by'    => NULL,
                'updated_by'    => 1,
                'created_at'    => now(),
                'updated_at'    => now()
            ]


        ];

        DB::table('branches')->insert($branches);
            $branches = Branch::active()->get();
            foreach ($branches as $branch) {
                $email              = 'staff_' . $branch->id . '@spagreen.net';
                $user               = new User();
                $user->first_name   = $faker->firstName;
                $user->last_name    = $faker->lastName;
                $user->email        = $email;
                $user->password     = $pass_has;
                $user->permissions  = $roleSeeder->branchManagerPermissions();
                $user->image_id     = null;
                $user->user_type    = UserTypeEnum::STAFF;
                $user->branch_id    = $branch->id;
                $user->save();
                $branch->user_id    = $user->id;
                $branch->update();
                $role               = new RoleUser();
                $role->user_id      = $user->id;
                $role->role_id      = 2;
                $role->save();
                $activation         = Activation::create($user);
                Activation::complete($user, $activation->code);
         }

    }
}
