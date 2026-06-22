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
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->integer('old_id')->default(0)->after('position');
            $table->decimal('special_price', 18, 4)->unsigned()->nullable()->after('price_type');
            $table->string('special_price_type', 10)->after('special_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->dropColumn('old_id');
            $table->dropColumn('special_price');
            $table->dropColumn('special_price_type');
        });
    }
};
