# 📖 خطة صفحة قراءة الكتب (Book Reader)

## 📋 ملخص تحليلي

### ما لديك في المشروع القديم (BMS_v1):
- ✅ **Livewire Component** متكامل (`BookReader.php` - 1135 سطر)
- ✅ نظام تنقل بين الصفحات
- ✅ فهرس محتويات (أجزاء + فصول متداخلة)
- ✅ بحث داخل الكتاب
- ✅ تحكم بحجم الخط
- ✅ شريط تقدم القراءة
- ✅ حماية XSS باستخدام HTML Purifier
- ✅ Caching للأداء

### ما سنبنيه (محسّن ومطوّر):
صفحة قراءة عصرية مستوحاة من الصور المرفقة + كود Google المقترح

---

## 🎨 التصميم المرئي النهائي

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  🔖  │  📖 عمدة الرعاية على شرح الوقاية                    [🔍][A-][A+][🌙][📤] │
│  📚  │       الإمام اللكنوي (ت 747هـ)                                        │
│  ⭐  ├───────────────────────────────────────────────────────────────────────┤
│      │                                                                       │
│      │   ┌─────────────────────────────────────────┐   ┌──────────────────┐ │
│      │   │  ╔═══════════════════════════════════╗  │   │ 📑 فهرس الكتاب  │ │
│      │   │  ║                                   ║  │   │ ──────────────── │ │
│      │   │  ║    [باب الطهارة]                  ║  │   │ 🔍 ابحث...       │ │
│      │   │  ║                                   ║  │   │                  │ │
│      │   │  ║  الحمد لله رب العالمين...         ║  │   │ 📁 المجلد الأول  │ │
│      │   │  ║                                   ║  │   │   ├─ مقدمة المحقق│ │
│      │   │  ║  وقد تميز هذا الكتاب بدقة        ║  │   │   ├─ كتاب الطهارة│ │
│      │   │  ║  العبارة، وحسن الترتيب...         ║  │   │   │  ├─ الوضوء   │ │
│      │   │  ║                                   ║  │   │   │  └─ الغسل    │ │
│      │   │  ║         ❖                         ║  │   │   └─ كتاب الصلاة│ │
│      │   │  ╚═══════════════════════════════════╝  │   │ 📁 المجلد الثاني│ │
│      │   └─────────────────────────────────────────┘   │   └─ ...         │ │
│      │                                                  └──────────────────┘ │
│      │   [◀] ─────────────●───────────────────── [▶]   صفحة 45 / 310        │
│      │        ج: [1 ▼]                                                       │
└──────┴───────────────────────────────────────────────────────────────────────┘
```

---

## 🏗️ هيكل الملفات

```
app/
├── Livewire/
│   └── BookReader/
│       ├── BookReaderPage.php      ← المكون الرئيسي
│       ├── TableOfContents.php     ← الفهرس (مستقل للأداء)
│       └── SearchPanel.php         ← البحث (مستقل)
│
├── Http/Controllers/Api/
│   └── BookReaderController.php    ← API للتحميل الذكي
│
└── Services/
    └── BookReaderService.php       ← منطق الأعمال

resources/views/
├── livewire/
│   └── book-reader/
│       ├── book-reader-page.blade.php
│       ├── table-of-contents.blade.php
│       └── search-panel.blade.php
│
└── components/
    └── book-reader/
        ├── toolbar.blade.php
        ├── page-content.blade.php
        ├── navigation-bar.blade.php
        └── mini-sidebar.blade.php

routes/
└── web.php                         ← Route للقارئ
```

---

## 📅 مراحل التنفيذ

### 🚀 المرحلة 1: الأساس (3-4 ساعات)

#### 1.1 إنشاء Livewire Component الرئيسي

```php
// app/Livewire/BookReader/BookReaderPage.php

<?php

namespace App\Livewire\BookReader;

