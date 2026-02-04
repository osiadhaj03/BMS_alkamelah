@props([
    'chapters' => collect(),
    'book' => null,
    'currentPage' => null
])

<style>
    .toc-editor-sidebar {
        width: 320px;
        height: calc(100vh - 80px);
        background-color: var(--bg-sidebar);
        border-left: 1px solid var(--border-color);
        overflow-y: auto;
        position: sticky;
        top: 0;
        transition: transform 0.3s ease;
    }
    
    @media (max-width: 1023px) {
        .toc-editor-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            z-index: 40;
            transform: translateX(100%);
            box-shadow: var(--shadow-dropdown);
        }
        
        .toc-editor-sidebar.open {
            transform: translateX(0);
        }
    }
    
    .toc-header {
        position: sticky;
        top: 0;
        background: var(--bg-sidebar);
        padding: 1rem;
        border-bottom: 2px solid var(--accent-color);
        z-index: 10;
    }
    
    .add-chapter-btn {
        width: 100%;
        padding: 0.75rem;
        background: var(--accent-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-family: var(--font-ui);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .add-chapter-btn:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
    }
</style>

<aside class="toc-editor-sidebar" id="toc-editor-sidebar" x-data="tocEditor()">
    <!-- Header -->
    <div class="toc-header">
        <h3 class="text-lg font-bold mb-3" style="color: var(--text-main); font-family: var(--font-ui);">
            📑 إدارة الفهرس
        </h3>
        <button @click="openAddModal(null)" class="add-chapter-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>إضافة فصل جديد</span>
        </button>
    </div>
    
    <!-- Chapters List -->
    <div class="p-4">
        @if($chapters && $chapters->count() > 0)
            @foreach($chapters as $chapter)
                <x-book-editor.chapter-item-editor 
                    :chapter="$chapter" 
                    :book="$book"
                    :currentPage="$currentPage"
                />
            @endforeach
        @else
            <div class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p style="font-family: var(--font-ui);">لا توجد فصول بعد</p>
                <p class="text-sm mt-1">ابدأ بإضافة فصل جديد</p>
            </div>
        @endif
    </div>
    
    <!-- Add/Edit Chapter Modal -->
    <div x-show="showModal" 
         x-cloak
         @click.self="closeModal()"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
            <h3 class="text-xl font-bold mb-4" style="font-family: var(--font-ui);" x-text="modalTitle"></h3>
            
            <form @submit.prevent="saveChapter()">
                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                        عنوان الفصل
                    </label>
                    <input type="text" 
                           x-model="formData.title"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                           style="font-family: var(--font-ui);">
                </div>
                
                <!-- Parent Chapter -->
                <div class="mb-4" x-show="!editMode">
                    <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                        الفصل الأب (اختياري)
                    </label>
                    <select x-model="formData.parent_id"
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
                               x-model="formData.page_start"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="font-family: var(--font-ui);">
                            صفحة النهاية
                        </label>
                        <input type="number" 
                               x-model="formData.page_end"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-3 justify-end">
                    <button type="button" 
                            @click="closeModal()"
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
</aside>

<script>
    function tocEditor() {
        return {
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
                const method = this.editMode ? 'PUT' : 'POST';
                
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
                        // Reload page to show new chapter
                        window.location.reload();
                    } else {
                        alert('خطأ: ' + (data.message || 'فشل الحفظ'));
                    }
                } catch (error) {
                    alert('خطأ: ' + error.message);
                }
            }
        }
    }
</script>
