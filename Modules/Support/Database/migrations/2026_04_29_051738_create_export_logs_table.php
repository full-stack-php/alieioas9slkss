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
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('export_profiles')->onDelete('cascade');
            $table->enum('status', ['processing', 'success', 'error']);
            $table->string('file_path')->nullable();
            $table->integer('total_rows')->default(0);
            $table->timestamp('generated_at')->nullable();
            $table->integer('execution_time')->nullable();
            $table->integer('memory_usage')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
};
