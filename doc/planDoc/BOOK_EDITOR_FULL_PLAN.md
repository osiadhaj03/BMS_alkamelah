# 📖 خطة محرر الكتب (Book Editor) - الخطة الكاملة

## 🎯 نظرة عامة

محرر كتب متكامل شبيه بـ Microsoft Word مدمج داخل نظام إدارة الكتب (BMS)، يتيح تحرير الكتب بشكل احترافي مع دعم كامل للغة العربية والـ RTL.

---

## 📊 الهيكل الحالي لقاعدة البيانات

```
Book ─────► Volume ─────► Chapter (nested) ─────► Page
  │            │              │                      │
  │            │              ├─ parent_id          ├─ content
  │            │              ├─ level              ├─ html_content
  │            │              └─ order              └─ page_number
  │            │
  └────────────┴──────────────────────────────────► Page
```

### الجداول الموجودة:
- **books**: الكتب الرئيسية
- **volumes**: المجلدات (مرتبطة بالكتاب)
- **chapters**: الفصول (مرتبطة بالمجلد والكتاب، تدعم التداخل via parent_id)
- **pages**: الصفحات (تحتوي content و html_content)

---

## 🖥️ التصميم المرئي للمحرر

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  📖 محرر الكتب - [اسم الكتاب]                              [حفظ] [إغلاق]  │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌──────────────────┬──────────────────┐                                    │
│  │ المجلد: [المجلد 1 ▼] │ الفصل: [باب الطهارة ▼]│                           │
│  └──────────────────┴──────────────────┘                                    │
├───────────────────────────────────────────────────────────────┬─────────────┤
│  ┌───────────────────────────────────────────────────────┐   │ 📑 الفهرس   │
│  │ [B] [I] [U] [S] │ [H1][H2][H3] │ [•][1.] │ [🔗][📷] │   │             │
│  ├───────────────────────────────────────────────────────┤   │ 📁 المجلد 1 │
│  │                                                       │   │  ├─ باب 1   │
│  │                                                       │   │  │  ├─ فصل 1│
│  │              ╔═══════════════════════╗                │   │  │  └─ فصل 2│
│  │              ║                       ║                │   │  └─ باب 2   │
│  │              ║    محتوى الصفحة       ║                │   │ 📁 المجلد 2 │
│  │              ║                       ║                │   │  └─ ...     │
│  │              ║                       ║                │   │             │
│  │              ╚═══════════════════════╝                │   │             │
│  │                                                       │   │             │
│  └───────────────────────────────────────────────────────┘   │             │
│                                                               │             │
│  [◀ السابق]  صفحة [150] من 10,500  [التالي ▶]  [+ أضف صفحة]  │             │
└───────────────────────────────────────────────────────────────┴─────────────┘
```

---

## 🔧 المتطلبات التقنية

### Frontend
- **TipTap Editor v2**: محرر Rich Text مبني على ProseMirror
  - دعم كامل للـ RTL
  - قابل للتخصيص بالكامل
  - خفيف وسريع
  - يتكامل مع Alpine.js و Livewire

### Backend
- **Laravel 11** + **Filament v4**
- **Livewire 3** للتفاعلية
- **API Routes** للتحميل الذكي

---

## 📅 المراحل التفصيلية

---

### 🚀 المرحلة 1: الأساس (MVP)
**المدة المقدرة: أسبوع واحد**

#### 1.1 إنشاء Custom Filament Page
```
الملفات المطلوبة:
├── app/Filament/Pages/BookEditor.php
├── resources/views/filament/pages/book-editor.blade.php
└── routes: /admin/books/{book}/editor
```

#### 1.2 Livewire Component للمحرر
```php
// المكونات:
├── app/Livewire/BookEditor/Editor.php          // المحرر الرئيسي
├── app/Livewire/BookEditor/PageNavigator.php   // التنقل بين الصفحات
└── app/Livewire/BookEditor/AutoSave.php        // الحفظ التلقائي
```

#### 1.3 تثبيت وتكوين TipTap
```bash
npm install @tiptap/core @tiptap/starter-kit @tiptap/extension-text-align
npm install @tiptap/extension-underline @tiptap/extension-link @tiptap/extension-image
```

#### 1.4 API Endpoints
```
GET  /api/books/{book}/pages/{page_number}     → تحميل صفحة واحدة
PUT  /api/books/{book}/pages/{page_number}     → حفظ صفحة
GET  /api/books/{book}/toc                      → الفهرس (metadata فقط)
POST /api/books/{book}/pages                    → إضافة صفحة جديدة
```

#### 1.5 حل مشكلة آلاف الصفحات
```javascript
// استراتيجية التحميل الذكي:

