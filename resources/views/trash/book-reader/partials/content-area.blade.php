{{-- Content Area - منطقة المحتوى الرئيسية --}}

<div class="content-wrapper">
    {{-- الورقة (Paper Sheet) --}}
    <article 
        class="paper-sheet"
        id="paper-sheet"
        style="--font-size: {{ $fontSize }}px; --font-family: '{{ $fontFamily }}', serif;"
    >
        {{-- رأس الصفحة (إذا وجد فصل) --}}
        @if($currentChapterTitle)
            <header class="chapter-header">
                <h2 class="chapter-title">{{ $currentChapterTitle }}</h2>
                @if($currentVolumeTitle)
                    <span class="chapter-volume">{{ $currentVolumeTitle }}</span>
                @endif
                <div class="chapter-ornament">❖</div>
            </header>
        @endif
        
        {{-- محتوى الصفحة --}}
        <div class="chapter-text" id="chapter-text">
            @if(!empty($pageContent))
                {!! $pageContent !!}
            @else
                <div class="empty-page">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="empty-icon">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                    <p>لا يوجد محتوى لهذه الصفحة</p>
                    <span class="empty-page-number">الصفحة {{ $pageNumber }}</span>
                </div>
            @endif
        </div>
        
        {{-- تذييل الورقة --}}
        <footer class="paper-footer">
            <div class="page-ornament">۞</div>
        </footer>
    </article>
</div>

{{-- مؤشر رقم الصفحة العائم --}}
<div class="page-indicator" id="page-indicator">
    <span class="page-current">{{ $pageNumber }}</span>
    <span class="page-separator">/</span>
    <span class="page-total">{{ $this->totalPages }}</span>
</div>