use App\Models\Book;
use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class BookReaderPage extends Component
{
    // Properties
    public int $bookId;
    public int $pageNumber = 1;
    public ?Book $book = null;
    public ?Page $currentPage = null;
    public int $totalPages = 0;
    public int $fontSize = 24;
    public bool $darkMode = false;
    public bool $showMovements = true;
    public ?int $selectedVolumeId = null;
    
    // Query String
    protected $queryString = [
        'pageNumber' => ['as' => 'page', 'except' => 1],
    ];

    public function mount(int $bookId, ?int $pageNumber = null): void
    {
        $this->bookId = $bookId;
        $this->pageNumber = $pageNumber ?? 1;
        
        $this->loadBook();
        $this->loadPage();
    }

    private function loadBook(): void
    {
        $this->book = Cache::remember(
            "book_{$this->bookId}_basic",
            now()->addHours(6),
            fn() => Book::with(['authors', 'bookSection'])->findOrFail($this->bookId)
        );
        
        $this->totalPages = Cache::remember(
            "book_{$this->bookId}_total_pages",
            now()->addHours(1),
            fn() => Page::where('book_id', $this->bookId)->count()
        );
    }

    public function loadPage(): void
    {
        $this->currentPage = Page::where('book_id', $this->bookId)
            ->where('page_number', $this->pageNumber)
            ->with(['chapter', 'volume'])
            ->first();
            
        if ($this->currentPage) {
            $this->selectedVolumeId = $this->currentPage->volume_id;
        }
    }

    public function nextPage(): void
    {
        if ($this->pageNumber < $this->totalPages) {
            $this->pageNumber++;
            $this->loadPage();
        }
    }

    public function previousPage(): void
    {
        if ($this->pageNumber > 1) {
            $this->pageNumber--;
            $this->loadPage();
        }
    }

    public function goToPage(int $page): void
    {
        $this->pageNumber = max(1, min($page, $this->totalPages));
        $this->loadPage();
    }

    public function increaseFontSize(): void
    {
        $this->fontSize = min(40, $this->fontSize + 2);
    }

    public function decreaseFontSize(): void
    {
        $this->fontSize = max(14, $this->fontSize - 2);
    }

    public function toggleDarkMode(): void
    {
        $this->darkMode = !$this->darkMode;
    }

    public function toggleMovements(): void
    {
        $this->showMovements = !$this->showMovements;
    }

    public function render()
    {
        return view('livewire.book-reader.book-reader-page')
            ->layout('layouts.reader'); // Layout خاص بدون header/footer
    }
}
```

#### 1.2 إنشاء الـ View الأساسية

```blade
{{-- resources/views/livewire/book-reader/book-reader-page.blade.php --}}

<div class="book-reader-container" 
     x-data="bookReader(@js([
         'bookId' => $bookId,
         'pageNumber' => $pageNumber,
         'totalPages' => $totalPages,
         'darkMode' => $darkMode,
     ]))"
     :class="{ 'dark-mode': darkMode }"
     dir="rtl">
    
    {{-- Mini Sidebar (الأيقونات على اليسار) --}}
    @include('components.book-reader.mini-sidebar')
    
    {{-- Main Container --}}
    <div class="main-container">
        
        {{-- Header / Toolbar --}}
        @include('components.book-reader.toolbar')
        
        {{-- Workspace --}}
        <div class="workspace">
            
            {{-- Content Area --}}
            <main class="content-area">
                @include('components.book-reader.page-content')
            </main>
            
            {{-- Table of Contents Sidebar --}}
            <aside class="toc-sidebar" x-show="showToc" x-cloak>
                <livewire:book-reader.table-of-contents 
                    :book-id="$bookId" 
                    :current-page="$pageNumber"
                    wire:key="toc-{{ $bookId }}" />
            </aside>
            
        </div>
        
        {{-- Navigation Bar --}}
        @include('components.book-reader.navigation-bar')
        
    </div>
</div>
```

#### 1.3 إنشاء Route

```php
// routes/web.php

