<div class="min-h-screen bg-gray-50 flex flex-col" dir="rtl">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Title --}}
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">استيراد من جامع الكتب الإسلامية</h1>
                        <p class="text-xs text-gray-500 font-medium">نظام سحب الكتب الذكي KetabOnline Scraper v1.0</p>
                    </div>
                </div>

                {{-- Status Badges --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center px-3 py-1 rounded-full bg-blue-50 border border-blue-100">
                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse ml-2"></div>
                        <span class="text-xs font-semibold text-blue-700">النظام جاهز</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Import Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="max-w-2xl mx-auto space-y-6">

                    {{-- Instruction Box --}}
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800 leading-relaxed">
                            <span class="font-bold block mb-1">تعليمات الاستخدام:</span>
                            أدخل رابط الكتاب من موقع جامع الكتب الإسلامية (KetabOnline.com) أو رقم الكتاب (ID) فقط.
                            <br>
                            <span class="text-blue-600/80 text-xs mt-1 block ltr">مثال: https://ketabonline.com/ar/books/3501 أو 3501</span>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form wire:submit.prevent="startImport" class="space-y-4">
                        <label for="book_id" class="block text-sm font-semibold text-gray-700">رابط الكتاب أو المعرف (Book ID)</label>
                        <div class="relative group">
                            <input
                                type="text"
                                wire:model="bookId"
                                id="book_id"
                                placeholder="أدخل الرابط أو المعرف هنا..."
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none group-hover:bg-white"
                                {{ $isImporting ? 'disabled' : '' }}
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400">🌐</span>
                            </div>
                        </div>
                        @error('bookId') <span class="text-red-500 text-sm font-medium">{{ $message }}</span> @enderror

                        <button
                            type="submit"
                            class="w-full relative flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform active:scale-[0.99]"
                            {{ $isImporting ? 'disabled' : '' }}
                        >
                            <span class="flex items-center gap-2">
                                @if($isImporting)
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    جاري الاستيراد...
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    بدء الاستيراد
                                @endif
                            </span>
                        </button>
                    </form>

                </div>
            </div>
        </div>

        {{-- Logs Console --}}
        @if(count($logs) > 0 || $processedPages > 0)
        <div class="bg-[#1e1e1e] rounded-xl shadow-lg border border-gray-800 overflow-hidden flex flex-col font-mono text-sm"
             x-data="{ autoScroll: true }"
             x-init="$watch('autoScroll', value => value && $refs.logs.scrollTo(0, $refs.logs.scrollHeight))">
            {{-- Console Header --}}
            <div class="bg-[#2d2d2d] px-4 py-2 flex items-center justify-between border-b border-gray-700">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="ml-2 text-gray-400 text-xs font-semibold tracking-wider">TERMINAL OUTPUT</span>
                </div>
                <button
                    @click="autoScroll = !autoScroll"
                    class="text-xs px-2 py-1 rounded hover:bg-gray-700 transition-colors"
                    :class="autoScroll ? 'text-green-400' : 'text-gray-500'"
                >
                    {{-- Icon for Auto-scroll --}}
                    <span x-show="autoScroll">● Auto-scroll ON</span>
                    <span x-show="!autoScroll">○ Auto-scroll OFF</span>
                </button>
            </div>

            {{-- Logs Container --}}
            <div
                x-ref="logs"
                class="flex-1 p-4 overflow-y-auto space-y-1 max-h-[500px] scroll-smooth custom-scrollbar"
            >
                @foreach($logs as $log)
                    <div class="flex gap-2 animate-fade-in-up">
                        <span class="text-gray-500 select-none">[{{ $log['time'] }}]</span>
                        <span class="{{ $log['type'] === 'error' ? 'text-red-400 font-bold' : ($log['type'] === 'success' ? 'text-green-400 font-bold' : 'text-gray-300') }}">
                            @if($log['type'] === 'success') ✓ @endif
                            @if($log['type'] === 'error') ✗ @endif
                            @if($log['type'] === 'info') ℹ @endif
                            {!! $log['message'] !!}
                        </span>
                    </div>
                @endforeach
                <div id="logs-end"></div>
            </div>

            {{-- Progress Bar Footer --}}
            <div class="bg-[#252526] p-4 border-t border-gray-800">
                <div class="flex justify-between text-xs text-gray-400 mb-2 font-sans">
                    <span>التقدم الكلي</span>
                    <span>{{ number_format(($processedPages / max($totalPages, 1)) * 100, 1) }}% ({{ $processedPages }} / {{ $totalPages }})</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2.5 overflow-hidden">
                    <div
                        class="bg-blue-500 h-2.5 rounded-full transition-all duration-300 ease-out relative overflow-hidden"
                        style="width: {{ ($processedPages / max($totalPages, 1)) * 100 }}%"
                    >
                        <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite_linear]"
                             style="background-image: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%); transform: skewX(-20deg);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { bg-color: #1e1e1e; }
    .custom-scrollbar::-webkit-scrollbar-thumb { bg-color: #4a4a4a; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { bg-color: #5a5a5a; }
    @keyframes shimmer {
        0% { transform: translateX(-150%) skewX(-20deg); }
        100% { transform: translateX(150%) skewX(-20deg); }
    }
</style>
