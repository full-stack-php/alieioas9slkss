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
        Schema::create('product_packaging_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_packaging_id')->unsigned();
            $table->string('locale');
            $table->string('name');

            $table->foreign('product_packaging_id')->references('id')->on('product_packagings')->onDelete('cascade');

            $table->unique(['product_packaging_id', 'locale'], 'packaging_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packaging_translations');
    }
};
