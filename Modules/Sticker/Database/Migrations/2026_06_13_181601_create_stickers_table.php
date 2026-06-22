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
        Schema::create('stickers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('text_color')->nullable();
            $table->string('background_color')->nullable();
            $table->string('image_background_color')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active');;

            $table->softDeletes();
            $table->timestamps();

            $table->index('type');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stickers');
    }
};
