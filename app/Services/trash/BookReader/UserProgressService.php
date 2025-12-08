<?php

namespace App\Services\BookReader;

use App\Models\UserBookProgress;
use Illuminate\Support\Facades\Auth;

/**
 * User Progress Service
 * 
 * خدمة تتبع تقدم القراءة للمستخدمين
 */
class UserProgressService
{
    /**
     * Save reading progress
     * 
     * @param int $bookId
     * @param int $pageNumber
     * @param int|null $userId
     * @return void
     */
    public function saveProgress(int $bookId, int $pageNumber, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            // حفظ في الجلسة للزوار
            $this->saveToSession($bookId, $pageNumber);
            return;
        }

        UserBookProgress::updateOrCreate(
            ['user_id' => $userId, 'book_id' => $bookId],
            [
                'last_page' => $pageNumber,
                'last_read_at' => now(),
                'total_visits' => \DB::raw('total_visits + 1'),
            ]
        );
    }

    /**
     * Get last read page
     * 
     * @param int $bookId
     * @param int|null $userId
     * @return int|null
     */
    public function getLastReadPage(int $bookId, ?int $userId = null): ?int
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return $this->getFromSession($bookId);
        }

        return UserBookProgress::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->value('last_page');
    }

    /**
     * Get user's reading history
     * 
     * @param int|null $userId
     * @param int $limit
     * @return array
     */
    public function getReadingHistory(?int $userId = null, int $limit = 10): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return [];
        }

        return UserBookProgress::where('user_id', $userId)
            ->with('book:id,title')
            ->orderByDesc('last_read_at')
            ->limit($limit)
            ->get()
            ->map(fn($progress) => [
                'book_id' => $progress->book_id,
                'book_title' => $progress->book?->title,
                'last_page' => $progress->last_page,
                'last_read_at' => $progress->last_read_at,
            ])
            ->toArray();
    }

    /**
     * Update reading time
     * 
     * @param int $bookId
     * @param int $seconds
     * @param int|null $userId
     * @return void
     */
    public function updateReadingTime(int $bookId, int $seconds, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return;
        }

        UserBookProgress::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->increment('reading_time_seconds', $seconds);
    }

    /**
     * Save progress to session (for guests)
     * 
     * @param int $bookId
     * @param int $pageNumber
     * @return void
     */
    private function saveToSession(int $bookId, int $pageNumber): void
    {
        $key = "book_progress.{$bookId}";
        session([$key => $pageNumber]);
    }

    /**
     * Get progress from session (for guests)
     * 
     * @param int $bookId
     * @return int|null
     */
    private function getFromSession(int $bookId): ?int
    {
        $key = "book_progress.{$bookId}";
        return session($key);
    }

    /**
     * Clear user progress for a book
     * 
     * @param int $bookId
     * @param int|null $userId
     * @return void
     */
    public function clearProgress(int $bookId, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            session()->forget("book_progress.{$bookId}");
            return;
        }

        UserBookProgress::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->delete();
    }
}
