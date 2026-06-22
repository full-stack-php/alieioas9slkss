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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->decimal('price', 18, 4)->unsigned()->nullable();
            $table->decimal('special_price', 18, 4)->unsigned()->nullable();
            $table->string('special_price_type')->nullable();
            $table->date('special_price_start')->nullable();
            $table->date('special_price_end')->nullable();
            $table->string('sku')->nullable();
            $table->boolean('manage_stock')->default(0);
            $table->integer('qty')->nullable();
            $table->boolean('in_stock')->default(1);
            $table->integer('viewed')->unsigned()->default(0);
            $table->boolean('is_active');
            $table->datetime('new_from')->nullable();
            $table->datetime('new_to')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
