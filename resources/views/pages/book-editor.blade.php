<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تحرير: {{ $book->title }} | المكتبة الكاملة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Arabic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS Variables -->
    <style>
        :root {
            --bg-body: #ffffff; 
            --bg-paper: #fdfbf7;
            --bg-sidebar: #ffffff;
            --bg-hover: #f5f5f5;
            --text-main: #2b2b2b;
            --text-secondary: #666666;
            --text-muted: #9ca3af;
            --accent-color: #dc2626; /* Red for edit mode */
            --accent-hover: #b91c1c;
            --accent-light: #fee2e2;
            --border-color: #e5e7eb;
            --highlight-color: #ffeb3b80;
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-paper: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-dropdown: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-main: 'Amiri', serif;       
            --font-ui: 'Tajawal', sans-serif;  
            --radius-main: 16px;
        }
        
        body { 
            font-family: var(--font-ui); 
            background-color: var(--bg-body);
            color: var(--text-main);
        }
        
        .bg-red-600 { background-color: #dc2626; }
        .text-red-600 { color: #dc2626; }
        .text-red-700 { color: #b91c1c; }
        .bg-red-700 { background-color: #b91c1c; }
        .border-red-600 { border-color: #dc2626; }
        .hover\:bg-red-700:hover { background-color: #b91c1c; }
        
        /* Alpine.js x-cloak - Hide elements until Alpine initializes */
        [x-cloak] { display: none !important; }
        
        /* Content Editor Styles */
        .content-editor {
            min-height: 400px;
            font-family: var(--font-main);
            line-height: 2;
            padding: 1.5rem;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            background-color: var(--bg-paper);
            transition: border-color 0.2s;
        }
        
        .content-editor:focus {
            outline: none;
            border-color: var(--accent-color);
        }
    </style>
</head>
<body class="min-h-screen" style="background-color: var(--bg-body);">
    <div dir="rtl" x-data="bookEditor()">

        <!-- Edit Mode Banner -->
        <div class="bg-red-600 text-white py-2 px-4 text-center text-sm font-bold" style="font-family: var(--font-ui);">
            <span>🔧 وضع التحرير - أنت تقوم بتعديل الكتاب</span>
            <a href="{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $currentPageNum]) }}" 
               class="mr-4 underline hover:no-underline">
                العودة لوضع القراءة
            </a>
        </div>
        
        <!-- Main Site Header -->
        <x-layout.header />
        
        <!-- Book Editor Header -->
        <x-book-editor.header-editor 
            :book="$book ?? null" 
            :currentPageNum="$currentPageNum ?? 1"
            :totalPages="$totalPages ?? 1"
        />
        
        <!-- Main Layout -->
        <div class="flex" style="padding-top: 0px;">
            <!-- Editable TOC Sidebar -->
            <x-book-editor.toc-editor 
                :chapters="$chapters ?? collect()" 
                :book="$book ?? null"
                :currentPage="$currentPage ?? null"
            />
            
            <!-- Main Content Editor Component -->
            <x-book-editor.content-editor 
                :currentPage="$currentPage ?? null"
                :pages="$pages ?? collect()"
                :book="$book ?? null"
                :currentPageNum="$currentPageNum ?? 1"
                :totalPages="$totalPages ?? 1"
                :nextPage="$nextPage ?? null"
                :previousPage="$previousPage ?? null"
            />
        </div>
        
        <!-- Insert Page Options Modal -->
        <div x-show="$store.insertPage && $store.insertPage.showModal" 
             x-cloak
             @click.self="$store.insertPage.closeModal()"
             style="z-index: 9999 !important;"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6" @click.stop>
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900" style="font-family: var(--font-ui);" x-text="$store.insertPage.title"></h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500" style="font-family: var(--font-ui);">
                            سيتم إدراج صفحة جديدة وإعادة الترتيب التسلسلي لباقي الصفحات تلقائياً.
                        </p>
                    </div>
                </div>

                <div class="mb-6 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <label class="flex items-start space-x-3 space-x-reverse cursor-pointer">
                        <input type="checkbox" x-model="$store.insertPage.renumberOriginal" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                        <span class="text-sm text-gray-700" style="font-family: var(--font-ui);">
                            <span class="font-bold block mb-1">إعادة ترقيم الصفحات المطبوعة </span>
                            <span class="text-xs text-gray-500">قم بتفعيل هذا الخيار إذا كنت تريد إزاحة الأرقام المطبوعة (في ترويسة الكتاب) لباقي الصفحات (+1).</span>
                        </span>
                    </label>
                </div>
                
                <div class="flex gap-3 justify-center">
                    <button type="button" 
                            @click="$store.insertPage.closeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex-1"
                            style="font-family: var(--font-ui);">
                        إلغاء
                    </button>
                    <button type="button"
                            @click="$store.insertPage.confirmInsert()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex-1"
                            style="font-family: var(--font-ui);">
                        تأكيد الإدراج
                    </button>
                </div>
            </div>
        </div>

        <!-- TOC Add/Edit Chapter Modal (Outside sidebar for proper z-index) -->
        <div x-show="$store.tocEditor && $store.tocEditor.showModal" 
             x-cloak
             @click.self="$store.tocEditor.closeModal()"
             style="z-index: 9999 !important;"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h3 class="text-xl font-bold mb-4" style="font-family: var(--font-ui);" x-text="$store.tocEditor.modalTitle"></h3>
                
                <form @submit.prevent="$store.tocEditor.saveChapter()">
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                            عنوان الفصل
                        </label>
                        <input type="text" 
                               x-model="$store.tocEditor.formData.title"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                               style="font-family: var(--font-ui);">
                    </div>
                    
                    <!-- Parent Chapter -->
                    <div class="mb-4" x-show="!$store.tocEditor.editMode">
                        <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                            الفصل الأب (اختياري)
                        </label>
                        <select x-model="$store.tocEditor.formData.parent_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                style="font-family: var(--font-ui);">
                            <option value="">فصل رئيسي</option>
                            @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}">{{ $chapter->title }}</option>
                                @if($chapter->children && $chapter->children->count() > 0)
                                    @foreach($chapter->children as $child)
                                        <option value="{{ $child->id }}">— {{ $child->title }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Page Range -->
                    <div class="mb-4 grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                                صفحة البداية
                            </label>
                            <input type="number" 
                                   x-model="$store.tocEditor.formData.page_start"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                                صفحة النهاية
                            </label>
                            <input type="number" 
                                   x-model="$store.tocEditor.formData.page_end"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex gap-3 justify-end">
                        <button type="button" 
                                @click="$store.tocEditor.closeModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                                style="font-family: var(--font-ui);">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                style="font-family: var(--font-ui);">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Save Status Notification -->
        <div x-show="saveStatus !== ''" 
             x-transition
             :class="{
                 'bg-green-500': saveStatus === 'success',
                 'bg-red-500': saveStatus === 'error',
                 'bg-yellow-500': saveStatus === 'saving'
             }"
             class="fixed bottom-4 left-4 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             style="font-family: var(--font-ui);">
            <span x-text="saveMessage"></span>
        </div>
    </div>

<script>
        // TOC Editor Store (Global state for modal)
        document.addEventListener('alpine:init', () => {
            Alpine.store('tocEditor', {
                showModal: false,
                editMode: false,
                modalTitle: 'إضافة فصل جديد',
                formData: {
                    id: null,
                    title: '',
                    parent_id: '',
                    page_start: '',
                    page_end: ''
                },
                
                openAddModal(parentId = null) {
                    this.editMode = false;
                    this.modalTitle = 'إضافة فصل جديد';
                    this.formData = {
                        id: null,
                        title: '',
                        parent_id: parentId || '',
                        page_start: '',
                        page_end: ''
                    };
                    this.showModal = true;
                },
                
                openEditModal(chapter) {
                    this.editMode = true;
                    this.modalTitle = 'تعديل الفصل';
                    this.formData = {
                        id: chapter.id,
                        title: chapter.title,
                        parent_id: chapter.parent_id || '',
                        page_start: chapter.page_start || '',
                        page_end: chapter.page_end || ''
                    };
                    this.showModal = true;
                },
                
                closeModal() {
                    this.showModal = false;
                },
                
                async saveChapter() {
                    const bookId = {{ $book->id ?? 0 }};
                    const url = this.editMode 
                        ? `/editBook/${bookId}/chapters/${this.formData.id}`
                        : `/editBook/${bookId}/chapters`;
                    const method = 'POST';
                    
                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.closeModal();
                            window.location.reload();
                        } else {
                            alert('خطأ: ' + (data.message || 'فشل الحفظ'));
                        }
                    } catch (error) {
                        alert('خطأ: ' + error.message);
                    }
                }
            });
        });
        
        // Insert Page Store
        document.addEventListener('alpine:init', () => {
            Alpine.store('insertPage', {
                showModal: false,
                type: 'after', // 'before' or 'after'
                title: '',
                renumberOriginal: false,
                bookId: {{ $book->id ?? 0 }},
                pageNumber: {{ $currentPageNum ?? 1 }},
                
                openModal(type) {
                    this.type = type;
                    this.title = type === 'before' 
                        ? 'إدراج صفحة جديدة قبل الصفحة الحالية' 
                        : 'إدراج صفحة جديدة بعد الصفحة الحالية';
                    this.renumberOriginal = false; // Reset to false by default
                    this.showModal = true;
                },
                
                closeModal() {
                    this.showModal = false;
                },
                
                async confirmInsert() {
                    const url = this.type === 'before'
                        ? `/editBook/${this.bookId}/page/${this.pageNumber}/insert-before`
                        : `/editBook/${this.bookId}/page/${this.pageNumber}/insert-after`;
                        
                    const newPageNumber = this.type === 'before' ? this.pageNumber : this.pageNumber + 1;
                    
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                renumber_original: this.renumberOriginal
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            window.location.href = `/editBook/${this.bookId}/${newPageNumber}`;
                        } else {
                            alert('خطأ: ' + (data.message || 'فشل إدراج الصفحة'));
                        }
                    } catch (error) {
                        alert('خطأ: ' + error.message);
                    }
                    
                    this.closeModal();
                }
            });
        });
        
        function bookEditor() {
            return {
                saveStatus: '',
                saveMessage: '',
                isModified: false,
                
                async saveContent() {
                    let content = '';
                    let htmlContent = '';
                    
                    // Get data from CKEditor if available
                    if (window.editor) {
                        htmlContent = window.editor.getData();
                        // Create a temporary element to strip HTML tags for plain content
                        const tmp = document.createElement("DIV");
                        tmp.innerHTML = htmlContent;
                        content = tmp.textContent || tmp.innerText || "";
                    } else {
                        // Fallback to old method (safety check)
                        const contentEl = document.getElementById('content-editor');
                        if (contentEl) {
                           content = contentEl.innerText;
                           htmlContent = contentEl.innerHTML;
                        } else {
                            // If neither editor nor element exists, probably nothing to save or different page state
                            return; 
                        }
                    }
                    
                    this.saveStatus = 'saving';
                    this.saveMessage = 'جاري الحفظ...';
                    
                    try {
                        const response = await fetch('{{ route("book.updatePage", ["bookId" => $book->id, "pageNumber" => $currentPageNum]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                content: content, // Plain text for search indexing
                                html_content: htmlContent // Full HTML for display
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.saveStatus = 'success';
                            this.saveMessage = 'تم الحفظ بنجاح ✓';
                            this.isModified = false;
                        } else {
                            throw new Error(data.message || 'خطأ في الحفظ');
                        }
                    } catch (error) {
                        this.saveStatus = 'error';
                        this.saveMessage = 'خطأ: ' + error.message;
                    }
                    
                    setTimeout(() => {
                        this.saveStatus = '';
                    }, 3000);
                },
                
                markModified() {
                    this.isModified = true;
                },
                
                insertPageBefore() {
                    Alpine.store('insertPage').openModal('before');
                },
                
                insertPageAfter() {
                    Alpine.store('insertPage').openModal('after');
                }
            }
        }
        
        // Warn before leaving if there are unsaved changes
        window.addEventListener('beforeunload', function(e) {
            const editor = document.querySelector('[x-data]')?.__x?.$data;
            if (editor?.isModified) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>
