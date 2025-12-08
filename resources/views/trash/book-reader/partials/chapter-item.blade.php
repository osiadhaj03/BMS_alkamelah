{{-- Chapter Item - عنصر فصل في الفهرس (Recursive) --}}

@props(['chapter', 'level' => 0])

<div class="toc-chapter" wire:key="ch-{{ $chapter['id'] }}" style="--level: {{ $level }}">
    <div class="toc-chapter-row">
        {{-- زر التوسيع (إذا كان له أبناء) --}}
        @if(!empty($chapter['children']))
            <button 
                class="toc-expand-btn {{ in_array($chapter['id'], $expandedChapters) ? 'expanded' : '' }}"
                wire:click="toggleChapter({{ $chapter['id'] }})"
            >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                </svg>
            </button>
        @else
            <span class="toc-expand-placeholder"></span>
        @endif
        
        {{-- عنوان الفصل --}}
        <button 
            class="toc-chapter-link {{ $currentPage && $currentPage->chapter_id === $chapter['id'] ? 'active' : '' }}"
            wire:click="goToChapter({{ $chapter['id'] }})"
            title="{{ $chapter['title'] }}"
        >
            <span class="toc-chapter-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
            </span>
            <span class="toc-chapter-title">{{ $chapter['title'] }}</span>
            @if($chapter['page_start'])
                <span class="toc-chapter-page">{{ $chapter['page_start'] }}</span>
            @endif
        </button>
    </div>
    
    {{-- الفصول الفرعية --}}
    @if(!empty($chapter['children']) && in_array($chapter['id'], $expandedChapters))
        <div class="toc-children">
            @foreach($chapter['children'] as $child)
                @include('livewire.book-reader.partials.chapter-item', ['chapter' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
