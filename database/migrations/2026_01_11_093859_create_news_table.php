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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            
            // المحتوى
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            
            // التصنيف والحالة
            $table->enum('category', ['announcement', 'update', 'event', 'general'])->default('general');
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            
            // الوسائط
            $table->string('featured_image')->nullable();
            $table->string('thumbnail')->nullable();
            
            // التواريخ والنشر
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            
            // الإحصائيات
            $table->unsignedBigInteger('views_count')->default(0);
            
            // الأولوية والتثبيت
            $table->boolean('is_pinned')->default(false);
            $table->integer('priority')->default(0);
            
            // المؤلف
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('category');
            $table->index('published_at');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
