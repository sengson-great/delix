<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\RoleUser;
use App\Enums\UserTypeEnum;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
public function run()
{
    // Find or create roles
    $adminRole = DB::table('roles')->where('name', 'Admin')->first();
    if (!$adminRole) {
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $adminRole = (object)['id' => $adminRoleId];
    }

    $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
    if (!$superAdminRole) {
        $superAdminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Super Admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $superAdminRole = (object)['id' => $superAdminRoleId];
    }

    // Create admin user if not exists
    $admin = User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'first_name' => 'Admin',
            'last_name' => '',
            'dashboard' => 'admin',
            'password' => bcrypt(123456),
            'user_type' => 'staff',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );

    // Create super admin user if not exists
    $superAdmin = User::firstOrCreate(
        ['email' => 'superadmin@admin.com'],
        [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'dashboard' => 'admin',
            'password' => bcrypt(123456),
            'user_type' => 'staff',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );

    // Create role_user table if it doesn't exist
    if (!Schema::hasTable('role_user')) {
        Schema::create('role_user', function ($table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('role_id');
            $table->timestamps();
        });
    }

    // Attach roles to users
    DB::table('role_user')->updateOrInsert(
        ['user_id' => $admin->id, 'role_id' => $adminRole->id],
        ['created_at' => now(), 'updated_at' => now()]
    );

    DB::table('role_user')->updateOrInsert(
        ['user_id' => $superAdmin->id, 'role_id' => $superAdminRole->id],
        ['created_at' => now(), 'updated_at' => now()]
    );

    // Create activations
    if (Schema::hasTable('activations')) {
        foreach ([$admin, $superAdmin] as $user) {
            $exists = DB::table('activations')->where('user_id', $user->id)->exists();
            if (!$exists) {
                DB::table('activations')->insert([
                    'user_id' => $user->id,
                    'code' => uniqid(),
                    'completed' => 1,
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    $this->command->info('Users seeded successfully!');
}
}
