<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('redirects', function (Blueprint $table) {
            if (!Schema::hasColumn('redirects', 'comment')) {
                $table->text('comment')->nullable()->after('status_code');
            }

            if (!Schema::hasColumn('redirects', 'page_type')) {
                $table->string('page_type')->nullable()->after('comment');
            }

            $table->index('status_code');
            $table->index('page_type');
        });
    }

    public function down()
    {
        Schema::table('redirects', function (Blueprint $table) {
            if (Schema::hasColumn('redirects', 'comment')) {
                $table->dropColumn('comment');
            }

            if (Schema::hasColumn('redirects', 'page_type')) {
                $table->dropColumn('page_type');
            }
        });
    }
};
