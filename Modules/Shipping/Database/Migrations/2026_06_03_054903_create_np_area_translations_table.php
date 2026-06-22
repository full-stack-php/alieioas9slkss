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
        Schema::create('np_area_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('np_area_id')->unsigned();
            $table->string('locale');
            $table->string('name');

            $table->unique(['np_area_id', 'locale']);
            $table->foreign('np_area_id')->references('id')->on('np_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('np_area_translations');
    }
};