1. تحميل صفحة واحدة فقط عند الدخول
2. Prefetch للصفحات المجاورة (±2 صفحات)
3. Cache في الـ Frontend (Map/Object)
4. تحميل الفهرس خفيف (عناوين فقط، بدون محتوى)

// مثال:
const pageCache = new Map();
const PREFETCH_RANGE = 2;

async function loadPage(pageNumber) {
    if (pageCache.has(pageNumber)) {
        return pageCache.get(pageNumber);
    }
    const page = await fetch(`/api/books/${bookId}/pages/${pageNumber}`);
    pageCache.set(pageNumber, page);
    
    // Prefetch
    for (let i = 1; i <= PREFETCH_RANGE; i++) {
        prefetch(pageNumber - i);
        prefetch(pageNumber + i);
    }
    return page;
}
```

#### 1.6 الحفظ التلقائي
```javascript
// Debounced Auto-save كل 3 ثواني بعد التوقف عن الكتابة
let saveTimeout;
editor.on('update', () => {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => {
        savePage(currentPageNumber, editor.getHTML());
    }, 3000);
});
```

#### المخرجات:
- [x] صفحة محرر تعمل
- [x] تحميل صفحة واحدة
- [x] حفظ يدوي وتلقائي
- [x] تنقل بين الصفحات (سابق/تالي)

---

### 🚀 المرحلة 2: الفهرس والتنقل المتقدم
**المدة المقدرة: أسبوع واحد**

#### 2.1 Sidebar الفهرس (Table of Contents)
```php
// Livewire Component
app/Livewire/BookEditor/TableOfContents.php

// البيانات المطلوبة (خفيفة):
$toc = Volume::where('book_id', $bookId)
    ->with(['chapters' => function($q) {
        $q->select('id', 'volume_id', 'title', 'parent_id', 'page_start')
          ->orderBy('order');
    }])
    ->select('id', 'book_id', 'number', 'title')
    ->get();
```

#### 2.2 شجرة الفهرس (Tree View)
```blade
<!-- Alpine.js Tree Component -->
<div x-data="{ expanded: {} }">
    @foreach($volumes as $volume)
        <div class="volume">
            <button @click="expanded[{{ $volume->id }}] = !expanded[{{ $volume->id }}]">
                📁 {{ $volume->display_name }}
            </button>
            <div x-show="expanded[{{ $volume->id }}]">
                @foreach($volume->rootChapters as $chapter)
                    @include('partials.chapter-tree', ['chapter' => $chapter])
                @endforeach
            </div>
        </div>
    @endforeach
</div>
```

#### 2.3 فلترة الصفحات حسب المجلد/الفصل
```php
// عند اختيار مجلد أو فصل معين
public function filterByVolume($volumeId)
{
    $this->currentVolumeId = $volumeId;
    $this->currentChapterId = null;
    $this->loadFirstPageInVolume($volumeId);
}

