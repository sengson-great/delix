<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add image_id column (foreign key to media table)
            $table->unsignedBigInteger('image_id')->nullable()->after('user_type');
            // Add is_primary column
            //$table->boolean('is_primary')->default(0)->after('image_id');
            
            // If there's a media table, add foreign key constraint
            // $table->foreign('image_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['image_id', 'is_primary']);
        });
    }
}