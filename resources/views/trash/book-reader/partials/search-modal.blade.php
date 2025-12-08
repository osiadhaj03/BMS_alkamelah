{{-- Search Modal - نافذة البحث --}}

<div 
    class="modal-overlay"
    x-show="$wire.showSearchModal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click.self="$wire.showSearchModal = false"
    @keydown.escape.window="$wire.showSearchModal = false"
    x-cloak
>
    <div 
        class="modal-content modal-search"
        x-show="$wire.showSearchModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
    >
        {{-- رأس النافذة --}}
        <div class="modal-header">
            <h3 class="modal-title">
                <svg viewBox="0 0 24 24" fill="currentColor" class="modal-title-icon">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                البحث في الكتاب
            </h3>
            <button 
                class="modal-close"
                @click="$wire.showSearchModal = false"
            >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        
        {{-- حقل البحث --}}
        <div class="modal-body">
            <div class="search-input-wrapper">
                <input 
                    type="text"
                    wire:model.live.debounce.500ms="searchQuery"
                    wire:keydown.enter="search"
                    placeholder="اكتب كلمة البحث..."
                    class="search-input-field"
                    autofocus
                >
                <button 
                    class="search-submit-btn"
                    wire:click="search"
                >
                    بحث
                </button>
            </div>
            
            {{-- نتائج البحث --}}
            <div class="search-results">
                @if(!empty($searchResults))
                    <div class="results-header">
                        <span class="results-count">{{ count($searchResults) }} نتيجة</span>
                    </div>
                    
                    <div class="results-list">
                        @foreach($searchResults as $result)
                            <button 
                                class="result-item"
                                wire:click="goToSearchResult({{ $result['page_number'] }})"
                                wire:key="search-{{ $result['page_number'] }}"
                            >
                                <div class="result-page">
                                    <span class="result-page-label">صفحة</span>
                                    <span class="result-page-number">{{ $result['page_number'] }}</span>
                                </div>
                                <div class="result-content">
                                    <p class="result-excerpt">{!! $result['excerpt'] !!}</p>
                                    <div class="result-meta">
                                        @if($result['volume'])
                                            <span class="result-volume">{{ $result['volume'] }}</span>
                                        @endif
                                        @if($result['chapter'])
                                            <span class="result-chapter">{{ $result['chapter'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @elseif(!empty($searchQuery) && strlen($searchQuery) >= 2)
                    <div class="no-results">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="no-results-icon">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <p>لم يتم العثور على نتائج</p>
                        <span>جرب كلمات بحث أخرى</span>
                    </div>
                @else
                    <div class="search-hint">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="hint-icon">
                            <path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                        <p>ابحث عن أي كلمة في الكتاب</p>
                        <span>أدخل حرفين على الأقل للبحث</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
