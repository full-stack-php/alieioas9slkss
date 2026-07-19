<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'product_attributes',
            function (Blueprint $table) {
                $table
                    ->unsignedInteger('position')
                    ->default(0)
                    ->after('attribute_id');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'product_attributes',
            function (Blueprint $table) {
                $table->dropColumn('position');
            }
        );
    }
};
