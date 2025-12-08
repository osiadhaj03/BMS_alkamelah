# 📖 خطة قارئ الكتب الاحترافي - الإصدار النهائي

## 🎯 الرؤية

بناء قارئ كتب عربية احترافي مستوحى من:
- **موقع تراث (Turath.io)** - أفضل تجربة قراءة للكتب التراثية
- **كود Google** - التصميم العصري والألوان الهادئة
- **المكتبة الشاملة** - الوظائف المتقدمة

---

## 🏗️ البنية المعمارية (Architecture)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         Book Reader Architecture                             │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐          │
│  │   Livewire      │    │   Alpine.js     │    │   Laravel       │          │
│  │   Components    │◄──►│   (Frontend)    │◄──►│   API/Cache     │          │
│  └────────┬────────┘    └────────┬────────┘    └────────┬────────┘          │
│           │                      │                      │                    │
│           ▼                      ▼                      ▼                    │
│  ┌─────────────────────────────────────────────────────────────────┐        │
│  │                    Book Reader Page                              │        │
│  │  ┌─────────┐ ┌─────────────────────────────┐ ┌────────────────┐ │        │
│  │  │ Mini    │ │     Content Area            │ │   Sidebar      │ │        │
│  │  │ Sidebar │ │  ┌───────────────────────┐  │ │   (TOC)        │ │        │
│  │  │         │ │  │    Paper Sheet        │  │ │                │ │        │
│  │  │ [Icons] │ │  │    (Lazy Load)        │  │ │ [Tree View]    │ │        │
│  │  │         │ │  └───────────────────────┘  │ │                │ │        │
│  │  └─────────┘ └─────────────────────────────┘ └────────────────┘ │        │
│  └─────────────────────────────────────────────────────────────────┘        │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 📁 هيكل الملفات الكامل

```
app/
├── Livewire/
│   └── BookReader/
│       ├── BookReaderPage.php          ← المكون الرئيسي (Full Page)
│       ├── Traits/
│       │   ├── WithNavigation.php      ← التنقل بين الصفحات
│       │   ├── WithSearch.php          ← البحث في الكتاب
│       │   ├── WithTableOfContents.php ← الفهرس
│       │   └── WithSettings.php        ← الإعدادات (خط، وضع ليلي)
│       └── Components/
│           ├── TableOfContents.php     ← Livewire Component مستقل
│           └── SearchPanel.php         ← Livewire Component مستقل
│
├── Services/
│   └── BookReader/
│       ├── BookReaderService.php       ← منطق الأعمال الرئيسي
│       ├── PageLoaderService.php       ← تحميل الصفحات الذكي
│       ├── SearchService.php           ← البحث المتقدم
│       └── CacheService.php            ← التخزين المؤقت
│
├── Http/
│   └── Controllers/
│       └── Api/
│           └── BookReaderApiController.php  ← API Endpoints
│
└── Models/
    └── UserBookProgress.php            ← تتبع تقدم القراءة (جديد)

resources/
├── views/
│   ├── livewire/
│   │   └── book-reader/
│   │       ├── book-reader-page.blade.php      ← الصفحة الرئيسية
│   │       ├── partials/
│   │       │   ├── header.blade.php            ← الهيدر
│   │       │   ├── mini-sidebar.blade.php      ← الشريط الجانبي الصغير
│   │       │   ├── toolbar.blade.php           ← شريط الأدوات
│   │       │   ├── content-area.blade.php      ← منطقة المحتوى
│   │       │   ├── paper-sheet.blade.php       ← الورقة
│   │       │   ├── sidebar-toc.blade.php       ← الفهرس الجانبي
│   │       │   ├── navigation-bar.blade.php    ← شريط التنقل السفلي
│   │       │   ├── search-modal.blade.php      ← نافذة البحث
│   │       │   └── settings-modal.blade.php    ← نافذة الإعدادات
│   │       └── components/
│   │           ├── chapter-tree-item.blade.php ← عنصر شجرة الفهرس
│   │           └── search-result-item.blade.php
│   │
│   └── layouts/
│       └── reader.blade.php                    ← Layout خاص بدون header/footer
│
├── css/
│   └── book-reader/
│       ├── variables.css               ← المتغيرات (ألوان، خطوط)
│       ├── base.css                    ← الأساسيات
│       ├── layout.css                  ← التخطيط
│       ├── components.css              ← المكونات
│       ├── paper.css                   ← تصميم الورقة
│       ├── toc.css                     ← الفهرس
│       ├── dark-mode.css               ← الوضع الليلي
│       └── responsive.css              ← الموبايل
│
└── js/
    └── book-reader/
        ├── alpine-components.js        ← Alpine.js Components
        ├── keyboard-shortcuts.js       ← اختصارات لوحة المفاتيح
        ├── page-cache.js               ← تخزين الصفحات مؤقتاً
        └── highlight.js                ← تمييز نتائج البحث

routes/
├── web.php                             ← Route الرئيسي
└── api.php                             ← API Routes

database/
└── migrations/
    └── xxxx_create_user_book_progress_table.php
```

