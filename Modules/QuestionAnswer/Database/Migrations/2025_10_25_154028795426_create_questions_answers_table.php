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
        Schema::create('questions_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asker_id')->unsigned()->index()->nullable();
            $table->integer('product_id')->unsigned()->index();
            $table->string('asker_name');
            $table->string('asker_phone');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->boolean('is_approved');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions_answers');
    }
};
