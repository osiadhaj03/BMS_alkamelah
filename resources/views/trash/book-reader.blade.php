<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - قارئ الكتاب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Arabic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-body: #f8f2e8;
            --bg-paper: #ffffff;
            --text-main: #2b2b2b;
            --text-secondary: #666666;
            --accent-color: #15803d;
            --border-color: #e0d9cc;
            --font-main: 'Amiri', serif;
            --font-ui: 'Tajawal', sans-serif;
        }
        
        body { 
            font-family: var(--font-ui); 
            background: linear-gradient(135deg, #f8f2e8 0%, #f5efe5 50%, #efe8dc 100%);
            color: var(--text-main);
            min-height: 100vh;
        }
        
        .font-tajawal { font-family: 'Tajawal', sans-serif; }
        .font-amiri { font-family: 'Amiri', serif; }
        
        .book-content {
            font-family: 'Amiri', serif;
            font-size: 1.2rem;
            line-height: 2.2;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    </style>
</head>
<body>
    {{-- Header Section --}}
    <header class="bg-white shadow-md border-b border-[#e0d9cc] sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                {{-- Book Title & Author --}}
                <div class="flex-1">
                    <h1 class="text-xl md:text-2xl font-bold text-green-900 font-tajawal">
                        {{ $book->title }}
                    </h1>
                    @if($book->authors->isNotEmpty())
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $book->authors->first()->full_name ?? 'مؤلف غير معروف' }}
                        </p>
                    @endif
                </div>
                
                {{-- Navigation Info --}}
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600 hidden sm:inline">
                        صفحة {{ $currentPageNum }} من {{ $totalPages }}
                    </span>
                    <a href="{{ url('/') }}" class="px-4 py-2 bg-green-800 text-white rounded-lg hover:bg-green-900 transition-colors text-sm">
                        الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- Sidebar - Table of Contents --}}
            <aside class="w-full lg:w-80 flex-shrink-0 order-2 lg:order-1">
                <div class="bg-white rounded-xl shadow-lg border border-[#e0d9cc] p-4 lg:sticky lg:top-24">
                    <h2 class="text-lg font-bold text-green-900 mb-4 pb-2 border-b border-[#e0d9cc] font-tajawal">
                        📚 فهرس الكتاب
                    </h2>
                    
                    @if($chapters->isEmpty())
                        <p class="text-gray-500 text-sm">لا يوجد فهرس متاح لهذا الكتاب</p>
                    @else
                        <ul class="space-y-2 max-h-[60vh] overflow-y-auto">
                            @foreach($chapters as $chapter)
                                <li>
                                    <a href="{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $chapter->page_start ?? 1]) }}" 
                                       class="block py-2 px-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-900 transition-colors text-sm {{ ($currentPage && $currentPage->chapter_id == $chapter->id) ? 'bg-green-100 text-green-900 font-semibold' : '' }}">
                                        {{ $chapter->title }}
                                        @if($chapter->page_start)
                                            <span class="text-xs text-gray-400 mr-1">(ص{{ $chapter->page_start }})</span>
                                        @endif
                                    </a>
                                    
                                    {{-- Nested Chapters --}}
                                    @if($chapter->children && $chapter->children->isNotEmpty())
                                        <ul class="mr-4 mt-1 space-y-1 border-r-2 border-green-100 pr-2">
                                            @foreach($chapter->children as $child)
                                                <li>
                                                    <a href="{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $child->page_start ?? 1]) }}" 
                                                       class="block py-1 px-3 text-sm rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-900 transition-colors">
                                                        {{ $child->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </aside>
            
            {{-- Main Content Area --}}
            <main class="flex-1 order-1 lg:order-2">
                <div class="bg-white rounded-xl shadow-lg border border-[#e0d9cc] p-6 md:p-8">
                    
                    {{-- Chapter Title if exists --}}
                    @if($currentPage && $currentPage->chapter)
                        <div class="mb-4 pb-4 border-b border-[#e0d9cc]">
                            <h2 class="text-lg font-bold text-green-800 font-tajawal">
                                {{ $currentPage->chapter->title }}
                            </h2>
                        </div>
                    @endif
                    
                    {{-- Page Content --}}
                    @if($currentPage)
                        <div class="book-content text-gray-800">
                            {!! $currentPage->html_content ?? nl2br(e($currentPage->content)) !!}
                        </div>
                    @else
                        <div class="text-center py-20">
                            <div class="text-6xl mb-4">📖</div>
                            <p class="text-gray-500 text-lg">لم يتم العثور على محتوى لهذه الصفحة</p>
                            <p class="text-gray-400 text-sm mt-2">حاول الانتقال إلى صفحة أخرى</p>
                        </div>
                    @endif
                    
                    {{-- Page Number Badge --}}
                    @if($currentPage)
                        <div class="mt-8 pt-4 border-t border-[#e0d9cc] text-center">
                            <span class="inline-block px-4 py-2 bg-green-50 text-green-900 rounded-full text-sm font-semibold">
                                صفحة {{ $currentPageNum }}
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- Navigation Buttons --}}
                <div class="mt-6 flex items-center justify-between gap-4">
                    {{-- Previous Page --}}
                    @if($previousPage)
                        <a href="{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $previousPage->page_number]) }}" 
                           class="flex items-center gap-2 px-5 py-3 bg-white border border-[#e0d9cc] rounded-xl shadow-md hover:bg-green-50 hover:border-green-300 transition-all text-sm">
                            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span class="hidden sm:inline">السابقة</span>
                        </a>
                    @else
                        <div class="px-5 py-3"></div>
                    @endif
                    
                    {{-- Page Jump Form --}}
                    <form action="{{ route('book.read', ['bookId' => $book->id]) }}" method="GET" class="flex items-center gap-2">
                        <input type="number" name="pageNumber" min="1" max="{{ $totalPages }}" value="{{ $currentPageNum }}"
                               class="w-16 px-2 py-2 border border-[#e0d9cc] rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                               onchange="this.form.action = '/book/{{ $book->id }}/' + this.value; this.form.submit();">
                        <span class="text-sm text-gray-500">/ {{ $totalPages }}</span>
                    </form>
                    
                    {{-- Next Page --}}
                    @if($nextPage)
                        <a href="{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $nextPage->page_number]) }}" 
                           class="flex items-center gap-2 px-5 py-3 bg-green-800 text-white rounded-xl shadow-md hover:bg-green-900 transition-all text-sm">
                            <span class="hidden sm:inline">التالية</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @else
                        <div class="px-5 py-3"></div>
                    @endif
                </div>
                
                {{-- Progress Bar --}}
                <div class="mt-6 bg-white rounded-xl shadow-md border border-[#e0d9cc] p-4">
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>تقدم القراءة</span>
                        @php $progress = $totalPages > 0 ? ($currentPageNum / $totalPages) * 100 : 0; @endphp
                        <span>{{ number_format($progress, 1) }}%</span>
                    </div>
                    <div class="bg-[#e0d9cc] rounded-full h-2 overflow-hidden">
                        <div class="bg-green-600 h-full rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    {{-- Keyboard Navigation Script --}}
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT') return;
            
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                @if($nextPage)
                    window.location.href = "{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $nextPage->page_number]) }}";
                @endif
            }
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                @if($previousPage)
                    window.location.href = "{{ route('book.read', ['bookId' => $book->id, 'pageNumber' => $previousPage->page_number]) }}";
                @endif
            }
        });
    </script>
</body>
</html>