---

## 🎨 التصميم المرئي (مستوحى من تراث + Google)

### الألوان والمتغيرات

```css
/* resources/css/book-reader/variables.css */

:root {
    /* === الألوان الأساسية (مستوحاة من تراث) === */
    --color-primary: #2c5f41;           /* أخضر داكن تراثي */
    --color-primary-light: #3d7a56;
    --color-primary-lighter: #e8f4ed;
    
    /* === ألوان الخلفية === */
    --bg-body: #f5f3ee;                 /* بيج دافئ */
    --bg-paper: #fffdf8;                /* لون ورق طبيعي */
    --bg-sidebar: #fafaf7;
    --bg-toolbar: #ffffff;
    
    /* === ألوان النص === */
    --text-primary: #1f2937;            /* أسود فحمي */
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    
    /* === الحدود والظلال === */
    --border-color: #e5e2d9;
    --border-light: #f0ede6;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
    --shadow-paper: 0 0 40px rgba(0,0,0,0.06);
    
    /* === الخطوط === */
    --font-reading: 'Amiri', 'Traditional Arabic', serif;
    --font-ui: 'Tajawal', 'Noto Kufi Arabic', sans-serif;
    --font-size-base: 22px;
    --line-height: 2.0;
    
    /* === الأبعاد === */
    --sidebar-width: 320px;
    --mini-sidebar-width: 56px;
    --toolbar-height: 64px;
    --nav-bar-height: 60px;
    --paper-max-width: 800px;
    --paper-padding: 60px 80px;
    
    /* === الحركة === */
    --transition-fast: 150ms ease;
    --transition-normal: 250ms ease;
    --transition-slow: 400ms ease;
    
    /* === الزوايا === */
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;
    --radius-full: 9999px;
}

/* === الوضع الليلي === */
[data-theme="dark"] {
    --color-primary: #4ade80;
    --color-primary-light: #86efac;
    --color-primary-lighter: #1a2e23;
    
    --bg-body: #111827;
    --bg-paper: #1f2937;
    --bg-sidebar: #1a202c;
    --bg-toolbar: #1f2937;
    
    --text-primary: #f3f4f6;
    --text-secondary: #9ca3af;
    --text-muted: #6b7280;
    
    --border-color: #374151;
    --border-light: #2d3748;
    --shadow-paper: none;
}

/* === وضع السبيا (Sepia) للقراءة المريحة === */
[data-theme="sepia"] {
    --bg-body: #f4ecd8;
    --bg-paper: #faf6e9;
    --text-primary: #5c4b37;
}
```

---

## 🧩 المكونات الرئيسية

### 1. Livewire Component الرئيسي

