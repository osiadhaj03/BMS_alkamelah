<div class="flex-1 bg-white overflow-y-auto">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Book Content -->
        <div class="prose prose-lg max-w-none" dir="rtl" style="font-family: Tajawal;">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-green-700">
                    {{ $book->title }}
                </h2>
                <div class="text-sm text-gray-600">
                    صفحة {{ $currentPage }} من {{ $totalPages }}
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="leading-loose text-gray-800 text-lg">
                {!! nl2br(e($content)) !!}
                
                <!-- Sample Arabic content for demonstration -->
                <p class="mb-4">
                    بسم الله الرحمن الرحيم. الحمد لله رب العالمين، والصلاة والسلام على أشرف الأنبياء والمرسلين، نبينا محمد وعلى آله وصحبه أجمعين.
                </p>
                
                <p class="mb-4">
                    أما بعد: فإن من أعظم ما يحتاج إليه طالب العلم معرفة آداب الفتوى والمفتي والمستفتي، وذلك لأن الفتوى أمر عظيم، وشأن خطير، إذ هي توقيع عن الله تبارك وتعالى.
                </p>
                
                <p class="mb-4">
                    وقد اعتنى العلماء بهذا الجانب عناية فائقة، وألفوا في آداب الفتوى مؤلفات كثيرة، منها ما هو مستقل، ومنها ما هو ضمن مؤلفات أخرى في أصول الفقه أو غيرها.
                </p>
                
                <h3 class="text-xl font-bold text-green-700 mt-8 mb-4">الباب الأول: في آداب المفتي</h3>
                
                <p class="mb-4">
                    ينبغي للمفتي أن يتحلى بآداب عديدة، منها ما يتعلق بعلمه وفقهه، ومنها ما يتعلق بأخلاقه وسلوكه، ومنها ما يتعلق بطريقة إفتائه وأسلوبه في التعامل مع المستفتين.
                </p>
                
                <p class="mb-4">
                    فمن الآداب المتعلقة بالعلم: أن يكون عالماً بأحكام الشريعة، عارفاً بأدلتها من الكتاب والسنة والإجماع والقياس، متمكناً من فهم النصوص وتطبيقها على الوقائع والنوازل.
                </p>
            </div>
            
            <!-- Page Navigation -->
            <div class="flex justify-between items-center mt-12 pt-6 border-t border-gray-200">
                <div class="flex items-center gap-4">
                    @if($previousPage)
                        <a href="{{ route('book.read', ['book' => $book->id, 'page' => $previousPage]) }}" 
                           class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span style="font-family: Tajawal;">الصفحة السابقة</span>
                        </a>
                    @endif
                    
                    @if($nextPage)
                        <a href="{{ route('book.read', ['book' => $book->id, 'page' => $nextPage]) }}" 
                           class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <span style="font-family: Tajawal;">الصفحة التالية</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endif
                </div>
                
                <!-- Page Counter -->
                <div class="text-center">
                    <div class="text-lg font-bold text-green-700" style="font-family: Tajawal;">
                        {{ $currentPage }}
                    </div>
                    <div class="text-xs text-gray-500" style="font-family: Tajawal;">
                        صفحة
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>