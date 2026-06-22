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
        Schema::create('product_gifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('gift_product_id');
            $table->decimal('price', 18, 4)->default(0);
            $table->integer('min_qty')->default(1);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('gift_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_gifts');
    }
};
