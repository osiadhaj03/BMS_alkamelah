<?php

namespace App\Livewire\BookReader;

use App\Models\Book;
use App\Models\Page;
use App\Services\BookReader\BookReaderService;
use App\Services\BookReader\PageLoaderService;
use App\Services\BookReader\UserProgressService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

/**
 * Book Reader Page Component
 * 
 * المكون الرئيسي لقراءة الكتب
 */
#[Layout('layouts.reader')]
class BookReaderPage extends Component
{
    // ═══════════════════════════════════════════════════════════
    // PROPERTIES
    // ═══════════════════════════════════════════════════════════

    /**
     * معرف الكتاب
     */
    public int $bookId;

    /**
     * رقم الصفحة الحالية
     */
    #[Url(as: 'page', except: 1)]
    public int $pageNumber = 1;

    /**
     * معرف المجلد المحدد
     */
    #[Url(as: 'vol', except: null)]
    public ?int $volumeId = null;

    /**
     * الكتاب
     */
    public ?Book $book = null;

    /**
     * الصفحة الحالية
     */
    public ?Page $currentPage = null;

    /**
     * محتوى الصفحة المعالج
     */
    public string $pageContent = '';

    /**
     * عنوان الفصل الحالي
     */
    public ?string $currentChapterTitle = null;

    /**
     * عنوان المجلد الحالي
     */
    public ?string $currentVolumeTitle = null;

    // ═══════════════════════════════════════════════════════════
    // SETTINGS PROPERTIES
    // ═══════════════════════════════════════════════════════════

    /**
     * حجم الخط
     */
    public int $fontSize = 22;

    /**
     * الثيم (light, dark, sepia)
     */
    public string $theme = 'light';

    /**
     * إظهار التشكيل
     */
    public bool $showDiacritics = true;

    /**
     * نوع الخط
     */
    public string $fontFamily = 'Amiri';

    // ═══════════════════════════════════════════════════════════
    // UI STATE PROPERTIES
    // ═══════════════════════════════════════════════════════════

    /**
     * إظهار الشريط الجانبي
     */
    public bool $showSidebar = true;

    /**
     * إظهار نافذة البحث
     */
    public bool $showSearchModal = false;

    /**
     * إظهار نافذة الإعدادات
     */
    public bool $showSettingsModal = false;

    /**
     * حالة التحميل
     */
    public bool $isLoading = false;

    // ═══════════════════════════════════════════════════════════
    // SEARCH PROPERTIES
    // ═══════════════════════════════════════════════════════════

    /**
     * نص البحث
     */
    public string $searchQuery = '';

    /**
     * نتائج البحث
     */
    public array $searchResults = [];

    /**
     * البحث في الفهرس
     */
    public string $tocSearchQuery = '';

    // ═══════════════════════════════════════════════════════════
    // TOC STATE
    // ═══════════════════════════════════════════════════════════

    /**
     * المجلدات المفتوحة في الفهرس
     */
    public array $expandedVolumes = [];

    /**
     * الفصول المفتوحة في الفهرس
     */
    public array $expandedChapters = [];

    // ═══════════════════════════════════════════════════════════
    // LIFECYCLE
    // ═══════════════════════════════════════════════════════════

    /**
     * Mount the component
     */
    public function mount(int $bookId, ?int $pageNumber = null): void
    {
        $this->bookId = $bookId;

        // تحميل الكتاب
        $this->loadBook();

        // تحديد الصفحة (من URL أو آخر صفحة مقروءة أو الأولى)
        $this->pageNumber = $pageNumber
            ?? app(UserProgressService::class)->getLastReadPage($this->bookId)
            ?? 1;

        // تحميل الصفحة
        $this->loadPage();

        // تحميل إعدادات المستخدم
        $this->loadUserSettings();

        // فتح المجلد الحالي في الفهرس
        $this->initializeTocState();
    }

    /**
     * تحميل الكتاب
     */
    private function loadBook(): void
    {
        $this->book = app(BookReaderService::class)->getBook($this->bookId);

        if (!$this->book) {
            abort(404, 'الكتاب غير موجود');
        }
    }

    /**
     * تحميل الصفحة
     */
    public function loadPage(): void
    {
        $this->isLoading = true;

        $result = app(PageLoaderService::class)->loadPage(
            $this->bookId,
            $this->pageNumber,
            $this->showDiacritics
        );

        if (!$result['found']) {
            // إذا لم تُوجد الصفحة، اذهب للأولى
            $this->pageNumber = 1;
            $result = app(PageLoaderService::class)->loadPage(
                $this->bookId,
                1,
                $this->showDiacritics
            );
        }

        $this->currentPage = $result['page'];
        $this->pageContent = $result['content'];
        $this->currentChapterTitle = $result['chapter_title'] ?? null;
        $this->currentVolumeTitle = $result['volume_title'] ?? null;
        $this->volumeId = $this->currentPage?->volume_id;

        $this->isLoading = false;

        // حفظ تقدم القراءة
        app(UserProgressService::class)->saveProgress($this->bookId, $this->pageNumber);

        // إرسال حدث تغيير الصفحة للـ JavaScript
        $this->dispatch('page-loaded', [
            'pageNumber' => $this->pageNumber,
            'chapterTitle' => $this->currentChapterTitle,
        ]);
    }

