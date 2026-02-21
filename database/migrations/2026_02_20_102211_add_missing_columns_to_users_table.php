<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'created_by')) {
            $table->integer('created_by')->nullable()->after('id');
        }
        if (!Schema::hasColumn('users', 'updated_by')) {
            $table->integer('updated_by')->nullable();
        }
        if (!Schema::hasColumn('users', 'last_login')) {
            $table->timestamp('last_login')->nullable();
        }
        if (!Schema::hasColumn('users', 'remember_token')) {
            $table->string('remember_token', 100)->nullable();
        }
        if (!Schema::hasColumn('users', 'deleted_at')) {
            $table->softDeletes();
        }
        if (!Schema::hasColumn('users', 'email_verified_at')) {
            $table->timestamp('email_verified_at')->nullable();
        }
        if (!Schema::hasColumn('users', 'status')) {
            $table->boolean('status')->default(1);
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'created_by', 'updated_by', 'last_login', 
            'remember_token', 'deleted_at', 'email_verified_at', 'status'
        ]);
    });
}
};
