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
        if (!Schema::hasTable('charges')) {
            Schema::create('charges', function (Blueprint $table) {
                $table->id();
                $table->string('charge_title')->nullable();
                $table->string('charge_type')->nullable();
                $table->decimal('charge_amount', 10, 2)->default(0);
                $table->string('category')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('charges');
    }
};
