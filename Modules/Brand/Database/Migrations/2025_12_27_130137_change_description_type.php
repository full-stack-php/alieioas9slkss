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
        Schema::table('brand_translations', function (Blueprint $table) {
            // Меняем string (255 символов) на longtext (4 ГБ данных)
            $table->longText('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_translations', function (Blueprint $table) {
            // Откатываем обратно на string
            $table->string('description')->change();
        });
    }
};
