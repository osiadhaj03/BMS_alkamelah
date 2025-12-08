{{-- Sidebar - Table of Contents (الفهرس الجانبي) --}}

<aside 
    class="reader-sidebar {{ $showSidebar ? 'sidebar-visible' : 'sidebar-hidden' }}"
>
    {{-- رأس الفهرس --}}
    <div class="sidebar-header">
        <h2 class="sidebar-title">
            <svg viewBox="0 0 24 24" fill="currentColor" class="sidebar-title-icon">
                <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
            </svg>
            فهرس الكتاب
        </h2>
        
        {{-- بحث في الفهرس --}}
        <div class="sidebar-search">
            <input 
                type="text"
                wire:model.live.debounce.300ms="tocSearchQuery"
                placeholder="ابحث في الفهرس..."
                class="sidebar-search-input"
            >
            <span class="sidebar-search-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </span>
        </div>
    </div>
    
    {{-- قائمة الفهرس --}}
    <div class="sidebar-content">
        <nav class="toc-nav">
            @php $toc = $this->filteredTableOfContents; @endphp
            
            @if($toc['type'] === 'volumes')
                {{-- عرض المجلدات مع الفصول --}}
                @foreach($toc['data'] as $volume)
                    <div class="toc-volume" wire:key="vol-{{ $volume['id'] }}">
                        {{-- عنوان المجلد --}}
                        <button 
                            class="toc-volume-header {{ in_array($volume['id'], $expandedVolumes) ? 'expanded' : '' }}"
                            wire:click="toggleVolume({{ $volume['id'] }})"
                        >
                            <span class="toc-volume-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                                </svg>
                            </span>
                            <span class="toc-volume-title">{{ $volume['title'] }}</span>
                            <span class="toc-chevron">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                                </svg>
                            </span>
                        </button>
                        
                        {{-- الفصول داخل المجلد --}}
                        @if(in_array($volume['id'], $expandedVolumes) && !empty($volume['chapters']))
                            <div class="toc-chapters">
                                @foreach($volume['chapters'] as $chapter)
                                    @include('livewire.book-reader.partials.chapter-item', ['chapter' => $chapter, 'level' => 0])
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                {{-- عرض الفصول فقط (بدون مجلدات) --}}
                <div class="toc-chapters-only">
                    @foreach($toc['data'] as $chapter)
                        @include('livewire.book-reader.partials.chapter-item', ['chapter' => $chapter, 'level' => 0])
                    @endforeach
                </div>
            @endif
            
            @if(empty($toc['data']))
                <div class="toc-empty">
                    <p>لا توجد نتائج</p>
                </div>
            @endif
        </nav>
    </div>
    
    {{-- تذييل الفهرس --}}
    <div class="sidebar-footer">
        <div class="reading-progress">
            <span class="progress-text">التقدم: {{ $this->progressPercentage }}%</span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $this->progressPercentage }}%"></div>
            </div>
        </div>
    </div>
</aside>
