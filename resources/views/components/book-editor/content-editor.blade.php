@props([
    'currentPage' => null,
    'pages' => collect(),
    'book' => null,
    'currentPageNum' => 1,
    'totalPages' => 1,
    'nextPage' => null,
    'previousPage' => null
])

<style>
    /* Editor Toolbar Styles */
    .editor-toolbar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        font-family: var(--font-ui);
    }
    
    .toolbar-group {
        display: flex;
        gap: 4px;
        padding: 0 8px;
        border-left: 1px solid #e5e7eb;
    }
    
    .toolbar-group:first-child {
        padding-right: 8px;
        border-left: none;
    }
    
    .toolbar-btn {
        width: 36px;
        height: 36px;
        border: 1px solid #e5e7eb;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: #374151;
    }
    
    .toolbar-btn:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        transform: translateY(-1px);
    }
    
    .toolbar-btn.active {
        background: #dbeafe;
        border-color: #3b82f6;
        color: #1d4ed8;
    }
    
    .toolbar-select {
        height: 36px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 0 8px;
        background: white;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .toolbar-select:hover {
        border-color: #d1d5db;
    }
    
    .toolbar-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .color-picker-wrapper {
        position: relative;
    }
    
    .color-picker-btn {
        width: 36px;
        height: 36px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        background: white;
    }
    
    .color-picker-btn input[type="color"] {
        position: absolute;
        width: 200%;
        height: 200%;
        top: -50%;
        left: -50%;
        border: none;
        cursor: pointer;
    }
    
    .content-editor {
        min-height: 400px;
        padding: 24px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        outline: none;
        font-family: 'Amiri', 'Traditional Arabic', serif;
        font-size: 18px;
        line-height: 2;
        color: #1f2937;
    }
    
    .content-editor:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<main class="flex-1 overflow-y-auto" style="height: calc(100vh - 80px); background-color: var(--bg-body);">
    <div class="max-w-4xl mx-auto p-2 lg:p-8">
        <div class="p-0">
            
            <div id="book-content-wrapper" class="space-y-8">
                @if($currentPage)
                    <div class="rounded-lg shadow-lg p-2 lg:p-8 relative page-container" 
                         style="background-color: var(--bg-paper); box-shadow: var(--shadow-paper);"
                         data-page="{{ $currentPageNum }}">
                         
                        <!-- Page Header -->
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                            <span class="text-xs text-gray-400" style="font-family: var(--font-ui);">
                                {{ $book->title ?? 'الكتاب' }} - {{ $book->authors->first()->full_name ?? 'المؤلف' }}
                            </span>
                            <span class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-full font-bold">
                                وضع التحرير
                            </span>
                        </div>
                        
                        @if($currentPage->chapter)
                            <h3 class="text-xl font-bold mb-4" style="color: var(--accent-color); font-family: var(--font-ui);">
                                {{ $currentPage->chapter->title }}
                            </h3>
                        @endif
                        
                        <!-- Rich Text Editor Toolbar -->
                        <div class="editor-toolbar" id="editor-toolbar">
                            <!-- Font Size -->
                            <div class="toolbar-group">
                                <select class="toolbar-select" id="fontSize" title="حجم الخط">
                                    <option value="1">صغير جداً</option>
                                    <option value="2">صغير</option>
                                    <option value="3" selected>عادي</option>
                                    <option value="4">كبير</option>
                                    <option value="5">كبير جداً</option>
                                    <option value="6">ضخم</option>
                                    <option value="7">عملاق</option>
                                </select>
                            </div>
                            
                            <!-- Colors -->
                            <div class="toolbar-group">
                                <div class="color-picker-wrapper">
                                    <div class="color-picker-btn" title="لون الخط">
                                        <input type="color" id="textColor" value="#000000">
                                    </div>
                                </div>
                                <div class="color-picker-wrapper">
                                    <div class="color-picker-btn" title="لون الخلفية">
                                        <input type="color" id="bgColor" value="#ffff00">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Text Formatting -->
                            <div class="toolbar-group">
                                <button class="toolbar-btn" data-command="bold" title="عريض (Ctrl+B)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                                        <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="italic" title="مائل (Ctrl+I)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="19" y1="4" x2="10" y2="4"></line>
                                        <line x1="14" y1="20" x2="5" y2="20"></line>
                                        <line x1="15" y1="4" x2="9" y2="20"></line>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="underline" title="تسطير (Ctrl+U)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path>
                                        <line x1="4" y1="21" x2="20" y2="21"></line>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="strikeThrough" title="يتوسطه خط">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.3 4.9c-2.3-.6-4.4-1-6.2-.9-2.7 0-5.3.7-5.3 3.6 0 1.5 1.8 3.3 3.6 3.9h.2m8.2 3.7c.3.4.4.8.4 1.3 0 2.9-2.7 3.6-6.2 3.6-2.3 0-4.4-.3-6.2-.9M4 11.5h16"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Alignment -->
                            <div class="toolbar-group">
                                <button class="toolbar-btn" data-command="justifyRight" title="محاذاة لليمين">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="21" y1="10" x2="3" y2="10"></line>
                                        <line x1="21" y1="6" x2="7" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="21" y1="18" x2="7" y2="18"></line>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="justifyCenter" title="توسيط">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="10" x2="6" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="18" y1="14" x2="6" y2="14"></line>
                                        <line x1="21" y1="18" x2="3" y2="18"></line>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="justifyLeft" title="محاذاة لليسار">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="21" y1="10" x2="3" y2="10"></line>
                                        <line x1="17" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="17" y1="18" x2="3" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Lists -->
                            <div class="toolbar-group">
                                <button class="toolbar-btn" data-command="insertUnorderedList" title="قائمة نقطية">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="8" y1="6" x2="21" y2="6"></line>
                                        <line x1="8" y1="12" x2="21" y2="12"></line>
                                        <line x1="8" y1="18" x2="21" y2="18"></line>
                                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="insertOrderedList" title="قائمة مرقمة">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="10" y1="6" x2="21" y2="6"></line>
                                        <line x1="10" y1="12" x2="21" y2="12"></line>
                                        <line x1="10" y1="18" x2="21" y2="18"></line>
                                        <path d="M4 6h1v4"></path>
                                        <path d="M4 10h2"></path>
                                        <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Undo/Redo -->
                            <div class="toolbar-group">
                                <button class="toolbar-btn" data-command="undo" title="تراجع (Ctrl+Z)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 7v6h6"></path>
                                        <path d="M21 17a9 9 0 00-9-9 9 9 0 00-6 2.3L3 13"></path>
                                    </svg>
                                </button>
                                <button class="toolbar-btn" data-command="redo" title="إعادة (Ctrl+Y)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 7v6h-6"></path>
                                        <path d="M3 17a9 9 0 019-9 9 9 0 016 2.3l3 2.7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Clear Formatting -->
                            <div class="toolbar-group">
                                <button class="toolbar-btn" data-command="removeFormat" title="إزالة التنسيق">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 7V4h16v3"></path>
                                        <path d="M5 20h6"></path>
                                        <path d="M13 4L8 20"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Editable Content -->
                        <div id="content-editor" 
                             class="content-editor" 
                             contenteditable="true"
                             @input="markModified()"
                             dir="rtl">
                            {!! $currentPage->html_content ?? nl2br(e($currentPage->content)) !!}
                        </div>

                        <!-- Page Footer -->
                        <div class="flex justify-center items-center mt-8 pt-6 border-t border-gray-100 text-gray-400 font-bold" style="font-family: var(--font-ui);">
                            <span class="text-lg">{{ $currentPage->original_page_number }}</span>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg shadow-lg p-2 lg:p-8 text-center" 
                         style="background-color: var(--bg-paper); box-shadow: var(--shadow-paper);">
                        <div class="text-6xl mb-4">📖</div>
                        <p class="text-gray-500 text-lg" style="font-family: var(--font-ui);">
                            لم يتم العثور على محتوى لهذه الصفحة
                        </p>
                    </div>
                @endif
            </div>
            
            <!-- Navigation -->
            <div class="flex items-center justify-between mt-8 gap-4">
                @if($previousPage)
                    <a href="{{ route('book.edit', ['bookId' => $book->id, 'pageNumber' => $previousPage->page_number]) }}" 
                       class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-xl shadow-md hover:bg-gray-50 hover:border-red-300 transition-all"
                       style="font-family: var(--font-ui);">
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>الصفحة السابقة</span>
                    </a>
                @else
                    <div class="px-6 py-3"></div>
                @endif
                
                <div class="flex items-center gap-2" style="font-family: var(--font-ui);">
                    <span class="text-sm text-gray-500">صفحة</span>
                    <input type="number" 
                           id="page-jump-input"
                           min="1" 
                           max="{{ $totalPages }}" 
                           value="{{ $currentPageNum }}"
                           class="w-16 px-2 py-2 border border-gray-200 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                           onchange="window.location.href='/editBook/{{ $book->id }}/' + this.value">
                    <span class="text-sm text-gray-500">من {{ $totalPages }}</span>
                </div>
                
                @if($nextPage)
                    <a href="{{ route('book.edit', ['bookId' => $book->id, 'pageNumber' => $nextPage->page_number]) }}" 
                       class="flex items-center gap-2 px-6 py-3 text-white rounded-xl shadow-md hover:opacity-90 transition-all"
                       style="background-color: var(--accent-color); font-family: var(--font-ui);">
                        <span>الصفحة التالية</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @else
                    <div class="px-6 py-3"></div>
                @endif
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('content-editor');
        const toolbar = document.getElementById('editor-toolbar');
        
        if (!editor || !toolbar) return;
        
        // Execute formatting command
        function execCmd(command, value = null) {
            document.execCommand(command, false, value);
            editor.focus();
            updateToolbarState();
        }
        
        // Update toolbar button states
        function updateToolbarState() {
            const buttons = toolbar.querySelectorAll('[data-command]');
            buttons.forEach(btn => {
                const command = btn.dataset.command;
                if (document.queryCommandState(command)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
        
        // Toolbar button clicks
        toolbar.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-command]');
            if (btn) {
                e.preventDefault();
                execCmd(btn.dataset.command);
            }
        });
        
        // Font size
        document.getElementById('fontSize')?.addEventListener('change', function(e) {
            execCmd('fontSize', e.target.value);
        });
        
        // Text color
        document.getElementById('textColor')?.addEventListener('input', function(e) {
            execCmd('foreColor', e.target.value);
        });
        
        // Background color
        document.getElementById('bgColor')?.addEventListener('input', function(e) {
            execCmd('backColor', e.target.value);
        });
        
        // Update state on selection change
        document.addEventListener('selectionchange', function() {
            if (document.activeElement === editor) {
                updateToolbarState();
            }
        });
        
        // Keyboard shortcuts
        editor.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key.toLowerCase()) {
                    case 'b':
                        e.preventDefault();
                        execCmd('bold');
                        break;
                    case 'i':
                        e.preventDefault();
                        execCmd('italic');
                        break;
                    case 'u':
                        e.preventDefault();
                        execCmd('underline');
                        break;
                }
            }
        });
    });
    
    // Page navigation shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.target.id === 'content-editor' || e.target.tagName === 'INPUT') return;
        
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            @if($nextPage)
                window.location.href = "{{ route('book.edit', ['bookId' => $book->id, 'pageNumber' => $nextPage->page_number]) }}";
            @endif
        }
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            @if($previousPage)
                window.location.href = "{{ route('book.edit', ['bookId' => $book->id, 'pageNumber' => $previousPage->page_number]) }}";
            @endif
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const alpineData = document.querySelector('[x-data]').__x.$data;
            if (alpineData && alpineData.saveContent) {
                alpineData.saveContent();
            }
        }
    });
</script>
