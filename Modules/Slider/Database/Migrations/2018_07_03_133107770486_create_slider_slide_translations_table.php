<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slider_slide_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('slider_slide_id')->unsigned();
            $table->string('locale');
            $table->integer('file_id')->nullable()->unsigned();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->string('price_from')->nullable();
            $table->string('price_text')->nullable();


            $table->unique(['slider_slide_id', 'locale']);
            $table->foreign('slider_slide_id')->references('id')->on('slider_slides')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slider_slide_translations');
    }
};
