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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('customer_group_discount', 18, 4)
                ->unsigned()
                ->default(0)
                ->after('discount');

            $table->decimal('customer_group_discount_percent', 8, 2)
                ->unsigned()
                ->default(0)
                ->after('customer_group_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_group_discount',
                'customer_group_discount_percent',
            ]);
        });
    }
};
