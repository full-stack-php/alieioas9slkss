<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table
                ->unsignedTinyInteger('stock_status')
                ->default(0)
                ->after('manage_stock')
                ->index();
        });

        DB::table('products')
            ->where('manage_stock', true)
            ->update([
                'stock_status' => 1,
            ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_status');
        });
    }
};
