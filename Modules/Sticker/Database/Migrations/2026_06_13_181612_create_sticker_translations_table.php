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
        Schema::create('sticker_translations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('sticker_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->string('image_alt')->nullable();
            $table->text('description')->nullable();
            $table->text('popup_description')->nullable();
            $table->unique(['sticker_id', 'locale']);

            $table
                ->foreign('sticker_id')
                ->references('id')
                ->on('stickers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sticker_translations');
    }
};
