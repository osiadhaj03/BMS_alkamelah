<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookMetadata extends Model
{
    use HasFactory;

    protected $table = 'book_metadata';

    protected $fillable = [
        'book_id',
        'images',
        'video_links',
        'edition',
        'edition_year',
        'download_links',
        'metadata',
    ];

    protected $casts = [
        'images' => 'array',
        'video_links' => 'array',
        'download_links' => 'array',
        'metadata' => 'array',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
