<div class="h-full flex flex-col">
    <!-- Results Header / Filter -->
    <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <span class="text-xs font-bold text-gray-500">254 نتيجة</span>
        <div class="flex gap-1">
            <button class="p-1 hover:bg-white rounded text-gray-400 hover:text-gray-600" title="تصفية النتائج">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-2">
        
        {{-- Mock Result 1 (Active) --}}
        <div class="group relative bg-green-50 rounded-lg p-3 cursor-pointer border border-green-200 transition-all hover:shadow-sm">
             <div class="absolute right-0 top-0 bottom-0 w-1 bg-green-500 rounded-r-lg"></div>
             
             <div class="flex justify-between items-start mb-1">
                 <h4 class="font-bold text-sm text-gray-900 line-clamp-1">المجموع شرح المهذب</h4>
                 <span class="text-[10px] bg-white border px-1.5 py-0.5 rounded text-gray-500 whitespace-nowrap">صـ 412</span>
             </div>
             <p class="text-xs text-gray-500 mb-2">للإمام النووي • الفقه الشافعي</p>
             
             <div class="text-xs text-gray-700 leading-relaxed font-serif bg-white/50 p-1.5 rounded">
                 ...فإن كانت <mark class="bg-yellow-200 text-gray-900 rounded-sm px-0.5">الصلاة</mark> مكتوبة لم يجز تركها، وإن كانت نافلة جاز...
             </div>
        </div>

        {{-- Mock Result 2 --}}
        <div class="group relative bg-white hover:bg-gray-50 rounded-lg p-3 cursor-pointer border border-gray-100 hover:border-gray-300 transition-all">
             <div class="flex justify-between items-start mb-1">
                 <h4 class="font-bold text-sm text-gray-800 line-clamp-1">المغني</h4>
                 <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 whitespace-nowrap">صـ 85</span>
             </div>
             <p class="text-xs text-gray-500 mb-2">لابن قدامة • الفقه الحنبلي</p>
             <div class="text-xs text-gray-600 leading-relaxed font-serif">
                 ...فأما <mark class="bg-yellow-200 text-gray-900 rounded-sm px-0.5">الصلاة</mark> على النبي صلى الله عليه وسلم في التشهد الأول...
             </div>
        </div>

        {{-- Mock Result 3 --}}
        <div class="group relative bg-white hover:bg-gray-50 rounded-lg p-3 cursor-pointer border border-gray-100 hover:border-gray-300 transition-all">
             <div class="flex justify-between items-start mb-1">
                 <h4 class="font-bold text-sm text-gray-800 line-clamp-1">بداية المجتهد ونهاية الموقتصد</h4>
                 <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 whitespace-nowrap">صـ 120</span>
             </div>
             <p class="text-xs text-gray-500 mb-2">لابن رشد الحفيد • الفقه المالكي</p>
             <div class="text-xs text-gray-600 leading-relaxed font-serif">
                 ...واختلفوا في <mark class="bg-yellow-200 text-gray-900 rounded-sm px-0.5">الصلاة</mark> خلف الفاسق والمبتدع...
             </div>
        </div>
        
        {{-- Repeat Mock Results for Scrolling effect --}}
        @for($i = 0; $i < 10; $i++)
        <div class="group relative bg-white hover:bg-gray-50 rounded-lg p-3 cursor-pointer border border-gray-100 hover:border-gray-300 transition-all">
             <div class="flex justify-between items-start mb-1">
                 <h4 class="font-bold text-sm text-gray-800 line-clamp-1">الكتب الستة</h4>
                 <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 whitespace-nowrap">صـ {{ rand(100, 900) }}</span>
             </div>
             <p class="text-xs text-gray-500 mb-2">صحيح البخاري • الحديث</p>
             <div class="text-xs text-gray-600 leading-relaxed font-serif">
                 ...باب فضل <mark class="bg-yellow-200 text-gray-900 rounded-sm px-0.5">الصلاة</mark> في مسجد مكة والمدينة...
             </div>
        </div>
        @endfor

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #d1d5db;
    }
</style>
