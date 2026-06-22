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
        Schema::create('meest_city_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meest_city_id')->unsigned();
            $table->string('locale');
            $table->string('name');

            $table->unique(['meest_city_id', 'locale']);
            $table->foreign('meest_city_id')->references('id')->on('meest_cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meest_city_translations');
    }
};
