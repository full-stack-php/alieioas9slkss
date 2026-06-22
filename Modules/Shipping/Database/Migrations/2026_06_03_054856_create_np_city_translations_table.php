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
        Schema::create('np_city_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('np_city_id')->unsigned();
            $table->string('locale');
            $table->string('name'); // Название города
            $table->string('type')->nullable(); // Тип (місто, смт, село)

            $table->unique(['np_city_id', 'locale']);
            $table->foreign('np_city_id')->references('id')->on('np_cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('np_city_translations');
    }
};