    /**
     * تحميل إعدادات المستخدم
     */
    private function loadUserSettings(): void
    {
        $settings = session('book_reader_settings', []);

        $this->fontSize = $settings['fontSize'] ?? 22;
        $this->theme = $settings['theme'] ?? 'light';
        $this->showDiacritics = $settings['showDiacritics'] ?? true;
        $this->fontFamily = $settings['fontFamily'] ?? 'Amiri';
    }

    /**
     * حفظ إعدادات المستخدم
     */
    private function saveUserSettings(): void
    {
        session([
            'book_reader_settings' => [
                'fontSize' => $this->fontSize,
                'theme' => $this->theme,
                'showDiacritics' => $this->showDiacritics,
                'fontFamily' => $this->fontFamily,
            ]
        ]);
    }

    /**
     * تهيئة حالة الفهرس
     */
    private function initializeTocState(): void
    {
        if ($this->volumeId) {
            $this->expandedVolumes = [$this->volumeId];
        }
    }

    // ═══════════════════════════════════════════════════════════
    // COMPUTED PROPERTIES
    // ═══════════════════════════════════════════════════════════

    /**
     * إجمالي الصفحات
     */
    #[Computed(cache: true)]
    public function totalPages(): int
    {
        return app(BookReaderService::class)->getTotalPages($this->bookId);
    }

    /**
     * الفهرس
     */
    #[Computed(cache: true)]
    public function tableOfContents(): array
    {
        return app(BookReaderService::class)->getTableOfContents($this->bookId);
    }

    /**
     * المجلدات
     */
    #[Computed(cache: true)]
    public function volumes()
    {
        return app(BookReaderService::class)->getVolumes($this->bookId);
    }

    /**
     * نسبة التقدم
     */
    #[Computed]
    public function progressPercentage(): float
    {
        return $this->totalPages > 0
            ? round(($this->pageNumber / $this->totalPages) * 100, 1)
            : 0;
    }

    /**
     * هل يوجد صفحة سابقة
     */
    #[Computed]
    public function hasPreviousPage(): bool
    {
        return $this->pageNumber > 1;
    }

    /**
     * هل يوجد صفحة تالية
     */
    #[Computed]
    public function hasNextPage(): bool
    {
        return $this->pageNumber < $this->totalPages;
    }

    /**
     * الفهرس المفلتر بالبحث
     */
    #[Computed]
    public function filteredTableOfContents(): array
    {
        $toc = $this->tableOfContents;

        if (empty($this->tocSearchQuery)) {
            return $toc;
        }

        $query = mb_strtolower($this->tocSearchQuery);

        // فلترة حسب البحث
        if ($toc['type'] === 'volumes') {
            $toc['data'] = array_map(function ($volume) use ($query) {
                $volume['chapters'] = $this->filterChapters($volume['chapters'], $query);
                return $volume;
            }, $toc['data']);

            // إزالة المجلدات الفارغة
            $toc['data'] = array_filter($toc['data'], function ($volume) use ($query) {
                return !empty($volume['chapters']) ||
                    mb_strpos(mb_strtolower($volume['title']), $query) !== false;
            });
        } else {
            $toc['data'] = $this->filterChapters($toc['data'], $query);
        }

        return $toc;
    }

    /**
     * فلترة الفصول
     */
    private function filterChapters(array $chapters, string $query): array
    {
        return array_filter($chapters, function ($chapter) use ($query) {
            $titleMatch = mb_strpos(mb_strtolower($chapter['title']), $query) !== false;

            if ($titleMatch) {
                return true;
            }

            if (!empty($chapter['children'])) {
                $chapter['children'] = $this->filterChapters($chapter['children'], $query);
                return !empty($chapter['children']);
            }

            return false;
        });
    }

    // ═══════════════════════════════════════════════════════════
    // NAVIGATION ACTIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * الانتقال لصفحة معينة
     */
    public function goToPage(int $page): void
    {
        $this->pageNumber = max(1, min($page, $this->totalPages));
        $this->loadPage();
    }

    /**
     * الصفحة التالية
     */
    public function nextPage(): void
    {
        if ($this->hasNextPage) {
            $this->goToPage($this->pageNumber + 1);
        }
    }

    /**
     * الصفحة السابقة
     */
    public function previousPage(): void
    {
        if ($this->hasPreviousPage) {
            $this->goToPage($this->pageNumber - 1);
        }
    }