```php
<?php
// app/Livewire/BookReader/BookReaderPage.php

namespace App\Livewire\BookReader;

use App\Models\Book;
use App\Models\Page;
use App\Services\BookReader\BookReaderService;
use App\Services\BookReader\PageLoaderService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

#[Layout('layouts.reader')]
class BookReaderPage extends Component
{
    // ═══════════════════════════════════════════════════════════
    // PROPERTIES
    // ═══════════════════════════════════════════════════════════
    
    public int $bookId;
    
    #[Url(as: 'page')]
    public int $pageNumber = 1;
    
    #[Url(as: 'vol')]
    public ?int $volumeId = null;
    
    public ?Book $book = null;
    public ?Page $currentPage = null;
    public string $pageContent = '';
    
    // إعدادات العرض
    public int $fontSize = 22;
    public string $theme = 'light'; // light, dark, sepia
    public bool $showDiacritics = true;
    public string $fontFamily = 'Amiri';
    
    // حالة الواجهة
    public bool $showSidebar = true;
    public bool $showSearchModal = false;
    public bool $showSettingsModal = false;
    public bool $isLoading = false;
    
    // البحث
    public string $searchQuery = '';
    public array $searchResults = [];
    
    // ═══════════════════════════════════════════════════════════
    // LIFECYCLE
    // ═══════════════════════════════════════════════════════════
    
    public function mount(int $bookId, ?int $pageNumber = null): void
    {
        $this->bookId = $bookId;
        $this->pageNumber = $pageNumber ?? $this->getLastReadPage() ?? 1;
        
        $this->loadBook();
        $this->loadPage();
        $this->loadUserSettings();
    }
    
    public function boot(): void
    {
        // تحميل الخدمات
    }
    
    // ═══════════════════════════════════════════════════════════
    // COMPUTED PROPERTIES
    // ═══════════════════════════════════════════════════════════
    
    #[Computed(cache: true)]
    public function totalPages(): int
    {
        return app(BookReaderService::class)->getTotalPages($this->bookId);
    }
    
    #[Computed(cache: true)]
    public function tableOfContents(): array
    {
        return app(BookReaderService::class)->getTableOfContents($this->bookId);
    }
    
    #[Computed]
    public function progressPercentage(): float
    {
        return $this->totalPages > 0 
            ? round(($this->pageNumber / $this->totalPages) * 100, 1) 
            : 0;
    }
    
    #[Computed]
    public function hasPreviousPage(): bool
    {
        return $this->pageNumber > 1;
    }
    
    #[Computed]
    public function hasNextPage(): bool
    {
        return $this->pageNumber < $this->totalPages;
    }
    
    #[Computed]
    public function volumes()
    {
        return $this->book?->volumes()->orderBy('number')->get() ?? collect();
    }
    
    // ═══════════════════════════════════════════════════════════
    // ACTIONS - NAVIGATION
    // ═══════════════════════════════════════════════════════════
    
    public function goToPage(int $page): void
    {
        $this->pageNumber = max(1, min($page, $this->totalPages));
        $this->loadPage();
        $this->saveReadingProgress();
        
        $this->dispatch('page-changed', pageNumber: $this->pageNumber);
    }
    
    public function nextPage(): void
    {
        if ($this->hasNextPage) {
            $this->goToPage($this->pageNumber + 1);
        }
    }
    
    public function previousPage(): void
    {
        if ($this->hasPreviousPage) {
            $this->goToPage($this->pageNumber - 1);
        }
    }
    
    public function goToVolume(int $volumeId): void
    {
        $firstPage = app(BookReaderService::class)->getFirstPageOfVolume($volumeId);
        if ($firstPage) {
            $this->volumeId = $volumeId;
            $this->goToPage($firstPage);
        }
    }
    
    public function goToChapter(int $chapterId): void
    {
        $pageStart = app(BookReaderService::class)->getChapterStartPage($chapterId);
        if ($pageStart) {
            $this->goToPage($pageStart);
            $this->showSidebar = false;
        }
    }
    
    // ═══════════════════════════════════════════════════════════
    // ACTIONS - SETTINGS
    // ═══════════════════════════════════════════════════════════
    
    public function increaseFontSize(): void
    {
        $this->fontSize = min(40, $this->fontSize + 2);
        $this->saveUserSettings();
    }
    
    public function decreaseFontSize(): void
    {
        $this->fontSize = max(14, $this->fontSize - 2);
        $this->saveUserSettings();
    }
    
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
        $this->saveUserSettings();
        $this->dispatch('theme-changed', theme: $theme);
    }
    
    public function toggleDiacritics(): void
    {
        $this->showDiacritics = !$this->showDiacritics;
        $this->saveUserSettings();
    }
    
    public function toggleSidebar(): void
    {
        $this->showSidebar = !$this->showSidebar;
    }
    
    // ═══════════════════════════════════════════════════════════
    // ACTIONS - SEARCH
    // ═══════════════════════════════════════════════════════════
    
    public function search(): void
    {
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = [];
            return;
        }
        
        $this->searchResults = app(BookReaderService::class)
            ->searchInBook($this->bookId, $this->searchQuery);
    }
    
    public function goToSearchResult(int $pageNumber): void
    {
        $this->goToPage($pageNumber);
        $this->showSearchModal = false;
        
        // تمييز الكلمات في الصفحة
        $this->dispatch('highlight-terms', query: $this->searchQuery);
    }
    
    // ═══════════════════════════════════════════════════════════
    // PRIVATE METHODS
    // ═══════════════════════════════════════════════════════════
    
    private function loadBook(): void
    {
        $this->book = app(BookReaderService::class)->getBook($this->bookId);
        
        if (!$this->book) {
            abort(404, 'الكتاب غير موجود');
        }
    }
    
    private function loadPage(): void
    {
        $this->isLoading = true;
        
        $result = app(PageLoaderService::class)->loadPage(
            $this->bookId, 
            $this->pageNumber,
            $this->showDiacritics
        );
        
        $this->currentPage = $result['page'];
        $this->pageContent = $result['content'];
        $this->volumeId = $this->currentPage?->volume_id;
        
        $this->isLoading = false;
    }
    
    private function loadUserSettings(): void
    {
        // تحميل إعدادات المستخدم من الجلسة أو قاعدة البيانات
        $settings = session('book_reader_settings', []);
        
        $this->fontSize = $settings['fontSize'] ?? 22;
        $this->theme = $settings['theme'] ?? 'light';
        $this->showDiacritics = $settings['showDiacritics'] ?? true;
        $this->fontFamily = $settings['fontFamily'] ?? 'Amiri';
    }
    
    private function saveUserSettings(): void
    {
        session(['book_reader_settings' => [
            'fontSize' => $this->fontSize,
            'theme' => $this->theme,
            'showDiacritics' => $this->showDiacritics,
            'fontFamily' => $this->fontFamily,
        ]]);
    }
    
    private function saveReadingProgress(): void
    {
        if (auth()->check()) {
            app(BookReaderService::class)->saveProgress(
                auth()->id(),
                $this->bookId,
                $this->pageNumber
            );
        }
    }
    
    private function getLastReadPage(): ?int
    {
        if (auth()->check()) {
            return app(BookReaderService::class)->getLastReadPage(
                auth()->id(),
                $this->bookId
            );
        }
        return null;
    }
    
    // ═══════════════════════════════════════════════════════════
    // RENDER
    // ═══════════════════════════════════════════════════════════
    
    public function render()
    {
        return view('livewire.book-reader.book-reader-page');
    }
}
```

