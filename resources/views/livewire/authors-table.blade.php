<div>
    {{-- Search and Filters Row --}}
    @if($showSearch)
        <div class="mb-6">
            {{-- Search Bar with integrated controls --}}
            <div
                class="relative flex items-center bg-white border-2 border-gray-200 rounded-xl focus-within:border-green-500 transition-colors">
                {{-- Search Icon --}}
                <div class="absolute right-4 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Search Input --}}
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="ابحث في أسماء المؤلفين أو سيرهم أو مذاهبهم..."
                    class="flex-1 px-4 py-3 pr-12 text-right bg-transparent border-none rounded-xl focus:outline-none focus:ring-0 text-gray-800 placeholder-gray-400">

                {{-- Controls inside search bar --}}
                <div class="flex items-center gap-1 pl-2 border-r border-gray-200">
                    {{-- Per Page Selector --}}
                    @if($showPerPageSelector)
                        <div class="flex items-center gap-1 px-2 py-1">
                            <select wire:model.live="perPage"
                                class="border-none bg-transparent px-1 py-1 text-sm focus:outline-none focus:ring-0 text-gray-800 font-medium">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    @endif

                    {{-- Filter Button --}}
                    <button wire:click="$toggle('filterModalOpen')"
                        class="flex items-center gap-1 px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-100 rounded-lg transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        <span class="hidden sm:inline text-sm font-medium">تصفية</span>
                        @if($this->getActiveFiltersCount() > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-green-600 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center text-[10px]">
                                {{ $this->getActiveFiltersCount() }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Active Filters Display --}}
            @if(count($madhhabFilters) > 0 || count($centuryFilters) > 0 || $deathDateFrom || $deathDateTo)
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach($madhhabFilters as $mFilter)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            {{ $mFilter }}
                            <button wire:click="toggleMadhhabFilter('{{ $mFilter }}')" class="hover:text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </span>
                    @endforeach
                    @foreach($centuryFilters as $cFilter)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ $availableCenturies[$cFilter] ?? "القرن $cFilter" }}
                            <button wire:click="toggleCenturyFilter({{ $cFilter }})" class="hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </span>
                    @endforeach
                    @if($deathDateFrom || $deathDateTo)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                            {{ $deathDateFrom ?? '...' }} - {{ $deathDateTo ?? '...' }} هـ
                            <button wire:click="clearDateRange" class="hover:text-purple-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    <button wire:click="clearAllFilters" class="text-sm text-red-600 hover:text-red-800 hover:underline">
                        مسح الكل
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- Filter Buttons (Old Style - for homepage) --}}
    @if($showFilters && !$showSearch)
        <div class="flex flex-wrap gap-2 mb-4">
            <button wire:click="$set('madhhab', 'المذهب الحنفي')"
                class="bg-white text-green-800 border border-green-800 px-5 py-2 rounded-full transition-colors duration-300 hover:bg-green-800 hover:text-white {{ $madhhab === 'المذهب الحنفي' ? 'bg-green-800 text-white' : '' }}">
                المؤلفون الحنفيون
            </button>
            <a href="{{ route('authors.index') }}"
                class="bg-white text-green-800 border border-green-800 px-5 py-2 rounded-full transition-colors duration-300 hover:bg-green-800 hover:text-white">
                عرض جميع المؤلفين
            </a>
        </div>
    @endif

    {{-- Authors Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden" wire:loading.class="opacity-50"
        wire:target="search,perPage,previousPage,nextPage,toggleMadhhabFilter,clearAllFilters">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-right font-medium text-gray-900 uppercase tracking-wider w-16"
                            style="font-size: 1rem;">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3 text-right font-medium text-gray-900 uppercase tracking-wider"
                            style="font-size: 1rem;">
                            اسم المؤلف
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center font-medium text-gray-900 uppercase tracking-wider w-28"
                            style="font-size: 1rem;">
                            عدد الكتب
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right font-medium text-gray-900 uppercase tracking-wider w-40"
                            style="font-size: 1rem;">
                            المذهب
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($authors as $index => $author)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-gray-900 w-16" style="font-size: 1.1rem;">
                                {{ ($authors->currentPage() - 1) * $authors->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900" style="font-size: 1.1rem;">
                                    <a href="#"
                                        class="text-green-700 hover:text-green-900 hover:underline transition-colors duration-200">
                                        {!! $this->highlightText($author->full_name, $search) !!}
                                    </a>
                                </div>
                                @if($author->biography)
                                    <div class="text-gray-500 text-sm truncate max-w-xs">
                                        {!! $this->highlightText(Str::limit($author->biography, 50), $search) !!}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center w-28">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full font-medium bg-green-100 text-green-800 text-sm">
                                    {{ $author->books_count }} كتاب
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap w-40">
                                @if($author->madhhab)
                                    <span class="text-gray-700" style="font-size: 1rem;">
                                        {!! $this->highlightText($author->madhhab, $search) !!}
                                    </span>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <p class="font-medium text-gray-900 mb-1" style="font-size: 1.2rem;">لا يوجد مؤلفون
                                        متوفرون</p>
                                    <p class="text-gray-500">
                                        {{ $search ? 'جرب البحث بكلمات أخرى' : 'سيتم إضافة المؤلفين قريباً' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($showPagination && ($authors->hasPages() || $authors->count() > 0))
            <div class="px-6 py-4 flex items-center justify-center border-t border-gray-200 bg-gray-50">
                {{-- Navigation Buttons --}}
                <div class="flex items-center gap-2">
                    @if($authors->hasPages())
                        @if($authors->onFirstPage())
                            <button disabled class="p-2 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @else
                            <button wire:click="previousPage"
                                class="p-2 rounded-full hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @endif

                        <span class="text-sm text-gray-600 mx-2">
                            {{ $authors->firstItem() ?: 0 }}-{{ $authors->lastItem() ?: 0 }} من {{ $authors->total() }}
                        </span>

                        @if($authors->hasMorePages())
                            <button wire:click="nextPage"
                                class="p-2 rounded-full hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </button>
                        @else
                            <button disabled class="p-2 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Filter Modal --}}
    @if($filterModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                wire:click="$set('filterModalOpen', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl transition-all w-full max-w-md flex flex-col max-h-[70vh]">

                    {{-- Header --}}
                    <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold leading-6 text-gray-900">تصفية حسب المذهب</h3>
                            <button wire:click="$set('filterModalOpen', false)"
                                class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                        <ul class="space-y-1">
                            @foreach($this->getFilteredMadhhabs() as $m)
                                <li class="relative flex items-start py-2 px-4 hover:bg-white hover:shadow-sm rounded-lg transition-all cursor-pointer"
                                    wire:click="toggleMadhhabFilter('{{ $m }}')">
                                    <div class="min-w-0 flex-1 text-sm">
                                        <label class="select-none font-medium text-gray-900 cursor-pointer">{{ $m }}</label>
                                    </div>
                                    <div class="mr-3 flex h-6 items-center">
                                        <div
                                            class="relative flex items-center justify-center w-5 h-5 border rounded transition-colors {{ in_array($m, $madhhabFilters) ? 'bg-green-600 border-green-600' : 'bg-white border-gray-300' }}">
                                            @if(in_array($m, $madhhabFilters))
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-white px-6 py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                        <button type="button"
                            class="inline-flex justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors"
                            wire:click="$set('filterModalOpen', false)">
                            تطبيق
                        </button>
                        <button type="button"
                            class="inline-flex justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                            wire:click="$set('filterModalOpen', false)">
                            إلغاء
                        </button>
                        <button type="button" class="mr-auto text-sm text-gray-500 hover:text-red-600 transition-colors"
                            wire:click="clearAllFilters">
                            مسح الكل
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>