<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookExtractedMetadata extends Model
{
    protected $table = 'book_extracted_metadata';

    protected $fillable = [
        'book_id',
        'extracted_section_name',
        'matched_section_id',
        'section_match_confidence',
        'extracted_author_name',
        'extracted_author_death_year',
        'extracted_author_madhhab',
        'matched_author_id',
        'author_match_confidence',
        'extracted_publisher_name',
        'extracted_publisher_city',
        'matched_publisher_id',
        'publisher_match_confidence',
        'extracted_edition',
        'extracted_edition_number',
        'extracted_year_hijri',
        'extracted_year_miladi',
        'extracted_tahqeeq_name',
        'matched_tahqeeq_author_id',
        'extracted_pages_count',
        'extracted_volumes_count',
        'is_processed',
        'is_applied',
        'needs_review',
        'processing_status',
        'error_message',
        'extracted_at',
        'applied_at',
    ];

    protected $casts = [
        'section_match_confidence' => 'decimal:2',
        'author_match_confidence' => 'decimal:2',
        'publisher_match_confidence' => 'decimal:2',
        'extracted_pages_count' => 'integer',
        'extracted_volumes_count' => 'integer',
        'is_processed' => 'boolean',
        'is_applied' => 'boolean',
        'needs_review' => 'boolean',
        'extracted_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    // العلاقات

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function matchedSection(): BelongsTo
    {
        return $this->belongsTo(BookSection::class, 'matched_section_id');
    }

    public function matchedAuthor(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'matched_author_id');
    }

    public function matchedPublisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'matched_publisher_id');
    }

    public function matchedTahqeeqAuthor(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'matched_tahqeeq_author_id');
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    public function scopeNeedsReview($query)
    {
        return $query->where('needs_review', true);
    }
}
