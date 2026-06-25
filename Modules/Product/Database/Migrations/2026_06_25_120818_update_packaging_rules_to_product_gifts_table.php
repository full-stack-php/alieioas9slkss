<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('product_gifts', 'product_id')) {
            Schema::table('product_gifts', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
            });

            Schema::table('product_gifts', function (Blueprint $table) {
                $table->renameColumn('product_id', 'parent_product_id');
            });
        }

        Schema::table('product_gifts', function (Blueprint $table) {
            if (!Schema::hasColumn('product_gifts', 'parent_packaging_id')) {
                $table->unsignedInteger('parent_packaging_id')->nullable()->after('parent_product_id');
            }

            if (!Schema::hasColumn('product_gifts', 'gift_packaging_id')) {
                $table->unsignedInteger('gift_packaging_id')->nullable()->after('gift_product_id');
            }

            if (!Schema::hasColumn('product_gifts', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('min_qty');
            }

            $table->foreign('parent_product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('parent_packaging_id')
                ->references('id')
                ->on('product_packagings')
                ->onDelete('set null');

            $table->foreign('gift_packaging_id')
                ->references('id')
                ->on('product_packagings')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('product_gifts', function (Blueprint $table) {
            $table->dropForeign(['parent_product_id']);
            $table->dropForeign(['parent_packaging_id']);
            $table->dropForeign(['gift_packaging_id']);

            if (Schema::hasColumn('product_gifts', 'parent_packaging_id')) {
                $table->dropColumn('parent_packaging_id');
            }

            if (Schema::hasColumn('product_gifts', 'gift_packaging_id')) {
                $table->dropColumn('gift_packaging_id');
            }

            if (Schema::hasColumn('product_gifts', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        if (Schema::hasColumn('product_gifts', 'parent_product_id')) {
            Schema::table('product_gifts', function (Blueprint $table) {
                $table->renameColumn('parent_product_id', 'product_id');
            });
        }

        Schema::table('product_gifts', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }
};