use App\Livewire\BookReader\BookReaderPage;

Route::get('/read/{bookId}/{pageNumber?}', BookReaderPage::class)
    ->name('book.read')
    ->where(['bookId' => '[0-9]+', 'pageNumber' => '[0-9]+']);
```

---

### 🚀 المرحلة 2: الـ Styling (2-3 ساعات)

#### 2.1 CSS Variables (متغيرات الألوان)

```css
/* resources/css/book-reader.css */

:root {
    /* النمط النهاري "التراثي" */
    --bg-body: #f3f1eb;
    --bg-paper: #fdfbf7;
    --bg-sidebar: #fdfbf7;
    
    --text-main: #2b2b2b;
    --text-secondary: #666666;
    
    --accent-color: #557c55; /* أخضر زيتوني */
    --accent-hover: #416041;
    --accent-light: #e8f0e8;
    
    --border-color: #e0ddd5;
    --highlight-color: rgba(255, 235, 59, 0.5);
    
    --font-main: 'Amiri', serif;
    --font-ui: 'Noto Kufi Arabic', 'Tajawal', sans-serif;
    
    --radius-main: 16px;
    --shadow-soft: 0 10px 30px rgba(85, 124, 85, 0.08);
    --shadow-paper: 0 4px 20px rgba(0,0,0,0.06);
}

.dark-mode {
    --bg-body: #1a1b1e;
    --bg-paper: #25262b;
    --bg-sidebar: #202125;
    
    --text-main: #e0e0e0;
    --text-secondary: #909296;
    
    --accent-color: #74b874;
    --accent-hover: #8ce98c;
    --accent-light: #2c3e2c;
    
    --border-color: #2c2e33;
    --highlight-color: rgba(74, 74, 46, 0.5);
}
```

#### 2.2 تنسيق الورقة (Paper Sheet)

```css
.paper-sheet {
    width: 100%;
    max-width: 900px;
    background-color: var(--bg-paper);
    min-height: 100%;
    padding: 60px 80px;
    box-shadow: var(--shadow-paper);
    border-radius: var(--radius-main);
    border: 1px solid var(--border-color);
    margin: 0 auto;
}

.chapter-text {
    font-family: var(--font-main);
    line-height: 2.1;
    font-size: var(--reader-font-size, 24px);
    text-align: justify;
    color: var(--text-main);
}

/* زخرفة العنوان */
.chapter-header::after {
    content: "❖";
    display: block;
    color: var(--border-color);
    font-size: 1.5rem;
    margin-top: 15px;
    text-align: center;
}
```

---

### 🚀 المرحلة 3: الفهرس الجانبي (2 ساعات)

#### 3.1 Livewire Component للفهرس

```php
// app/Livewire/BookReader/TableOfContents.php

<?php

namespace App\Livewire\BookReader;

use App\Models\Volume;
use App\Models\Chapter;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class TableOfContents extends Component
{
    public int $bookId;
    public int $currentPage;
    public string $search = '';
    public array $expandedVolumes = [];
    public array $expandedChapters = [];
    
    public function mount(int $bookId, int $currentPage = 1): void
    {
        $this->bookId = $bookId;
        $this->currentPage = $currentPage;
    }
    
    public function getTableOfContentsProperty(): array
    {
        return Cache::remember(
            "book_{$this->bookId}_toc",
            now()->addHours(6),
            fn() => $this->buildToc()
        );
    }
    
    private function buildToc(): array
    {
        $volumes = Volume::where('book_id', $this->bookId)
            ->with(['chapters' => fn($q) => $q->whereNull('parent_id')->orderBy('order')])
            ->orderBy('number')
            ->get();
            
        if ($volumes->isEmpty()) {
            return [
                'type' => 'chapters_only',
                'data' => Chapter::where('book_id', $this->bookId)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->get()
            ];
        }
        
        return [
            'type' => 'volumes_with_chapters',
            'data' => $volumes
        ];
    }
    
    public function toggleVolume(int $volumeId): void
    {
        if (in_array($volumeId, $this->expandedVolumes)) {
            $this->expandedVolumes = array_diff($this->expandedVolumes, [$volumeId]);
        } else {
            $this->expandedVolumes[] = $volumeId;
        }
    }
    
    public function goToChapter(int $pageStart): void
    {
        $this->dispatch('navigate-to-page', page: $pageStart);
    }
    
    public function render()
    {
        return view('livewire.book-reader.table-of-contents');
    }
}
```

#### 3.2 View الفهرس

```blade
{{-- resources/views/livewire/book-reader/table-of-contents.blade.php --}}