    /**
     * الانتقال لمجلد معين
     */
    public function goToVolume(int $volumeId): void
    {
        $firstPage = app(BookReaderService::class)->getFirstPageOfVolume($volumeId);

        if ($firstPage) {
            $this->volumeId = $volumeId;
            $this->goToPage($firstPage);
        }
    }

    /**
     * الانتقال لفصل معين
     */
    public function goToChapter(int $chapterId): void
    {
        $pageStart = app(BookReaderService::class)->getChapterStartPage($chapterId);

        if ($pageStart) {
            $this->goToPage($pageStart);
            $this->showSidebar = false;

            // إغلاق الـ sidebar على الموبايل
            $this->dispatch('close-mobile-sidebar');
        }
    }

    // ═══════════════════════════════════════════════════════════
    // SETTINGS ACTIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * زيادة حجم الخط
     */
    public function increaseFontSize(): void
    {
        $this->fontSize = min(40, $this->fontSize + 2);
        $this->saveUserSettings();
    }

    /**
     * تصغير حجم الخط
     */
    public function decreaseFontSize(): void
    {
        $this->fontSize = max(14, $this->fontSize - 2);
        $this->saveUserSettings();
    }

    /**
     * تغيير الثيم
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
        $this->saveUserSettings();
        $this->dispatch('theme-changed', theme: $theme);
    }

    /**
     * تبديل الثيم
     */
    public function toggleTheme(): void
    {
        $themes = ['light', 'sepia', 'dark'];
        $currentIndex = array_search($this->theme, $themes);
        $nextTheme = $themes[($currentIndex + 1) % count($themes)];
        $this->setTheme($nextTheme);
    }

    /**
     * تبديل التشكيل
     */
    public function toggleDiacritics(): void
    {
        $this->showDiacritics = !$this->showDiacritics;
        $this->saveUserSettings();
        $this->loadPage(); // إعادة تحميل المحتوى
    }

    /**
     * تغيير نوع الخط
     */
    public function setFontFamily(string $fontFamily): void
    {
        $this->fontFamily = $fontFamily;
        $this->saveUserSettings();
    }

    /**
     * تبديل الشريط الجانبي
     */
    public function toggleSidebar(): void
    {
        $this->showSidebar = !$this->showSidebar;
    }

    // ═══════════════════════════════════════════════════════════
    // TOC ACTIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * تبديل فتح/إغلاق مجلد في الفهرس
     */
    public function toggleVolume(int $volumeId): void
    {
        if (in_array($volumeId, $this->expandedVolumes)) {
            $this->expandedVolumes = array_diff($this->expandedVolumes, [$volumeId]);
        } else {
            $this->expandedVolumes[] = $volumeId;
        }
    }

    /**
     * تبديل فتح/إغلاق فصل في الفهرس
     */
    public function toggleChapter(int $chapterId): void
    {
        if (in_array($chapterId, $this->expandedChapters)) {
            $this->expandedChapters = array_diff($this->expandedChapters, [$chapterId]);
        } else {
            $this->expandedChapters[] = $chapterId;
        }
    }

    // ═══════════════════════════════════════════════════════════
    // SEARCH ACTIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * البحث في الكتاب
     */
    public function search(): void
    {
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = app(BookReaderService::class)
            ->searchInBook($this->bookId, $this->searchQuery);
    }

    /**
     * الانتقال لنتيجة بحث
     */
    public function goToSearchResult(int $pageNumber): void
    {
        $this->goToPage($pageNumber);
        $this->showSearchModal = false;

        // تمييز الكلمات في الصفحة
        $this->dispatch('highlight-search-terms', query: $this->searchQuery);
    }

    /**
     * مسح البحث
     */
    public function clearSearch(): void
    {
        $this->searchQuery = '';
        $this->searchResults = [];
    }

    // ═══════════════════════════════════════════════════════════
    // PREFETCH
    // ═══════════════════════════════════════════════════════════

    /**
     * تحميل مسبق للصفحات المجاورة
     */
    public function prefetchPages(array $pageNumbers): void
    {
        app(PageLoaderService::class)->prefetchPages(
            $this->bookId,
            $this->pageNumber,
            2
        );
    }

    // ═══════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ═══════════════════════════════════════════════════════════

    /**
     * الاستماع لأحداث لوحة المفاتيح من JavaScript
     */
    #[On('keyboard-next')]
    public function onKeyboardNext(): void
    {
        $this->nextPage();
    }

    #[On('keyboard-previous')]
    public function onKeyboardPrevious(): void
    {
        $this->previousPage();
    }

    /**
     * حفظ موضع القراءة
     */
    #[On('save-reading-position')]
    public function onSaveReadingPosition(): void
    {
        app(UserProgressService::class)->saveProgress($this->bookId, $this->pageNumber);
    }

    // ═══════════════════════════════════════════════════════════
    // RENDER
    // ═══════════════════════════════════════════════════════════

    public function render()
    {
        return view('livewire.book-reader.book-reader-page');
    }
}
