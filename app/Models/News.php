<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'status',
        'featured_image',
        'thumbnail',
        'published_at',
        'archived_at',
        'views_count',
        'is_pinned',
        'priority',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_pinned' => 'boolean',
        'views_count' => 'integer',
        'priority' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'announcement' => 'إعلان',
            'update' => 'تحديث',
            'event' => 'فعالية',
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
}
