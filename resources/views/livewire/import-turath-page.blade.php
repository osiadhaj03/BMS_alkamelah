<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4 text-right" dir="rtl"
    @if($batchMode && ($isImporting || $readyForNextBook || ($currentBatchIndex > 0 && $currentBatchIndex < count($batchBooks))))
        wire:poll.1s="pollBatchProgress"
    @elseif($isImporting)
        wire:poll.750ms="importBatch"
    @endif>
    <div class="w-full max-w-3xl bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
            <h1 class="text-xl font-bold text-gray-800">
                {{ $batchMode ? 'استيراد كتب متعددة' : 'استيراد كتاب من تراث' }}
            </h1>
            <div class="p-2 bg-emerald-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <!-- Status Message -->
            @if($statusMessage)
                <div
                    class="p-4 rounded-md {{ Str::contains($statusMessage, 'فشل') || Str::contains($statusMessage, 'خطأ') ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700' }}">
                    {{ $statusMessage }}
                </div>
            @endif

            <!-- Mode Toggle -->
            <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-200">
                <span class="text-sm font-medium text-gray-700">وضع الاستيراد:</span>
                <div class="flex gap-2">
                    <button wire:click="$set('batchMode', false)"
                        class="px-4 py-2 text-sm rounded-md transition-colors {{ !$batchMode ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        {{ $isImporting ? 'disabled' : '' }}>
                        كتاب واحد
                    </button>
                    <button wire:click="$set('batchMode', true)"
                        class="px-4 py-2 text-sm rounded-md transition-colors {{ $batchMode ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        {{ $isImporting ? 'disabled' : '' }}>
                        كتب متعددة
                    </button>
                </div>
            </div>

            <!-- Single Book Mode -->
            @if(!$batchMode)
                <div class="space-y-4">
                    <div>
                        <label for="bookUrl" class="block text-sm font-medium text-gray-700 mb-1">رابط الكتاب أو المعرف</label>
                        <input type="text" wire:model="bookUrl" id="bookUrl"
                            class="block w-full px-4 py-3 border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            placeholder="https://app.turath.io/book/147927 أو 147927" {{ $isImporting ? 'disabled' : '' }}>
                        @error('bookUrl') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sectionId" class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                        <select wire:model="sectionId" id="sectionId"
                            class="block w-full py-3 pr-4 pl-10 border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            {{ $isImporting ? 'disabled' : '' }}>
                            <option value="">-- اختر القسم --</option>
                            @foreach($sections as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="skipPages"
                                class="rounded border-gray-300 text-emerald-600" {{ $isImporting ? 'disabled' : '' }}>
                            <span class="mr-2 text-sm text-gray-600">استيراد الفهرس فقط</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="forceReimport"
                                class="rounded border-gray-300 text-emerald-600" {{ $isImporting ? 'disabled' : '' }}>
                            <span class="mr-2 text-sm text-gray-600">تحديث الكتاب الموجود</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button wire:click="startImport" {{ $isImporting ? 'disabled' : '' }}
                            class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-md hover:bg-emerald-700 disabled:opacity-50 transition-colors">
                            <span wire:loading.remove wire:target="startImport">بدء الاستيراد</span>
                            <span wire:loading wire:target="startImport">جاري البدء...</span>
                        </button>
                        <button wire:click="previewBook" {{ $isImporting ? 'disabled' : '' }}
                            class="px-6 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 disabled:opacity-50 transition-colors">
                            <span wire:loading.remove wire:target="previewBook">معاينة</span>
                            <span wire:loading wire:target="previewBook">جاري المعاينة...</span>
                        </button>
                    </div>
                </div>

                @if($bookInfo)
                    <div class="bg-emerald-50 rounded-lg p-5 border border-emerald-100 text-sm">
                        <h3 class="font-bold text-emerald-800 mb-4 border-b border-emerald-200 pb-2">معاينة بيانات الكتاب</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div><span class="text-emerald-600 text-xs">اسم الكتاب</span><br>{{ $bookInfo['meta']['name'] ?? '-' }}</div>
                            <div><span class="text-emerald-600 text-xs">المؤلف</span><br>{{ $parsedInfo['author_name'] ?? '-' }}</div>
                            <div><span class="text-emerald-600 text-xs">المجلدات</span><br>{{ isset($bookInfo['indexes']['volume_bounds']) ? count($bookInfo['indexes']['volume_bounds']) : '-' }}</div>
                            <div><span class="text-emerald-600 text-xs">الصفحات</span><br>{{ $totalPages }}</div>
                        </div>
                    </div>
                @endif

            @else
                <!-- Batch Mode -->
                <div class="space-y-4">
                    <div>
                        <label for="batchIds" class="block text-sm font-medium text-gray-700 mb-1">
                            أرقام الكتب (ID) - كل رقم في سطر أو مفصولة بفاصلة
                        </label>
                        <textarea wire:model.live="batchIds" id="batchIds" rows="4"
                            class="block w-full pr-4 py-3 border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm font-mono"
                            placeholder="147927
12216
151
8630"
                            {{ $isImporting ? 'disabled' : '' }}></textarea>
                    </div>

                    <div>
                        <label for="sectionId" class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                        <select wire:model="sectionId" id="sectionId"
                            class="block w-full py-3 pr-4 pl-10 border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            {{ $isImporting ? 'disabled' : '' }}>
                            <option value="">-- اختر القسم --</option>
                            @foreach($sections as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="skipPages"
                                class="rounded border-gray-300 text-emerald-600" {{ $isImporting ? 'disabled' : '' }}>
                            <span class="mr-2 text-sm text-gray-600">استيراد الفهرس فقط</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="forceReimport"
                                class="rounded border-gray-300 text-emerald-600" {{ $isImporting ? 'disabled' : '' }}>
                            <span class="mr-2 text-sm text-gray-600">تحديث الكتب الموجودة</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        @if(empty($batchBooks))
                            <button wire:click="loadBatchBooks" {{ empty($batchIds) || $isImporting ? 'disabled' : '' }}
                                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors">
                                <span wire:loading.remove wire:target="loadBatchBooks">📚 تحميل الكتب</span>
                                <span wire:loading wire:target="loadBatchBooks">جاري التحميل...</span>
                            </button>
                        @else
                            <button wire:click="startBatchImport" {{ $isImporting || $readyForNextBook ? 'disabled' : '' }}
                                class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-md hover:bg-emerald-700 disabled:opacity-50 transition-colors">
                                <span wire:loading.remove wire:target="startBatchImport">🚀 بدء الاستيراد ({{ count($batchBooks) }} كتاب)</span>
                                <span wire:loading wire:target="startBatchImport">جاري البدء...</span>
                            </button>
                            <button wire:click="resetBatch" {{ $isImporting ? 'disabled' : '' }}
                                class="px-6 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 disabled:opacity-50 transition-colors">
                                إعادة تعيين
                            </button>
                        @endif

                        @if($isImporting || $readyForNextBook)
                            <div class="mr-auto flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="text-sm text-gray-500">جاري الاستيراد... ({{ $currentBatchIndex }}/{{ count($batchBooks) }})</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Batch Books Table -->
                @if(!empty($batchBooks))
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">ID</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">اسم الكتاب</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">المؤلف</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">الصفحات</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($batchBooks as $index => $book)
                                    <tr class="{{ $book['status'] === 'importing' ? 'bg-yellow-50' : ($book['status'] === 'done' ? 'bg-green-50' : ($book['status'] === 'error' ? 'bg-red-50' : ($book['status'] === 'skipped' ? 'bg-gray-50' : ''))) }}">
                                        <td class="px-4 py-2 font-mono text-gray-500">{{ $book['id'] }}</td>
                                        <td class="px-4 py-2 font-medium text-gray-900 max-w-xs truncate" title="{{ $book['name'] }}">{{ Str::limit($book['name'], 35) }}</td>
                                        <td class="px-4 py-2 text-gray-600 max-w-xs truncate" title="{{ $book['author'] }}">{{ Str::limit($book['author'], 25) }}</td>
                                        <td class="px-4 py-2 text-center text-gray-500">{{ $book['pages'] }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($book['status'] === 'pending')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">⏳ انتظار</span>
                                            @elseif($book['status'] === 'importing')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 animate-pulse">🔄 جاري</span>
                                            @elseif($book['status'] === 'done')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✅ تم</span>
                                            @elseif($book['status'] === 'skipped')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600" title="{{ $book['message'] }}">⏭️ تخطي</span>
                                            @elseif($book['status'] === 'error')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700" title="{{ $book['message'] }}">❌ فشل</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($batchCompletedCount > 0 || $batchFailedCount > 0)
                        <div class="flex items-center gap-4 text-sm mt-2">
                            <span class="text-green-600">✅ نجاح: {{ $batchCompletedCount }}</span>
                            <span class="text-red-600">❌ فشل: {{ $batchFailedCount }}</span>
                            <span class="text-gray-500">📊 الإجمالي: {{ count($batchBooks) }}</span>
                        </div>
                    @endif
                @endif
            @endif

            <!-- Progress Bar -->
            @if($progress > 0 || $isImporting)
                <div class="space-y-1">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>التقدم الإجمالي</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif

            <!-- Operations Log -->
            @if(!empty($importLog))
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-2 px-1">
                        <h3 class="text-sm font-semibold text-gray-700">سجل العمليات</h3>
                        <div class="flex gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] text-gray-400 font-mono uppercase tracking-wider">Activity</span>
                        </div>
                    </div>
                    <div class="bg-gray-900 rounded-md p-4 font-mono text-xs h-64 overflow-y-auto" id="log-container">
                        <div class="flex flex-col gap-1">
                            @foreach($importLog as $log)
                                <div class="text-gray-300 border-l-2 border-transparent pl-2 hover:border-emerald-500/30 hover:bg-emerald-900/10 py-0.5 rounded">
                                    <span class="text-emerald-500/50 mr-2">#</span>
                                    {!! nl2br(e($log)) !!}
                                </div>
                            @endforeach
                            @if($isImporting || $readyForNextBook)
                                <div class="text-emerald-500 animate-pulse mt-2 ml-1">▋</div>
                            @endif
                        </div>
                    </div>
                    <script>
                        const logContainer = document.getElementById('log-container');
                        if (logContainer) {
                            const observer = new MutationObserver(() => logContainer.scrollTop = logContainer.scrollHeight);
                            observer.observe(logContainer, { childList: true, subtree: true });
                            logContainer.scrollTop = logContainer.scrollHeight;
                        }
                    </script>
                </div>
            @endif
        </div>
    </div>
</div>
