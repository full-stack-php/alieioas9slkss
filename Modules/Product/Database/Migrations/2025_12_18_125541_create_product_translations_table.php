<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('product_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->string('h1_name');
            $table->longText('description');

            $table->unique(['product_id', 'locale']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE product_translations ADD FULLTEXT(name)');
        DB::statement('ALTER TABLE product_translations ADD FULLTEXT(h1_name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
