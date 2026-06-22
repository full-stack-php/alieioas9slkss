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
        Schema::create('np_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('ref')->unique();
            $table->integer('area_id')->unsigned();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('area_id')->references('id')->on('np_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('np_cities');
    }
};
