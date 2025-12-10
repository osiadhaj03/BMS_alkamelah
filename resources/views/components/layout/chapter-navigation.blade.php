<div class="w-80 bg-gray-50 border-r border-gray-200 overflow-y-auto">
    <div class="p-4">
        <h3 class="text-lg font-bold text-green-700 mb-4" style="font-family: Tajawal;" dir="rtl">
            فصول الكتاب
        </h3>
        
        <!-- Chapters List -->
        <div class="space-y-1" dir="rtl">
            @foreach($sections as $index => $section)
                <div class="bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-shadow">
                    <a href="{{ route('book.read', ['book' => $book->id, 'page' => $section->start_page ?? ($index * 10 + 1)]) }}" 
                       class="block p-3 hover:bg-green-50 rounded-lg transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 text-sm mb-1" style="font-family: Tajawal;">
                                    {{ $section->title ?? "الفصل " . ($index + 1) }}
                                </h4>
                                <p class="text-xs text-gray-600" style="font-family: Tajawal;">
                                    من صفحة {{ $section->start_page ?? ($index * 10 + 1) }} إلى {{ $section->end_page ?? ($index * 10 + 10) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block w-6 h-6 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center justify-center">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                        </div>
                        
                        @if($currentPage >= ($section->start_page ?? ($index * 10 + 1)) && $currentPage <= ($section->end_page ?? ($index * 10 + 10)))
                            <div class="mt-2 text-xs text-green-600 font-medium" style="font-family: Tajawal;">
                                ← تقرأ حالياً
                            </div>
                        @endif
                    </a>
                </div>
            @endforeach
            
            @if($sections->isEmpty())
                <!-- Default chapters if no sections exist -->
                @for($i = 1; $i <= 10; $i++)
                    <div class="bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-shadow">
                        <a href="{{ route('book.read', ['book' => $book->id, 'page' => ($i - 1) * 10 + 1]) }}" 
                           class="block p-3 hover:bg-green-50 rounded-lg transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm mb-1" style="font-family: Tajawal;">
                                        فصل في {{ ['آداب الفتوى', 'المفتي وأحكامه', 'المستفتي وآدابه', 'أصول الإفتاء', 'قواعد الفتوى', 'مناهج الأئمة', 'الاجتهاد والتقليد', 'النوازل والمستجدات', 'ضوابط الفتوى', 'مراتب المفتين'][$i-1] ?? 'الفصل ' . $i }}
                                    </h4>
                                    <p class="text-xs text-gray-600" style="font-family: Tajawal;">
                                        من صفحة {{ ($i - 1) * 10 + 1 }} إلى {{ $i * 10 }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block w-6 h-6 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center justify-center">
                                        {{ $i }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($currentPage >= (($i - 1) * 10 + 1) && $currentPage <= ($i * 10))
                                <div class="mt-2 text-xs text-green-600 font-medium" style="font-family: Tajawal;">
                                    ← تقرأ حالياً
                                </div>
                            @endif
                        </a>
                    </div>
                @endfor
            @endif
        </div>
        
        <!-- Book Info -->
        <div class="mt-6 pt-4 border-t border-gray-300">
            <div class="bg-white rounded-lg p-4 border border-gray-200" dir="rtl">
                <h4 class="font-bold text-green-700 text-sm mb-2" style="font-family: Tajawal;">
                    معلومات الكتاب
                </h4>
                <div class="space-y-2 text-xs text-gray-600" style="font-family: Tajawal;">
                    <div class="flex justify-between">
                        <span>المؤلف:</span>
                        <span class="font-medium">{{ $book->author->name ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>عدد الصفحات:</span>
                        <span class="font-medium">{{ $totalPages }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>القسم:</span>
                        <span class="font-medium">{{ $book->section->name ?? 'غير محدد' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>