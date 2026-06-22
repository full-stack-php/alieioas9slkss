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
        Schema::table('default_addresses', function (Blueprint $table) {
            $table->tinyInteger('np_address_type')
                ->nullable()
                ->after('address_id');

            $table->unique(
                ['customer_id', 'np_address_type'],
                'default_addresses_customer_np_type_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('default_addresses', function (Blueprint $table) {
            $table->index('customer_id', 'default_addresses_customer_id_index');
        });

        Schema::table('default_addresses', function (Blueprint $table) {
            $table->dropUnique('default_addresses_customer_np_type_unique');
            $table->dropColumn('np_address_type');
        });
    }
};