<div class="toc-container">
    {{-- Search --}}
    <div class="toc-search">
        <input type="text" 
               wire:model.live.debounce.300ms="search" 
               placeholder="ابحث في الفهرس..."
               class="toc-search-input">
        <span class="search-icon">🔍</span>
    </div>
    
    {{-- TOC List --}}
    <div class="toc-scroll">
        @if($this->tableOfContents['type'] === 'volumes_with_chapters')
            @foreach($this->tableOfContents['data'] as $volume)
                <div class="toc-volume" wire:key="vol-{{ $volume->id }}">
                    <button class="toc-volume-title" 
                            wire:click="toggleVolume({{ $volume->id }})">
                        <span class="chevron {{ in_array($volume->id, $expandedVolumes) ? 'expanded' : '' }}">
                            ▼
                        </span>
                        📁 {{ $volume->title ?: 'المجلد ' . $volume->number }}
                    </button>
                    
                    @if(in_array($volume->id, $expandedVolumes))
                        <div class="toc-chapters">
                            @foreach($volume->chapters as $chapter)
                                @include('components.book-reader.chapter-item', ['chapter' => $chapter, 'level' => 0])
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            @foreach($this->tableOfContents['data'] as $chapter)
                @include('components.book-reader.chapter-item', ['chapter' => $chapter, 'level' => 0])
            @endforeach
        @endif
    </div>
</div>
```

---

### 🚀 المرحلة 4: البحث (2 ساعات)

#### 4.1 وظيفة البحث

```php
// إضافة للـ BookReaderPage.php

public string $searchQuery = '';
public array $searchResults = [];
public bool $showSearchResults = false;

public function search(): void
{
    if (strlen($this->searchQuery) < 2) {
        $this->searchResults = [];
        return;
    }
    
    $this->searchResults = Page::where('book_id', $this->bookId)
        ->where('content', 'LIKE', "%{$this->searchQuery}%")
        ->select(['id', 'page_number', 'content', 'chapter_id'])
        ->limit(50)
        ->get()
        ->map(function ($page) {
            return [
                'page_number' => $page->page_number,
                'excerpt' => $this->extractExcerpt($page->content, $this->searchQuery),
                'chapter' => $page->chapter?->title,
            ];
        })
        ->toArray();
        
    $this->showSearchResults = true;
}

private function extractExcerpt(string $content, string $query, int $length = 100): string
{
    $pos = mb_stripos($content, $query);
    if ($pos === false) return mb_substr($content, 0, $length) . '...';
    
    $start = max(0, $pos - 50);
    $excerpt = mb_substr($content, $start, $length);
    
    return ($start > 0 ? '...' : '') . $excerpt . '...';
}
```

---

### 🚀 المرحلة 5: الميزات المتقدمة (3-4 ساعات)

#### 5.1 Keyboard Shortcuts

```javascript
// resources/js/book-reader.js