public function filterByChapter($chapterId)
{
    $this->currentChapterId = $chapterId;
    $this->loadFirstPageInChapter($chapterId);
}
```

#### 2.4 إضافة صفحة جديدة
```php
public function addPage($afterPageNumber = null)
{
    $newPageNumber = $afterPageNumber ? $afterPageNumber + 1 : $this->getLastPageNumber() + 1;
    
    // إعادة ترقيم الصفحات التالية
    Page::where('book_id', $this->bookId)
        ->where('page_number', '>=', $newPageNumber)
        ->increment('page_number');
    
    // إنشاء الصفحة الجديدة
    $page = Page::create([
        'book_id' => $this->bookId,
        'volume_id' => $this->currentVolumeId,
        'chapter_id' => $this->currentChapterId,
        'page_number' => $newPageNumber,
        'content' => '',
    ]);
    
    $this->goToPage($newPageNumber);
}
```

#### المخرجات:
- [x] فهرس جانبي تفاعلي
- [x] شجرة المجلدات والفصول
- [x] فلترة حسب المجلد/الفصل
- [x] إضافة صفحات جديدة
- [x] الانتقال السريع من الفهرس

---

### 🚀 المرحلة 3: تجربة Word الكاملة
**المدة المقدرة: أسبوع واحد**

#### 3.1 Toolbar متكامل
```javascript
// TipTap Extensions
const editor = new Editor({
    extensions: [
        StarterKit,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Underline,
        Link,
        Image,
        Table.configure({ resizable: true }),
        TableRow,
        TableCell,
        TableHeader,
        Highlight,
        TextStyle,
        Color,
        FontFamily,
        FontSize,
    ],
});
```

#### 3.2 أزرار Toolbar
```html
<div class="toolbar">
    <!-- تنسيق النص -->
    <button @click="editor.chain().focus().toggleBold().run()">B</button>
    <button @click="editor.chain().focus().toggleItalic().run()">I</button>
    <button @click="editor.chain().focus().toggleUnderline().run()">U</button>
    <button @click="editor.chain().focus().toggleStrike().run()">S</button>
    
    <!-- العناوين -->
    <button @click="editor.chain().focus().toggleHeading({ level: 1 }).run()">H1</button>
    <button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">H2</button>
    <button @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">H3</button>
    
    <!-- القوائم -->
    <button @click="editor.chain().focus().toggleBulletList().run()">•</button>
    <button @click="editor.chain().focus().toggleOrderedList().run()">1.</button>
    
    <!-- المحاذاة -->
    <button @click="editor.chain().focus().setTextAlign('right').run()">→</button>
    <button @click="editor.chain().focus().setTextAlign('center').run()">↔</button>
    <button @click="editor.chain().focus().setTextAlign('left').run()">←</button>
    
    <!-- الجداول -->
    <button @click="insertTable()">⊞</button>
    
    <!-- الصور -->
    <button @click="insertImage()">🖼</button>
</div>
```

#### 3.3 البحث والاستبدال
```php
// بحث في الصفحة الحالية (Frontend)
editor.commands.find('كلمة البحث');

// بحث في الكتاب كامل (Backend)
public function searchInBook($query)
{
    return Page::where('book_id', $this->bookId)
        ->where(function($q) use ($query) {
            $q->where('content', 'LIKE', "%{$query}%")
              ->orWhere('html_content', 'LIKE', "%{$query}%");
        })
        ->select('id', 'page_number', 'chapter_id')
        ->limit(100)
        ->get()
        ->map(function($page) use ($query) {
            // استخراج snippet حول الكلمة
            $page->snippet = $this->extractSnippet($page->content, $query);
            return $page;
        });
}
```

#### 3.4 اختصارات لوحة المفاتيح
```javascript
// TipTap يدعم الاختصارات افتراضياً:
// Ctrl+B = Bold
// Ctrl+I = Italic
// Ctrl+U = Underline
// Ctrl+Z = Undo
// Ctrl+Y = Redo

// اختصارات مخصصة:
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        savePage();
    }
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        openSearchDialog();
    }
});
```

#### المخرجات:
- [x] Toolbar كامل شبيه بـ Word
- [x] دعم الجداول
- [x] دعم الصور
- [x] البحث والاستبدال
- [x] اختصارات لوحة المفاتيح

---

### 🚀 المرحلة 4: ميزات متقدمة (اختياري)
**المدة المقدرة: أسبوعين**

#### 4.1 تاريخ التعديلات (Version History)
```php
// جدول جديد: page_versions
Schema::create('page_versions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('page_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained();
    $table->longText('content');
    $table->timestamp('created_at');
});

