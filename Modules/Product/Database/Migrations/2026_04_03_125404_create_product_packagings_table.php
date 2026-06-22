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
        Schema::create('product_packagings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->decimal('price', 18, 4)->unsigned()->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('special_price', 18, 4)->unsigned()->nullable();
            $table->string('special_price_type')->nullable();
            $table->integer('gift_id')->nullable();
            $table->boolean('is_gift')->default(0);


            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packagings');
    }
};