---

### 2. Service Layer

```php
<?php
// app/Services/BookReader/BookReaderService.php

namespace App\Services\BookReader;

use App\Models\Book;
use App\Models\Page;
use App\Models\Chapter;
use App\Models\Volume;
use App\Models\UserBookProgress;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class BookReaderService
{
    private const CACHE_TTL = 60 * 60 * 6; // 6 hours
    
    /**
     * Get book with essential relationships
     */
    public function getBook(int $bookId): ?Book
    {
        return Cache::remember(
            "book_reader:{$bookId}:book",
            self::CACHE_TTL,
            fn() => Book::with(['authors', 'bookSection'])
                ->where('visibility', 'public')
                ->find($bookId)
        );
    }
    
    /**
     * Get total pages count (cached)
     */
    public function getTotalPages(int $bookId): int
    {
        return Cache::remember(
            "book_reader:{$bookId}:total_pages",
            self::CACHE_TTL,
            fn() => Page::where('book_id', $bookId)->count()
        );
    }
    
    /**
     * Get table of contents (cached)
     */
    public function getTableOfContents(int $bookId): array
    {
        return Cache::remember(
            "book_reader:{$bookId}:toc",
            self::CACHE_TTL,
            fn() => $this->buildTableOfContents($bookId)
        );
    }
    
    /**
     * Build table of contents structure
     */
    private function buildTableOfContents(int $bookId): array
    {
        $volumes = Volume::where('book_id', $bookId)
            ->with(['chapters' => fn($q) => $q->whereNull('parent_id')->orderBy('order')])
            ->orderBy('number')
            ->get();
        
        if ($volumes->isEmpty()) {
            $chapters = Chapter::where('book_id', $bookId)
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('order')
                ->get();
            
            return [
                'type' => 'chapters_only',
                'data' => $this->formatChaptersForToc($chapters),
            ];
        }
        
        return [
            'type' => 'volumes',
            'data' => $volumes->map(fn($vol) => [
                'id' => $vol->id,
                'number' => $vol->number,
                'title' => $vol->title ?: "الجزء {$vol->number}",
                'page_start' => $vol->page_start,
                'chapters' => $this->formatChaptersForToc($vol->chapters),
            ])->toArray(),
        ];
    }
    
    /**
     * Format chapters recursively for TOC
     */
    private function formatChaptersForToc(Collection $chapters): array
    {
        return $chapters->map(fn($ch) => [
            'id' => $ch->id,
            'title' => $ch->title,
            'page_start' => $ch->page_start,
            'level' => $ch->level ?? 0,
            'children' => $ch->children->isNotEmpty() 
                ? $this->formatChaptersForToc($ch->children) 
                : [],
        ])->toArray();
    }
    
    /**
     * Search in book content
     */
    public function searchInBook(int $bookId, string $query, int $limit = 50): array
    {
        return Page::where('book_id', $bookId)
            ->where('content', 'LIKE', "%{$query}%")
            ->select(['id', 'page_number', 'content', 'chapter_id', 'volume_id'])
            ->with(['chapter:id,title', 'volume:id,number,title'])
            ->limit($limit)
            ->get()
            ->map(fn($page) => [
                'page_number' => $page->page_number,
                'excerpt' => $this->extractExcerpt($page->content, $query),
                'chapter' => $page->chapter?->title,
                'volume' => $page->volume?->title ?: "الجزء {$page->volume?->number}",
            ])
            ->toArray();
    }
    
    /**
     * Extract excerpt around search term
     */
    private function extractExcerpt(string $content, string $query, int $length = 150): string
    {
        $content = strip_tags($content);
        $pos = mb_stripos($content, $query);
        
        if ($pos === false) {
            return mb_substr($content, 0, $length) . '...';
        }
        
        $start = max(0, $pos - 60);
        $excerpt = mb_substr($content, $start, $length);
        
        // Highlight the query
        $excerpt = preg_replace(
            '/(' . preg_quote($query, '/') . ')/iu',
            '<mark>$1</mark>',
            $excerpt
        );
        
        return ($start > 0 ? '...' : '') . $excerpt . '...';
    }
    
    /**
     * Get first page of volume
     */
    public function getFirstPageOfVolume(int $volumeId): ?int
    {
        return Page::where('volume_id', $volumeId)
            ->orderBy('page_number')
            ->value('page_number');
    }
    
    /**
     * Get chapter start page
     */
    public function getChapterStartPage(int $chapterId): ?int
    {
        $chapter = Chapter::find($chapterId);
        return $chapter?->page_start;
    }
    
    /**
     * Save reading progress
     */
    public function saveProgress(int $userId, int $bookId, int $pageNumber): void
    {
        UserBookProgress::updateOrCreate(
            ['user_id' => $userId, 'book_id' => $bookId],
            ['last_page' => $pageNumber, 'updated_at' => now()]
        );
    }
    
    /**
     * Get last read page
     */
    public function getLastReadPage(int $userId, int $bookId): ?int
    {
        return UserBookProgress::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->value('last_page');
    }
}
```

