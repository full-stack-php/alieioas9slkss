<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->increments('id');

            $table->string('type');
            $table->string('recipient')->default('customer');
            $table->string('status_key')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('show_product_image')->default(true);

            $table->unsignedInteger('product_image_max_width')->default(80);
            $table->unsignedInteger('product_image_max_height')->default(80);

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['type', 'recipient']);
            $table->index(['type', 'recipient', 'status_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
};
