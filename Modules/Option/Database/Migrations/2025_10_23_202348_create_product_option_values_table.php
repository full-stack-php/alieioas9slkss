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
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('product_option_id')->unsigned()->index();
            $table->integer('option_id')->unsigned()->index();
            $table->integer('option_value_id')->unsigned()->index();
            $table->decimal('price', 18, 4)->unsigned()->nullable();
            $table->string('price_type', 10);
            $table->integer('position')->unsigned();

            $table->foreign('product_option_id')
                ->references('id')
                ->on('product_options')
                ->onDelete('cascade');

            $table->foreign('option_value_id')->references('id')->on('option_values')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