---

### 3. Page Loader Service (التحميل الذكي)

```php
<?php
// app/Services/BookReader/PageLoaderService.php

namespace App\Services\BookReader;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Mews\Purifier\Facades\Purifier;

class PageLoaderService
{
    private const CACHE_TTL = 60 * 60; // 1 hour
    
    /**
     * Load page with smart caching
     */
    public function loadPage(int $bookId, int $pageNumber, bool $showDiacritics = true): array
    {
        $cacheKey = "book_reader:{$bookId}:page:{$pageNumber}";
        
        $page = Cache::remember($cacheKey, self::CACHE_TTL, function() use ($bookId, $pageNumber) {
            return Page::where('book_id', $bookId)
                ->where('page_number', $pageNumber)
                ->with(['chapter:id,title', 'volume:id,number,title'])
                ->first();
        });
        
        if (!$page) {
            return ['page' => null, 'content' => ''];
        }
        
        $content = $this->processContent($page->content, $showDiacritics);
        
        return [
            'page' => $page,
            'content' => $content,
        ];
    }
    
    /**
     * Process and sanitize content
     */
    private function processContent(string $content, bool $showDiacritics): string
    {
        if (empty($content)) {
            return '';
        }
        
        // Sanitize HTML
        $content = Purifier::clean($content, [
            'HTML.Allowed' => 'p,br,strong,em,b,i,u,h1,h2,h3,h4,h5,h6,ul,ol,li,blockquote,span,div,sup,sub,table,tr,td,th',
            'HTML.AllowedAttributes' => 'class,id,style',
            'CSS.AllowedProperties' => 'color,font-weight,text-align',
        ]);
        
        // Remove diacritics if needed
        if (!$showDiacritics) {
            $content = $this->removeDiacritics($content);
        }
        
        // Smart paragraph formatting
        $content = $this->formatParagraphs($content);
        
        return $content;
    }
    
    /**
     * Remove Arabic diacritics
     */
    private function removeDiacritics(string $text): string
    {
        // Arabic diacritics Unicode range
        return preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
    }
    
    /**
     * Smart paragraph formatting
     */
    private function formatParagraphs(string $content): string
    {
        // Normalize line breaks
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        
        // Remove breaks after non-terminal punctuation
        $content = preg_replace('/([،,؛;:])\s*\n\s*/', '$1 ', $content);
        
        // Preserve double line breaks (paragraph breaks)
        $content = preg_replace('/\n\n+/', "\n\n", $content);
        
        // Convert to br tags
        return nl2br($content);
    }
    
    /**
     * Prefetch adjacent pages (for performance)
     */
    public function prefetchPages(int $bookId, int $currentPage, int $range = 2): void
    {
        $pages = range(
            max(1, $currentPage - $range),
            $currentPage + $range
        );
        
        foreach ($pages as $pageNumber) {
            $cacheKey = "book_reader:{$bookId}:page:{$pageNumber}";
            
            if (!Cache::has($cacheKey)) {
                Cache::remember($cacheKey, self::CACHE_TTL, function() use ($bookId, $pageNumber) {
                    return Page::where('book_id', $bookId)
                        ->where('page_number', $pageNumber)
                        ->with(['chapter:id,title', 'volume:id,number,title'])
                        ->first();
                });
            }
        }
    }
}
```

