<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->text('query_string');
            $table->string('path')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_filters');
    }
};
