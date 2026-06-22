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
        Schema::table('contact_submissions', function (Blueprint $table) {
            Schema::table('contact_submissions', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('user_agent');
                $table->timestamp('processed_at')->nullable()->after('read_at');
                $table->unsignedBigInteger('processed_by')->nullable()->after('processed_at');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'read_at',
                'processed_at',
                'processed_by',
            ]);
        });
    }
};
