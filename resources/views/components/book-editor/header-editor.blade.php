@props([
    'book' => null,
    'currentPageNum' => 1,
    'totalPages' => 1
])

<style>
    /* Scoped styles for the book editor header */
    .book-editor-header {
        background-image: url('/images/backgrond_islamic.png');
        background-size: auto;
        background-repeat: repeat;
        background-position: center top;
        position: relative;
        padding: 0.5rem 0.75rem;
        border-bottom: 2px solid #dc2626;
    }
    
    @media (min-width: 1024px) {
        .book-editor-header {
            padding: 1.5rem 2rem;
        }
    }
    
    .book-editor-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background-color: rgba(254, 226, 226, 0.3); /* Light red tint for edit mode */
        z-index: 0;
    }
    .book-editor-header > * {
        position: relative;
        z-index: 1;
    }

    .header-main-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .book-identity {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        min-width: 0;
    }

    .book-icon-svg {
        width: 48px;
        height: 48px;
        display: none;
    }
    
    @media (min-width: 1024px) {
        .book-icon-svg {
            display: block;
        }
    }

    .header-title h1 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
    }
    
    .header-title span {
        font-size: 0.9rem;
        color: var(--text-secondary);
        display: block;
        margin-top: 0.25rem;
    }

    .save-button {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 8px;
        font-family: var(--font-ui);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: var(--shadow-soft);
    }
    
    .save-button:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: var(--shadow-dropdown);
    }
    
    .save-button:active {
        transform: translateY(0);
    }

    .page-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: var(--font-ui);
        font-size: 0.9rem;
        color: var(--text-secondary);
    }
</style>

<header class="book-editor-header">
    <div class="header-main-row">
        <!-- Book Identity -->
        <div class="book-identity">
            <div class="book-icon-svg">
                <img src="/images/icon_islamic.png" alt="أيقونة إسلامية" width="48" height="48" style="display: block; margin: 0 auto;" />
            </div>
            <div class="header-title">
                <h1>✏️ تحرير: {{ $book->title ?? 'الكتاب' }}</h1>
                <span>{{ $book->authors->first()->full_name ?? 'المؤلف' }}</span>
            </div>
        </div>
        
        <!-- Page Info -->
        <div class="page-info">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span>صفحة {{ $currentPageNum }} من {{ $totalPages }}</span>
        </div>
        
        <!-- Page Insertion Buttons -->
        <div class="flex gap-2">
            <button @click="insertPageBefore()" 
                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2"
                    style="font-family: var(--font-ui); font-size: 0.9rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>إدراج قبل</span>
            </button>
            <button @click="insertPageAfter()" 
                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2"
                    style="font-family: var(--font-ui); font-size: 0.9rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>إدراج بعد</span>
            </button>
        </div>
        
        <!-- Save Button -->
        <button @click="saveContent()" class="save-button">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            <span>حفظ التعديلات</span>
        </button>
    </div>
</header>
