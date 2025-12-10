<div class="w-80 bg-white border-l border-gray-200 overflow-y-auto">
    <div class="p-4">
        <h3 class="text-lg font-bold text-green-700 mb-4" style="font-family: Tajawal;" dir="rtl">
            الصفحة
        </h3>
        
        <!-- Page Input -->
        <div class="mb-4" dir="rtl">
            <div class="flex items-center gap-2">
                <input 
                    type="number" 
                    value="{{ $currentPage }}" 
                    min="1" 
                    max="{{ $totalPages }}"
                    class="w-16 px-2 py-1 text-center border border-gray-300 rounded focus:outline-none focus:border-green-600"
                    style="font-family: Tajawal;"
                >
                <span class="text-gray-600 text-sm" style="font-family: Tajawal;">من {{ $totalPages }}</span>
            </div>
        </div>

        <h3 class="text-lg font-bold text-green-700 mb-4" style="font-family: Tajawal;" dir="rtl">
            فهرس المحتويات
        </h3>
        
        <!-- Table of Contents -->
        <div class="space-y-2" dir="rtl">
            @foreach($sections as $index => $section)
                <div class="border-b border-gray-100 pb-2">
                    <a href="{{ route('book.read', ['book' => $book->id, 'page' => $section->start_page ?? ($index * 10 + 1)]) }}" 
                       class="flex justify-between items-center p-2 rounded hover:bg-green-50 transition-colors {{ $currentPage >= ($section->start_page ?? ($index * 10 + 1)) && $currentPage <= ($section->end_page ?? ($index * 10 + 10)) ? 'bg-green-100 text-green-700' : 'text-gray-700' }}">
                        <span class="text-sm font-medium" style="font-family: Tajawal;">
                            {{ $section->title ?? "الفصل " . ($index + 1) }}
                        </span>
                        <span class="text-xs text-gray-500" style="font-family: Tajawal;">
                            {{ $section->start_page ?? ($index * 10 + 1) }}
                        </span>
                    </a>
                </div>
            @endforeach
            
            @if($sections->isEmpty())
                <!-- Default chapters if no sections exist -->
                @for($i = 1; $i <= 10; $i++)
                    <div class="border-b border-gray-100 pb-2">
                        <a href="{{ route('book.read', ['book' => $book->id, 'page' => ($i - 1) * 10 + 1]) }}" 
                           class="flex justify-between items-center p-2 rounded hover:bg-green-50 transition-colors {{ $currentPage >= (($i - 1) * 10 + 1) && $currentPage <= ($i * 10) ? 'bg-green-100 text-green-700' : 'text-gray-700' }}">
                            <span class="text-sm font-medium" style="font-family: Tajawal;">
                                الفصل {{ $i }}
                            </span>
                            <span class="text-xs text-gray-500" style="font-family: Tajawal;">
                                {{ ($i - 1) * 10 + 1 }}
                            </span>
                        </a>
                    </div>
                @endfor
            @endif
        </div>
        
        <!-- Reading Progress -->
        <div class="mt-6 pt-4 border-t border-gray-200" dir="rtl">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600" style="font-family: Tajawal;">تقدم القراءة</span>
                <span class="text-sm font-medium text-green-600" style="font-family: Tajawal;">
                    {{ round(($currentPage / $totalPages) * 100) }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ ($currentPage / $totalPages) * 100 }}%"></div>
            </div>
        </div>
    </div>
</div>