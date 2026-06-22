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
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('bundle_product_id')->unsigned();

            $table->integer('product_qty')->default(1);
            $table->decimal('product_price', 18, 4)->unsigned()->nullable();
            $table->decimal('special_price', 18, 4)->unsigned()->nullable();
            $table->string('special_price_type')->nullable();

            $table->integer('bundle_qty')->default(1);
            $table->decimal('bundle_price', 18, 4)->unsigned()->nullable();
            $table->decimal('special_bundle_price', 18, 4)->unsigned()->nullable();
            $table->string('special_bundle_price_type')->nullable();


            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('bundle_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_bundles');
    }
};
