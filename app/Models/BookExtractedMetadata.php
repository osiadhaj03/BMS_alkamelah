<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookExtractedMetadata extends Model
{
    use HasFactory;

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

    /**
     * حالات المعالجة
     */
    const PROCESSING_STATUS = [
        'pending' => 'قيد الانتظار',
        'extracted' => 'تم الاستخراج',
        'matched' => 'تم المطابقة',
        'applied' => 'تم التطبيق',
        'failed' => 'فشل',
    ];

    /**
     * الكتاب
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * القسم المطابق
     */
    public function matchedSection(): BelongsTo
    {
        return $this->belongsTo(BookSection::class, 'matched_section_id');
    }

    /**
     * المؤلف المطابق
     */
    public function matchedAuthor(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'matched_author_id');
    }

    /**
     * الناشر المطابق
     */
    public function matchedPublisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'matched_publisher_id');
    }

    /**
     * المحقق المطابق
     */
    public function matchedTahqeeqAuthor(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'matched_tahqeeq_author_id');
    }

    /**
     * Scope للسجلات التي تحتاج مراجعة
     */
    public function scopeNeedsReview($query)
    {
        return $query->where('needs_review', true);
    }

    /**
     * Scope للسجلات المعالجة
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    /**
     * Scope للسجلات المطبقة
     */
    public function scopeApplied($query)
    {
        return $query->where('is_applied', true);
    }

    /**
     * Scope حسب حالة المعالجة
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('processing_status', $status);
    }

    /**
     * Scope للسجلات الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    /**
     * الحصول على اسم الحالة بالعربية
     */
    public function getStatusNameAttribute(): string
    {
        return self::PROCESSING_STATUS[$this->processing_status] ?? $this->processing_status;
    }

    /**
     * التحقق من اكتمال المطابقة
     */
    public function isFullyMatched(): bool
    {
        return $this->matched_section_id && 
               $this->matched_author_id && 
               $this->matched_publisher_id;
    }

    /**
     * الحصول على نسبة الثقة الإجمالية
     */
    public function getOverallConfidenceAttribute(): float
    {
        $confidences = array_filter([
            $this->section_match_confidence,
            $this->author_match_confidence,
            $this->publisher_match_confidence,
        ]);

        if (empty($confidences)) {
            return 0;
        }

        return round(array_sum($confidences) / count($confidences), 2);
    }
}