// حفظ نسخة عند كل تعديل مهم
public function saveVersion($pageId, $content)
{
    PageVersion::create([
        'page_id' => $pageId,
        'user_id' => auth()->id(),
        'content' => $content,
    ]);
}
```

#### 4.2 التعليقات والملاحظات
```php
// جدول: page_comments
Schema::create('page_comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('page_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained();
    $table->text('comment');
    $table->json('position'); // موقع التعليق في النص
    $table->boolean('is_resolved')->default(false);
    $table->timestamps();
});
```

#### 4.3 تصدير الكتاب
```php
// تصدير PDF
public function exportToPdf($bookId)
{
    $book = Book::with(['volumes.chapters', 'pages'])->find($bookId);
    $pdf = PDF::loadView('exports.book-pdf', compact('book'));
    return $pdf->download("{$book->title}.pdf");
}

// تصدير DOCX
public function exportToDocx($bookId)
{
    // استخدام مكتبة PhpWord
}

// تصدير EPUB
public function exportToEpub($bookId)
{
    // استخدام مكتبة PHPePub
}
```

#### 4.4 التعاون المباشر (Real-time)
```javascript
// باستخدام Laravel Echo + Pusher/Soketi
Echo.private(`book.${bookId}`)
    .listen('PageUpdated', (e) => {
        if (e.pageNumber === currentPage && e.userId !== currentUserId) {
            // تحديث المحتوى أو إظهار تنبيه
            showNotification('تم تحديث الصفحة بواسطة مستخدم آخر');
        }
    });
```

---

## 📁 هيكل الملفات النهائي

```
app/
├── Filament/
│   └── Pages/
│       └── BookEditor.php
├── Livewire/
│   └── BookEditor/
│       ├── Editor.php
│       ├── PageNavigator.php
│       ├── TableOfContents.php
│       ├── Toolbar.php
│       └── SearchReplace.php
├── Http/
│   └── Controllers/
│       └── Api/
│           └── BookEditorController.php
└── Services/
    └── BookEditorService.php

resources/
├── views/
│   └── filament/
│       └── pages/
│           └── book-editor.blade.php
│   └── livewire/
│       └── book-editor/
│           ├── editor.blade.php
│           ├── page-navigator.blade.php
│           ├── table-of-contents.blade.php
│           ├── toolbar.blade.php
│           └── search-replace.blade.php
├── js/
│   └── book-editor/
│       ├── tiptap-config.js
│       ├── page-cache.js
│       └── keyboard-shortcuts.js
└── css/
    └── book-editor.css

routes/
└── api.php  (endpoints للمحرر)
```

---

## 🔒 اعتبارات الأمان

1. **التحقق من الصلاحيات**: فقط من له صلاحية تعديل الكتاب يمكنه الوصول للمحرر
2. **CSRF Protection**: لجميع طلبات الـ API
3. **Sanitization**: تنظيف الـ HTML قبل الحفظ لمنع XSS
4. **Rate Limiting**: للـ API لمنع الإساءة

---

## 📈 اعتبارات الأداء

1. **Lazy Loading**: تحميل صفحة واحدة فقط
2. **Prefetching**: تحميل الصفحات المجاورة في الخلفية
3. **Debounced Saving**: الحفظ بعد توقف الكتابة
4. **Indexed Queries**: فهرسة الحقول المستخدمة في البحث
5. **Caching**: تخزين الفهرس مؤقتاً

---

## ✅ قائمة التحقق للإطلاق

### المرحلة 1:
- [ ] إنشاء Filament Page
- [ ] تثبيت TipTap
- [ ] إنشاء API endpoints
- [ ] تحميل صفحة واحدة
- [ ] حفظ تلقائي
- [ ] تنقل بين الصفحات

### المرحلة 2:
- [ ] فهرس جانبي
- [ ] شجرة المجلدات/الفصول
- [ ] فلترة حسب المجلد/الفصل
- [ ] إضافة صفحات

### المرحلة 3:
- [ ] Toolbar كامل
- [ ] دعم الجداول
- [ ] دعم الصور
- [ ] البحث والاستبدال
- [ ] اختصارات لوحة المفاتيح

### المرحلة 4 (اختياري):
- [ ] تاريخ التعديلات
- [ ] التعليقات
- [ ] التصدير
- [ ] التعاون المباشر

---

## 📞 الدعم والمراجع

- [TipTap Documentation](https://tiptap.dev/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/start-here)

---

**تاريخ الإنشاء**: 5 ديسمبر 2025
**آخر تحديث**: 5 ديسمبر 2025
**الإصدار**: 1.0
