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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->nullable()->after('payment_status');
            $table->string('currency')->nullable()->after('amount');
            $table->json('payload')->nullable()->after('currency');
            $table->timestamp('paid_at')->nullable()->after('payload');

            $table->index('payment_status');
            $table->index(['payment_method', 'transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['payment_method', 'transaction_id']);

            $table->dropColumn([
                'amount',
                'currency',
                'payload',
                'paid_at',
            ]);
        });
    }
};
