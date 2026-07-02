<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_filter_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seo_filter_id')->unsigned();
            $table->string('locale');
            $table->string('h1')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->longText('description')->nullable();

            $table->unique(['seo_filter_id', 'locale']);

            $table->foreign('seo_filter_id')
                ->references('id')
                ->on('seo_filters')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_filter_translations');
    }
};