---

## 🖼️ View الرئيسية

```blade
{{-- resources/views/livewire/book-reader/book-reader-page.blade.php --}}

<div 
    class="book-reader"
    x-data="bookReader({
        bookId: @js($bookId),
        pageNumber: @js($pageNumber),
        totalPages: @js($this->totalPages),
        theme: @js($theme),
        fontSize: @js($fontSize),
    })"
    :data-theme="theme"
    x-on:keydown.escape="closeModals()"
    x-on:keydown.arrow-left="$wire.nextPage()"
    x-on:keydown.arrow-right="$wire.previousPage()"
>
    {{-- Mini Sidebar (Icons) --}}
    @include('livewire.book-reader.partials.mini-sidebar')
    
    {{-- Main Container --}}
    <div class="reader-main">
        
        {{-- Header / Toolbar --}}
        @include('livewire.book-reader.partials.header')
        
        {{-- Workspace --}}
        <div class="reader-workspace">
            
            {{-- Table of Contents Sidebar --}}
            <aside 
                class="reader-sidebar"
                x-show="$wire.showSidebar"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-cloak
            >
                @include('livewire.book-reader.partials.sidebar-toc')
            </aside>
            
            {{-- Content Area --}}
            <main class="reader-content" id="reader-content">
                @include('livewire.book-reader.partials.content-area')
            </main>
            
        </div>
        
        {{-- Navigation Bar --}}
        @include('livewire.book-reader.partials.navigation-bar')
        
    </div>
    
    {{-- Modals --}}
    @include('livewire.book-reader.partials.search-modal')
    @include('livewire.book-reader.partials.settings-modal')
    
    {{-- Loading Overlay --}}
    <div 
        class="reader-loading"
        x-show="$wire.isLoading"
        x-transition
    >
        <div class="loading-spinner"></div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/book-reader.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/book-reader.js') }}" defer></script>
@endpush
```

---

## 📱 Responsive Design

