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
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 30)->index();

            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('subject')->nullable();
            $table->text('message')->nullable();

            $table->string('topic')->nullable();
            $table->timestamp('preferred_call_at')->nullable();

            $table->text('source_url')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};
