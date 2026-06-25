<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_gift_options', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('gift_id');
            $table->unsignedInteger('product_option_id');
            $table->unsignedInteger('product_option_value_id');

            $table->timestamps();

            $table->foreign('gift_id')
                ->references('id')
                ->on('product_gifts')
                ->onDelete('cascade');

            $table->foreign('product_option_id')
                ->references('id')
                ->on('product_options')
                ->onDelete('cascade');

            $table->foreign('product_option_value_id')
                ->references('id')
                ->on('product_option_values')
                ->onDelete('cascade');

            $table->unique(['gift_id', 'product_option_id'], 'gift_option_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_gift_options');
    }
};