```css
/* resources/css/book-reader/responsive.css */

/* Tablet */
@media (max-width: 1024px) {
    .reader-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        width: 85%;
        max-width: 360px;
        transform: translateX(100%);
    }
    
    .reader-sidebar[x-show="true"] {
        transform: translateX(0);
    }
    
    .paper-sheet {
        padding: 40px 50px;
        max-width: 100%;
    }
}

/* Mobile */
@media (max-width: 640px) {
    .mini-sidebar {
        display: none;
    }
    
    .paper-sheet {
        padding: 30px 20px;
        border-radius: 0;
        border: none;
    }
    
    .reader-toolbar-pill {
        padding: 4px 8px;
    }
    
    .nav-bar {
        padding: 8px 16px;
    }
    
    .chapter-text {
        font-size: 18px !important;
        line-height: 1.9;
    }
}
```

---

## ⌨️ Keyboard Shortcuts

```javascript
// resources/js/book-reader/keyboard-shortcuts.js

document.addEventListener('alpine:init', () => {
    Alpine.data('bookReader', (config) => ({
        ...config,
        
        init() {
            this.setupKeyboardShortcuts();
        },
        
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Don't trigger if typing in input
                if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
                
                switch(e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.$wire.nextPage();
                        break;
                        
                    case 'ArrowRight':
                        e.preventDefault();
                        this.$wire.previousPage();
                        break;
                        
                    case 'f':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.showSearchModal = true;
                        }
                        break;
                        
                    case 's':
                        e.preventDefault();
                        this.$wire.toggleSidebar();
                        break;
                        
                    case 'd':
                        e.preventDefault();
                        this.toggleTheme();
                        break;
                        
                    case '+':
                    case '=':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.increaseFontSize();
                        }
                        break;
                        
                    case '-':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.decreaseFontSize();
                        }
                        break;
                        
                    case 'Home':
                        e.preventDefault();
                        this.$wire.goToPage(1);
                        break;
                        
                    case 'End':
                        e.preventDefault();
                        this.$wire.goToPage(this.totalPages);
                        break;
                        
                    case 'Escape':
                        this.closeModals();
                        break;
                }
            });
        },
        
        toggleTheme() {
            const themes = ['light', 'sepia', 'dark'];
            const currentIndex = themes.indexOf(this.theme);
            const nextTheme = themes[(currentIndex + 1) % themes.length];
            this.$wire.setTheme(nextTheme);
        },
        
        closeModals() {
            this.$wire.showSearchModal = false;
            this.$wire.showSettingsModal = false;
        },
    }));
});
```

---

## 📊 Database Migration

```php
<?php
// database/migrations/xxxx_create_user_book_progress_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_book_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->integer('last_page')->default(1);
            $table->integer('reading_time_seconds')->default(0);
            $table->integer('total_visits')->default(1);
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'book_id']);
            $table->index(['user_id', 'updated_at']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('user_book_progress');
    }
};
```

---

## ✅ خطة التنفيذ المرحلية

### المرحلة 1: الأساس (4-6 ساعات)
- [ ] إنشاء `BookReaderPage.php` Livewire Component
- [ ] إنشاء `BookReaderService.php`
- [ ] إنشاء `PageLoaderService.php`
- [ ] إنشاء Layout `layouts/reader.blade.php`
- [ ] إنشاء Route

### المرحلة 2: الواجهة (4-6 ساعات)
- [ ] إنشاء CSS Variables
- [ ] تصميم Header/Toolbar
- [ ] تصميم Paper Sheet
- [ ] تصميم Navigation Bar
- [ ] الوضع الليلي + Sepia

### المرحلة 3: الفهرس (3-4 ساعات)
- [ ] تصميم Sidebar TOC
- [ ] شجرة الفهرس المتداخلة
- [ ] البحث في الفهرس
- [ ] الانتقال للفصول

### المرحلة 4: البحث والميزات (3-4 ساعات)
- [ ] نافذة البحث
- [ ] تمييز نتائج البحث
- [ ] اختصارات لوحة المفاتيح
- [ ] حفظ تقدم القراءة

### المرحلة 5: التحسينات (2-3 ساعات)
- [ ] Responsive للموبايل
- [ ] تحسين الأداء (Caching, Prefetch)
- [ ] نافذة الإعدادات
- [ ] المشاركة

**المجموع: ~16-23 ساعة عمل**

---

## 🎯 الخلاصة

هذه الخطة تجمع بين:
1. **تصميم تراث** - تجربة قراءة مريحة
2. **كود Google** - الألوان والتصميم العصري
3. **المشروع القديم** - المنطق المجرب
4. **أفضل الممارسات** - Livewire 3, Services, Caching

هل نبدأ بالتنفيذ؟ 🚀
