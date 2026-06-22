<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meest_warehouse_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meest_warehouse_id')->unsigned();
            $table->string('locale');
            $table->string('name');

            $table->unique(['meest_warehouse_id', 'locale']);
            $table->foreign('meest_warehouse_id')->references('id')->on('meest_warehouses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meest_warehouse_translations');
    }
};
