<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_one_c_mappings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned();
            $table->integer('product_packaging_id')->unsigned()->nullable();

            // Формат: { product_option_id: product_option_value_id }
            // Например: {"12": 45, "13": 51}
            $table->json('product_options')->nullable();

            // ID, который задаём вручную в админке
            $table->string('external_id');

            // Итоговый 1С ID: product.1c_id + "#" + external_id
            $table->string('one_c_id')->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('product_packaging_id')
                ->references('id')
                ->on('product_packagings')
                ->onDelete('set null');

            $table->index('product_id');
            $table->index('product_packaging_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_one_c_mappings');
    }
};
