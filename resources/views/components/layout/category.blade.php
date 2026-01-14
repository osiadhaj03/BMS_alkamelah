<!-- Book Categories-->
<div class="relative overflow-hidden bg-[#fafafa]" id="book-sections">
    <!-- Section Background Pattern -->
    <div class="absolute inset-0 pointer-events-none"
        style="background-image: url('{{ asset('assets/Frame 1321314420.png') }}'); background-repeat: repeat; background-size: 800px; background-attachment: fixed;">
    </div>

    <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20" dir="rtl">
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center">
                    <img src="{{ asset('images/group0.svg') }}" alt="Icon" class="w-full h-full object-contain">
                </div>
                <h2 class="text-3xl md:text-5xl font-extrabold text-[#1a3a2a]">أقسام الكتب</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @forelse ($sections as $section)
                <a href="{{ route('books.index', ['section' => $section->id]) }}"
                    class="group bg-white rounded-[2rem] shadow-xl shadow-green-900/5 overflow-hidden border border-gray-100 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-green-900/10 relative">

                    <!-- Decorative Mask Pattern -->
                    <img src="{{ asset('images/mask-group0.svg') }}" alt=""
                        class="absolute left-0 top-0 w-32 h-32 group-hover: pointer-events-none">

                    <div class="p-8 relative z-10">
                        <div class="flex items-center gap-6">
                            <!-- Category Icon/Logo -->
                            <div
                                class="w-20 h-20 flex-shrink-0 flex items-center justify-center bg-[#f8faf9] rounded-2xl group-hover:bg-[#e8f5e9] transition-colors p-3">
                                <img src="{{ $section->logo_url }}" alt="{{ $section->name }}"
                                    class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
                            </div>

                            <!-- Category Details -->
                            <div>
                                <h3
                                    class="text-xl md:text-2xl font-bold text-[#1a3a2a] group-hover:text-[#2C6E4A] transition-colors mb-2">
                                    {{ $section->name }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-[#e8f5e9] text-[#2e7d32]">
                                        {{ $section->books_count ?? 0 }} كتاب
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <div class="flex flex-col items-center gap-4">
                        <p class="text-gray-400 text-lg">لا توجد أقسام متوفرة حالياً</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Action Button -->
        <div class="flex justify-center">
            <a href="{{ route('categories.index') }}"
                class="group relative px-10 py-4 bg-white border-2 border-[#2C6E4A] text-[#2C6E4A] rounded-full font-bold text-lg overflow-hidden transition-all hover:text-white">
                <div
                    class="absolute inset-0 bg-[#2C6E4A] translate-y-full group-hover:translate-y-0 transition-transform duration-300 -z-10">
                </div>
                <span>عرض جميع الأقسام</span>
            </a>
        </div>
    </section>
</div>