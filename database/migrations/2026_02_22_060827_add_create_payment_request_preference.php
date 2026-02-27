<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $exists = DB::table('preferences')->where('key', 'create_payment_request')->exists();
        
        if (!$exists) {
            DB::table('preferences')->insert([
                'key' => 'create_payment_request',
                'title' => 'Create Payment Request',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'withdraw',
                'description' => 'Allow staff to create payment requests',
                'sort_order' => 1,
                'is_public' => 0,
                'is_system' => 0,
                'is_editable' => 1,
                'data_type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down()
    {
        DB::table('preferences')->where('key', 'create_payment_request')->delete();
    }
};