document.addEventListener('keydown', (e) => {
    // Arrow keys for navigation
    if (e.key === 'ArrowRight') {
        Livewire.dispatch('previous-page');
    }
    if (e.key === 'ArrowLeft') {
        Livewire.dispatch('next-page');
    }
    
    // Ctrl+F for search
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('search-input')?.focus();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        Livewire.dispatch('close-modals');
    }
});
```

#### 5.2 Highlight Search Terms

```javascript
function highlightSearchTerms(query) {
    if (!query) return;
    
    const content = document.getElementById('page-content');
    const terms = query.split(/\s+/).filter(t => t.length > 0);
    const pattern = new RegExp('(' + terms.map(t => 
        t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
    ).join('|') + ')', 'gi');
    
    const walker = document.createTreeWalker(content, NodeFilter.SHOW_TEXT);
    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    
    nodes.forEach(textNode => {
        if (pattern.test(textNode.nodeValue)) {
            const span = document.createElement('span');
            span.innerHTML = textNode.nodeValue.replace(pattern, '<mark>$1</mark>');
            textNode.parentNode.replaceChild(span, textNode);
        }
    });
}
```

#### 5.3 Progress Tracking (حفظ التقدم)

```php
// app/Models/UserReadingProgress.php (جديد)

Schema::create('user_reading_progress', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('book_id')->constrained()->cascadeOnDelete();
    $table->integer('last_page')->default(1);
    $table->integer('total_time_seconds')->default(0);
    $table->timestamps();
    
    $table->unique(['user_id', 'book_id']);
});
```

---

## 🎯 ملخص الميزات النهائية

| الميزة | الأولوية | الوقت |
|--------|----------|-------|
| عرض الصفحة الأساسي | 🔴 عالية | ساعتين |
| التنقل بين الصفحات | 🔴 عالية | ساعة |
| الفهرس الجانبي | 🔴 عالية | ساعتين |
| تغيير حجم الخط | 🟡 متوسطة | 30 دقيقة |
| الوضع الليلي | 🟡 متوسطة | 30 دقيقة |
| البحث في الكتاب | 🟡 متوسطة | ساعتين |
| اختصارات لوحة المفاتيح | 🟢 منخفضة | 30 دقيقة |
| حفظ تقدم القراءة | 🟢 منخفضة | ساعة |
| تمييز نتائج البحث | 🟢 منخفضة | 30 دقيقة |
| ملء الشاشة | 🟢 منخفضة | 30 دقيقة |

**المجموع الإجمالي: ~10-12 ساعة عمل**

---

## ✅ Checklist التنفيذ

### المرحلة 1 - الأساس:
- [ ] إنشاء `BookReaderPage.php` Livewire Component
- [ ] إنشاء View الأساسية `book-reader-page.blade.php`
- [ ] إضافة Route في `web.php`
- [ ] إنشاء Layout خاص `layouts/reader.blade.php`

### المرحلة 2 - التصميم:
- [ ] إنشاء `book-reader.css` بالمتغيرات
- [ ] تصميم الـ Toolbar
- [ ] تصميم Paper Sheet
- [ ] تصميم Navigation Bar
- [ ] الوضع الليلي (Dark Mode)

### المرحلة 3 - الفهرس:
- [ ] إنشاء `TableOfContents.php` Component
- [ ] تصميم شجرة الفهرس
- [ ] البحث في الفهرس
- [ ] الانتقال للفصول

### المرحلة 4 - البحث:
- [ ] وظيفة البحث في الصفحات
- [ ] عرض نتائج البحث
- [ ] تمييز الكلمات

### المرحلة 5 - المتقدمة:
- [ ] اختصارات لوحة المفاتيح
- [ ] حفظ تقدم القراءة
- [ ] ملء الشاشة
- [ ] المشاركة

---

## 🔗 المراجع من المشروع القديم

- `BMS_v1/app/Livewire/Reader/BookReader.php` - المنطق الكامل
- `BMS_v1/resources/views/pages/book-read.blade.php` - الـ View القديمة
- `BMS_v1/resources/views/partials/chapter-tree.blade.php` - عرض الفصول

---

**تاريخ الإنشاء**: 8 ديسمبر 2025
**الحالة**: جاهز للتنفيذ ✅
