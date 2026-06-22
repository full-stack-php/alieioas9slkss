<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('np_warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('ref')->unique();
            $table->integer('city_id')->unsigned();
            $table->integer('number')->nullable();
            $table->boolean('is_postomat')->default(false);
            $table->integer('max_weight')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('np_warehouses');
    }
};
