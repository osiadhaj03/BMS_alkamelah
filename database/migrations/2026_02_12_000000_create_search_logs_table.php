<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();

            // ====== بيانات البحث ======
            $table->string('query');
            $table->enum('search_type', ['books', 'authors', 'content']);
            $table->string('search_mode')->nullable();
            $table->string('word_order')->nullable();
            $table->string('word_match')->nullable();

            // ====== نتائج البحث ======
            $table->unsignedInteger('results_count')->default(0);

            // ====== ربط بسجل الزيارات ======
            $table->unsignedBigInteger('page_visit_id')->nullable();
            $table->string('ip_address', 45)->nullable();

            // ====== فلاتر البحث ======
            $table->json('filters')->nullable();

            $table->timestamps();

            // ====== الفهارس ======
            $table->foreign('page_visit_id')
                  ->references('id')
                  ->on('page_visits')
                  ->nullOnDelete();

            $table->index('search_type');
            $table->index('created_at');
            $table->index('ip_address');
            $table->index('query');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
