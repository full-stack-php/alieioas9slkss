<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meest_warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ref')->unique();
            $table->integer('city_id')->unsigned();
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('meest_cities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meest_warehouses');
    }
};
