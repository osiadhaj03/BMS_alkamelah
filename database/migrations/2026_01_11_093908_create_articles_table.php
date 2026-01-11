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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            
            // المحتوى
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            
            // التصنيف
            $table->enum('category', ['fiqh', 'hadith', 'history', 'literature', 'technology', 'general'])->default('general');
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            
            // الوسائط
            $table->string('cover_image')->nullable();
            $table->string('thumbnail')->nullable();
            
            // المؤلف ومعلومات النشر
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name')->nullable();
            
            // التواريخ
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            
            // الإحصائيات
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->integer('reading_time')->nullable();
            
            // العلاقات
            $table->foreignId('related_book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('related_author_id')->nullable()->constrained('authors')->nullOnDelete();
            
            // الأولوية
            $table->boolean('is_featured')->default(false);
            $table->integer('priority')->default(0);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // الكلمات المفتاحية
            $table->json('tags')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('category');
            $table->index('published_at');
            $table->index('is_featured');
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
