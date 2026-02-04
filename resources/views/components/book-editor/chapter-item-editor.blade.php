@props([
    'chapter' => null,
    'book' => null,
    'currentPage' => null,
    'level' => 0
])

<style>
    .chapter-item-editor {
        margin-bottom: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .chapter-header-editor {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .chapter-header-editor:hover {
        background: var(--bg-hover);
        border-color: var(--accent-color);
    }
    
    .chapter-header-editor.active {
        background: var(--accent-light);
        border-color: var(--accent-color);
    }
    
    .chapter-title-editor {
        flex: 1;
        font-family: var(--font-ui);
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .chapter-actions {
        position: relative;
    }
    
    .three-dots-btn {
        padding: 0.25rem 0.5rem;
        background: transparent;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: var(--text-secondary);
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }
    
    .three-dots-btn:hover {
        background: var(--bg-hover);
        color: var(--accent-color);
    }
    
    .actions-dropdown {
        position: absolute;
        left: 0;
        top: 100%;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: var(--shadow-dropdown);
        min-width: 180px;
        z-index: 50;
        display: none;
    }
    
    .actions-dropdown.show {
        display: block;
    }
    
    .action-btn {
        width: 100%;
        padding: 0.75rem 1rem;
        background: transparent;
        border: none;
        text-align: right;
        cursor: pointer;
        color: var(--text-main);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-family: var(--font-ui);
        font-size: 0.9rem;
    }
    
    .action-btn:hover {
        background: var(--bg-hover);
    }
    
    .action-btn.danger:hover {
        color: #dc2626;
    }
    
    .chapter-children {
        margin-right: 1.5rem;
        margin-top: 0.5rem;
    }
</style>

<div class="chapter-item-editor" x-data="chapterItem({{ $chapter->id }}, '{{ addslashes($chapter->title) }}', {{ $chapter->page_start ?? 'null' }}, {{ $chapter->page_end ?? 'null' }})">
    <div class="chapter-header-editor {{ $currentPage && $currentPage->chapter_id == $chapter->id ? 'active' : '' }}">
        <!-- Chapter Title -->
        <div class="chapter-title-editor">
            @if($chapter->children && $chapter->children->count() > 0)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6"></path>
                </svg>
            @endif
            <span>{{ $chapter->title }}</span>
            @if($chapter->page_start)
                <span class="text-xs text-gray-400">(ص {{ $chapter->page_start }})</span>
            @endif
        </div>
        
        <!-- Three Dots Menu -->
        <div class="chapter-actions" x-data="{ showMenu: false }" @click.away="showMenu = false">
            <button @click.stop="showMenu = !showMenu" class="three-dots-btn" title="خيارات">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div class="actions-dropdown" :class="{ 'show': showMenu }">
                <!-- Add Sub-chapter -->
                <button @click.stop="addSubChapter(); showMenu = false" class="action-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>إضافة فصل فرعي</span>
                </button>
                
                <!-- Edit -->
                <button @click.stop="editChapter(); showMenu = false" class="action-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>تعديل</span>
                </button>
                
                <!-- Move Up -->
                <button @click.stop="moveUp(); showMenu = false" class="action-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    <span>تحريك للأعلى</span>
                </button>
                
                <!-- Move Down -->
                <button @click.stop="moveDown(); showMenu = false" class="action-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <span>تحريك للأسفل</span>
                </button>
                
                <div style="height: 1px; background: var(--border-color); margin: 0.25rem 0;"></div>
                
                <!-- Delete -->
                <button @click.stop="deleteChapter(); showMenu = false" class="action-btn" style="color: #dc2626;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>حذف</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Children -->
    @if($chapter->children && $chapter->children->count() > 0)
        <div class="chapter-children">
            @foreach($chapter->children as $child)
                <x-book-editor.chapter-item-editor 
                    :chapter="$child" 
                    :book="$book"
                    :currentPage="$currentPage"
                    :level="$level + 1"
                />
            @endforeach
        </div>
    @endif
</div>

<script>
    function chapterItem(chapterId, chapterTitle, pageStart, pageEnd) {
        return {
            chapterId: chapterId,
            chapterTitle: chapterTitle,
            pageStart: pageStart,
            pageEnd: pageEnd,
            
            addSubChapter() {
                // Call parent TOC editor's openAddModal with this chapter as parent
                const tocEditor = document.querySelector('[x-data*="tocEditor"]').__x.$data;
                if (tocEditor) {
                    tocEditor.openAddModal(this.chapterId);
                }
            },
            
            editChapter() {
                const tocEditor = document.querySelector('[x-data*="tocEditor"]').__x.$data;
                if (tocEditor) {
                    tocEditor.openEditModal({
                        id: this.chapterId,
                        title: this.chapterTitle,
                        page_start: this.pageStart,
                        page_end: this.pageEnd
                    });
                }
            },
            
            async moveUp() {
                const bookId = {{ $book->id ?? 0 }};
                try {
                    const response = await fetch(`/editBook/${bookId}/chapters/${this.chapterId}/move-up`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'فشل التحريك');
                    }
                } catch (error) {
                    alert('خطأ: ' + error.message);
                }
            },
            
            async moveDown() {
                const bookId = {{ $book->id ?? 0 }};
                try {
                    const response = await fetch(`/editBook/${bookId}/chapters/${this.chapterId}/move-down`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'فشل التحريك');
                    }
                } catch (error) {
                    alert('خطأ: ' + error.message);
                }
            },
            
            async deleteChapter() {
                if (!confirm('هل أنت متأكد من حذف هذا الفصل؟\nسيتم حذف جميع الفصول الفرعية أيضاً.')) {
                    return;
                }
                
                const bookId = {{ $book->id ?? 0 }};
                try {
                    const response = await fetch(`/editBook/${bookId}/chapters/${this.chapterId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('خطأ: ' + (data.message || 'فشل الحذف'));
                    }
                } catch (error) {
                    alert('خطأ: ' + error.message);
                }
            }
        }
    }
</script>
