<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'status',
        'cover_image',
        'thumbnail',
        'author_id',
        'author_name',
        'published_at',
        'archived_at',
        'views_count',
        'likes_count',
        'shares_count',
        'reading_time',
        'related_book_id',
        'related_author_id',
        'is_featured',
        'priority',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'shares_count' => 'integer',
        'reading_time' => 'integer',
        'priority' => 'integer',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            
            // Auto-calculate reading time based on content
            if (empty($article->reading_time)) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = ceil($wordCount / 200); // 200 words per minute
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function relatedBook()
    {
        return $this->belongsTo(Book::class, 'related_book_id');
    }

    public function relatedAuthor()
    {
        return $this->belongsTo(Author::class, 'related_author_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(ArticleComment::class)->where('status', 'approved');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'fiqh' => 'فقه',
            'hadith' => 'حديث',
            'history' => 'تاريخ',
            'literature' => 'أدب',
            'technology' => 'تقنية',
            'general' => 'عام',
            default => 'غير محدد'
        };
    }

    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'published' => 'منشور',
            'scheduled' => 'مجدول',
            'archived' => 'مؤرشف',
            default => 'غير محدد'
        };
    }

    // Mutators
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }
}
