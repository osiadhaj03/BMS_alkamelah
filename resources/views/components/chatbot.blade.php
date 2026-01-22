<div x-data="{ isOpen: false }" class="fixed bottom-6 right-6 z-[9999]" dir="rtl">
    <!-- Chatbot Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-20 right-0 w-[350px] sm:w-[400px] max-h-[600px] bg-white rounded-[2rem] shadow-2xl border border-gray-100 overflow-hidden flex flex-col"
         x-cloak>
        
        <!-- Header -->
        <div class="bg-[#2C6E4A] p-6 text-white flex justify-between items-center relative overflow-hidden">
            <!-- Decorative Pattern (Matching site) -->
            <img src="{{ asset('images/mask-group0.svg') }}" alt="" class="absolute left-0 top-0 w-24 h-24 opacity-20 pointer-events-none">
            
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">مساعد المكتبة الذكي</h3>
                    <p class="text-xs text-white/80">قريبا سيتم تفعيل هذا الميزة</p>
                </div>
            </div>
            
            <button @click="isOpen = false" class="relative z-10 p-2 hover:bg-white/10 rounded-full transition-colors text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#f8faf9] min-h-[300px]">
            <!-- Welcome Message -->
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm flex-shrink-0 animate-bounce">
                    <span class="text-lg">🤖</span>
                </div>
                <div class="bg-white p-4 rounded-2xl rounded-tr-none shadow-sm border border-gray-100 max-w-[80%]">
                    <p class="text-s text-gray-700 leading-relaxed">
                        مرحباً بك في المكتبة الكاملة! أنا مساعدك الذكي. يمكنك الرغبة في البحث عن كتاب معين، أو الاستفسار عن مؤلف، أو تصفح الأقسام. كيف يمكنني خدمتك اليوم؟
                    </p>
                    
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100">
            <form @submit.prevent="" class="relative flex items-center">
                <input type="text" 
                       placeholder="اكتب رسالتك هنا..." 
                       class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#2C6E4A] text-sm text-gray-700 placeholder-gray-400">
                <button type="submit" class="absolute left-2 p-2 bg-[#2C6E4A] text-white rounded-xl hover:bg-[#1a3a2a] transition-all group">
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14"></path>
                    </svg>
                </button>
            </form>
            <p class="text-[10px] text-gray-400 text-center mt-3">المكتبة الكاملة</p>
        </div>
    </div>

    <!-- Floating Action Button (FAB) -->
    <button @click="isOpen = !isOpen" 
            class="group relative w-16 h-16 bg-[#2C6E4A] text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 focus:outline-none focus:ring-4 focus:ring-green-100"
            :class="{ 'rotate-90': isOpen }">
        
        <!-- Pulsing Effect -->
        <div class="absolute inset-0 bg-[#2C6E4A] rounded-full animate-ping opacity-20 pointer-events-none" x-show="!isOpen"></div>

        <!-- Hidden Icon when closed -->
        <svg x-show="!isOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>

        <!-- Close Icon when open -->
        <svg x-show="isOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
