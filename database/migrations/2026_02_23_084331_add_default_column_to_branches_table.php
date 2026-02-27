<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultColumnToBranchesTable extends Migration
{
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'default')) {
                $table->boolean('default')->default(false)->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'default')) {
                $table->dropColumn('default');
            }
        });
    }
}