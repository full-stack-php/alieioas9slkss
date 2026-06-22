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
        Schema::create('product_stickers', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('sticker_id')->unsigned();
            $table->unsignedInteger('sort_order')->default(0);

            $table->primary(['product_id', 'sticker_id']);

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table
                ->foreign('sticker_id')
                ->references('id')
                ->on('stickers')
                ->onDelete('cascade');

            $table->index(['product_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stickers');
    }
};
