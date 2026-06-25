<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_gifts', function (Blueprint $table) {
            if (!Schema::hasColumn('product_gifts', 'gift_qty')) {
                $table->unsignedInteger('gift_qty')
                    ->default(1)
                    ->after('min_qty');
            }

            if (!Schema::hasColumn('product_gifts', 'is_repeatable')) {
                $table->boolean('is_repeatable')
                    ->default(false)
                    ->after('gift_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_gifts', function (Blueprint $table) {
            if (Schema::hasColumn('product_gifts', 'is_repeatable')) {
                $table->dropColumn('is_repeatable');
            }

            if (Schema::hasColumn('product_gifts', 'gift_qty')) {
                $table->dropColumn('gift_qty');
            }
        });
    }
};
