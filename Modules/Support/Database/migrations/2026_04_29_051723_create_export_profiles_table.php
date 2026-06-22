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
        Schema::create('export_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('entity');
            $table->enum('format', ['csv', 'xlsx', 'json', 'xml']);
            $table->json('settings')->nullable();
            $table->json('columns')->nullable();
            $table->json('filters')->nullable();
            $table->json('sortings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('cron_schedule')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_profiles');
    }
};
