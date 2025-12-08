# 📖 محرر الكتب - الخطة المختصرة

## 🎯 الهدف
محرر Rich Text لتحرير صفحات الكتب، مع فهرس جانبي وتنقل ذكي.

---

## 🏗️ ما نحتاجه (الحد الأدنى)

### 1. الملفات الأساسية
```
app/Filament/Pages/BookEditor.php           ← صفحة المحرر
app/Livewire/BookEditor.php                 ← Livewire Component
resources/views/livewire/book-editor.blade.php
routes/api.php                              ← API endpoints
```

### 2. المكتبات المطلوبة
```bash
npm install @tiptap/core @tiptap/starter-kit @tiptap/extension-text-align @tiptap/extension-underline
```

---

## 📅 خطوات التنفيذ

### الخطوة 1: إنشاء API (ساعتين)
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/books/{book}/pages/{pageNumber}', [BookEditorController::class, 'getPage']);
    Route::put('/books/{book}/pages/{pageNumber}', [BookEditorController::class, 'savePage']);
    Route::get('/books/{book}/toc', [BookEditorController::class, 'getTableOfContents']);
});
```

### الخطوة 2: Controller بسيط (ساعة)
```php
// app/Http/Controllers/Api/BookEditorController.php
class BookEditorController extends Controller
{
    public function getPage(Book $book, int $pageNumber)
    {
        $page = $book->pages()->where('page_number', $pageNumber)->first();
        $totalPages = $book->pages()->count();
        
        return response()->json([
            'page' => $page,
            'total_pages' => $totalPages,
            'has_next' => $pageNumber < $totalPages,
            'has_previous' => $pageNumber > 1,
        ]);
    }

    public function savePage(Request $request, Book $book, int $pageNumber)
    {
        $page = $book->pages()->where('page_number', $pageNumber)->first();
        $page->update(['html_content' => $request->content]);
        return response()->json(['success' => true]);
    }

    public function getTableOfContents(Book $book)
    {
        return $book->volumes()
            ->with('rootChapters.children')
            ->select('id', 'number', 'title')
            ->get();
    }
}
```

### الخطوة 3: Filament Page (ساعتين)
```php
// app/Filament/Pages/BookEditor.php
class BookEditor extends Page
{
    protected static string $view = 'filament.pages.book-editor';
    protected static bool $shouldRegisterNavigation = false;
    
    public Book $book;
    
    public function mount(Book $book): void
    {
        $this->book = $book;
    }
    
    public static function getUrl(array $parameters = [], bool $isAbsolute = true): string
    {
        return route('filament.admin.pages.book-editor', $parameters, $isAbsolute);
    }
}
```

### الخطوة 4: Blade View + TipTap (3-4 ساعات)
```blade
{{-- resources/views/filament/pages/book-editor.blade.php --}}
<x-filament::page>
    <div class="flex gap-4" x-data="bookEditor({{ $book->id }})">
        
        {{-- المحرر الرئيسي --}}
        <div class="flex-1">
            {{-- Toolbar --}}
            <div class="toolbar bg-gray-100 p-2 rounded-t flex gap-2">
                <button @click="editor.chain().focus().toggleBold().run()">B</button>
                <button @click="editor.chain().focus().toggleItalic().run()">I</button>
                <button @click="editor.chain().focus().toggleUnderline().run()">U</button>
                <!-- المزيد من الأزرار -->
            </div>
            
            {{-- المحرر --}}
            <div id="editor" class="border p-4 min-h-[500px]"></div>
            
            {{-- التنقل --}}
            <div class="flex justify-between mt-4">
                <button @click="prevPage()" :disabled="!hasPrevious">◀ السابق</button>
                <span>صفحة <input type="number" x-model="currentPage" @change="goToPage()"> من <span x-text="totalPages"></span></span>
                <button @click="nextPage()" :disabled="!hasNext">التالي ▶</button>
            </div>
        </div>
        
        {{-- الفهرس --}}
        <div class="w-64 bg-gray-50 p-4 rounded">
            <h3 class="font-bold mb-4">📑 الفهرس</h3>
            <template x-for="volume in toc">
                <div class="mb-2">
                    <button @click="volume.expanded = !volume.expanded" class="font-bold">
                        📁 <span x-text="volume.title"></span>
                    </button>
                    <div x-show="volume.expanded" class="mr-4">
                        <template x-for="chapter in volume.chapters">
                            <div @click="goToChapter(chapter.page_start)" class="cursor-pointer hover:bg-gray-200 p-1">
                                <span x-text="chapter.title"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
    
    @push('scripts')
    <script type="module">
        import { Editor } from '@tiptap/core'
        import StarterKit from '@tiptap/starter-kit'
        
        window.bookEditor = (bookId) => ({
            bookId,
            currentPage: 1,
            totalPages: 0,
            hasNext: false,
            hasPrevious: false,
            toc: [],
            editor: null,
            
            init() {
                this.editor = new Editor({
                    element: document.querySelector('#editor'),
                    extensions: [StarterKit],
                    content: '',
                    onUpdate: () => this.autoSave(),
                });
                this.loadPage(1);
                this.loadToc();
            },
            
            async loadPage(pageNumber) {
                const res = await fetch(`/api/books/${this.bookId}/pages/${pageNumber}`);
                const data = await res.json();
                this.editor.commands.setContent(data.page?.html_content || '');
                this.currentPage = pageNumber;
                this.totalPages = data.total_pages;
                this.hasNext = data.has_next;
                this.hasPrevious = data.has_previous;
            },
            
            async loadToc() {
                const res = await fetch(`/api/books/${this.bookId}/toc`);
                this.toc = await res.json();
            },
            
            prevPage() { if (this.hasPrevious) this.loadPage(this.currentPage - 1); },
            nextPage() { if (this.hasNext) this.loadPage(this.currentPage + 1); },
            goToPage() { this.loadPage(this.currentPage); },
            goToChapter(pageStart) { this.loadPage(pageStart); },
            
            autoSave: _.debounce(async function() {
                await fetch(`/api/books/${this.bookId}/pages/${this.currentPage}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ content: this.editor.getHTML() })
                });
            }, 3000),
        });
    </script>
    @endpush
</x-filament::page>
```

---

## ⚡ ملخص سريع

| المهمة | الوقت | الأولوية |
|--------|-------|----------|
| API endpoints | ساعتين | 🔴 عالية |
| Controller | ساعة | 🔴 عالية |
| Filament Page | ساعتين | 🔴 عالية |
| TipTap + View | 4 ساعات | 🔴 عالية |
| الفهرس الجانبي | ساعتين | 🟡 متوسطة |
| التنسيق والتحسين | ساعتين | 🟢 منخفضة |

**المجموع: ~13 ساعة عمل للنسخة الأولى**

---

## 🚀 البدء السريع

```bash
# 1. تثبيت TipTap
npm install @tiptap/core @tiptap/starter-kit

# 2. إنشاء الملفات
php artisan make:controller Api/BookEditorController
php artisan make:filament-page BookEditor

# 3. تشغيل
npm run dev
php artisan serve
```

---

## ✅ Checklist

- [ ] إنشاء `BookEditorController`
- [ ] إضافة routes في `api.php`
- [ ] إنشاء `BookEditor` Filament Page
- [ ] تثبيت TipTap
- [ ] إنشاء View مع Alpine.js
- [ ] اختبار التحميل والحفظ
- [ ] إضافة الفهرس الجانبي

---

**الوقت المتوقع للانتهاء: يوم عمل واحد** 🎯
