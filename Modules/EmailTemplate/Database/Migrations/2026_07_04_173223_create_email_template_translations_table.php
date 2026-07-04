<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_template_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_template_id')->unsigned();
            $table->string('locale');

            $table->string('name');
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();

            $table->unique(['email_template_id', 'locale']);

            $table->foreign('email_template_id')
                ->references('id')
                ->on('email_templates')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_template_translations');
    }
};
