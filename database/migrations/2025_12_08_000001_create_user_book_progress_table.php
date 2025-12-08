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
        Schema::create('user_book_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('last_page')->default(1);
            $table->unsignedInteger('furthest_page')->default(1);
            $table->unsignedInteger('total_visits')->default(1);
            $table->unsignedInteger('total_reading_time')->default(0)->comment('بالثواني');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            // فهارس
            $table->unique(['user_id', 'book_id']);
            $table->index('last_read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_book_progress');
    }
};
