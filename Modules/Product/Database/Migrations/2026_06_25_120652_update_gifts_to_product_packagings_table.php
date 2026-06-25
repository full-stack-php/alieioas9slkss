<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_packagings', function (Blueprint $table) {
            if (Schema::hasColumn('product_packagings', 'gift_id')) {
                $table->dropColumn('gift_id');
            }

            if (Schema::hasColumn('product_packagings', 'is_gift')) {
                $table->dropColumn('is_gift');
            }

            if (!Schema::hasColumn('product_packagings', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('special_price_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_packagings', function (Blueprint $table) {
            if (Schema::hasColumn('product_packagings', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (!Schema::hasColumn('product_packagings', 'gift_id')) {
                $table->integer('gift_id')->nullable()->after('special_price_type');
            }

            if (!Schema::hasColumn('product_packagings', 'is_gift')) {
                $table->boolean('is_gift')->default(false)->after('gift_id');
            }
        });
    }
};
