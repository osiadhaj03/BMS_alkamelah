<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div x-data="{
    settingsOpen: false,
    downloadModalOpen: false,
    fontSize: 18,
    harakatEnabled: true,
    originalContent: '',
    isDownloading: false,
    
    init() {
        // Initialize content for Harakat toggling
        this.$nextTick(() => {
            const content = this.$refs.bookContent;
            if (content) {
                this.originalContent = content.innerHTML;
            }
        });
    },

    toggleHarakat() {
        this.harakatEnabled = !this.harakatEnabled;
        const content = this.$refs.bookContent;
        if (!content) return;

        if (this.harakatEnabled) {
            content.innerHTML = this.originalContent;
        } else {
            const harakatPattern = /[\u064B-\u065F\u0670]/g;
            content.innerHTML = this.originalContent.replace(harakatPattern, '');
        }
    },

    changeFontSize(delta) {
        this.fontSize = Math.max(12, Math.min(32, this.fontSize + delta));
    },

    toggleFullscreen() {
        if (!document.fullscreenElement) {
            this.$el.requestFullscreen().catch(err => {
                console.error(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
            });
        } else {
            document.exitFullscreen();
        }
    },

    async downloadPage(type) {
        this.isDownloading = true;
        
        // Create a wrapper for capture
        const wrapper = document.createElement('div');
        wrapper.style.position = 'fixed';
        wrapper.style.top = '-9999px';
        wrapper.style.left = '-9999px';
        wrapper.style.width = '800px'; // Fixed width for consistent output
        wrapper.style.backgroundColor = '#ffffff';
        wrapper.style.padding = '40px';
        wrapper.style.direction = 'rtl';
        wrapper.style.fontFamily = 'serif';
        wrapper.style.color = '#000';
        
        // 1. Header Info
        const headerInfo = document.createElement('div');
        headerInfo.style.borderBottom = '1px solid #ddd';
        headerInfo.style.paddingBottom = '10px';
        headerInfo.style.marginBottom = '20px';
        headerInfo.style.textAlign = 'center';
        headerInfo.style.fontSize = '14px';
        headerInfo.style.color = '#555';
        headerInfo.innerHTML = `
            <span style='font-weight:bold; font-size: 16px; color: #000;'>المجموع شرح المهذب</span> 
            <span> / المجلد الرابع / كتاب الصلاة</span>
        `;
        wrapper.appendChild(headerInfo);

        // 2. Content (Clone)
        const contentClone = this.$refs.bookContent.cloneNode(true);
        contentClone.style.fontSize = '18px'; // Enforce readable size for export
        contentClone.style.lineHeight = '2';
        contentClone.style.margin = '0';
        contentClone.style.maxWidth = 'none';
        
        // Ensure highlights are visible (html2canvas handles bg colors well)
        wrapper.appendChild(contentClone);

        // 3. Footer (Page Number)
        const footerInfo = document.createElement('div');
        footerInfo.style.marginTop = '30px';
        footerInfo.style.paddingTop = '10px';
        footerInfo.style.borderTop = '1px solid #eee';
        footerInfo.style.textAlign = 'center';
        footerInfo.style.fontSize = '12px';
        footerInfo.style.color = '#888';
        footerInfo.innerHTML = 'المكتبة الكاملة - مكتبة تكاملت موضوعاتها وكتبها  ';
        wrapper.appendChild(footerInfo);

        document.body.appendChild(wrapper);

        try {
            const canvas = await html2canvas(wrapper, {
                scale: 2, // High resolution
                useCORS: true,
                backgroundColor: '#ffffff'
            });

            if (type === 'image') {
                const link = document.createElement('a');
                link.download = 'book-page-412.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            } else if (type === 'pdf') {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');
                const imgData = canvas.toDataURL('image/png');
                const imgProps = doc.getImageProperties(imgData);
                const pdfWidth = doc.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                
                doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                doc.save('book-page-412.pdf');
            }
        } catch (error) {
            console.error('Download failed:', error);
            alert('حدث خطأ أثناء التنزيل, يرجى المحاولة مرة أخرى.');
        } finally {
            document.body.removeChild(wrapper);
            this.isDownloading = false;
            this.downloadModalOpen = false;
        }
    }
    }
}" 
class="h-full w-full flex flex-col bg-white">
    <!-- Breadcrumbs / Info Bar (Fixed Top) -->
    <div class="flex-none px-6 py-3 border-b border-gray-100 flex justify-between items-center bg-white z-10">
        <div class="flex items-baseline gap-2 text-sm text-gray-500">
            <span class="font-bold text-gray-800 text-[1rem] leading-[1.35rem]">المجموع شرح المهذب</span>
            <span class="text-gray-300">/</span>
            <span>المجلد الرابع</span>
            <span class="text-gray-300">/</span>
            <span>كتاب الصلاة</span>
        </div>
        <div class="flex items-center gap-2">
            <button class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-full text-gray-600 transition-colors">
                فتح الكتاب الكامل
            </button>
        </div>
    </div>

    <!-- Content Scroller (Main Area) -->
    <div class="flex-1 overflow-y-auto bg-white" style="background-color: #ffffff;"> <!-- Explicit White Background as requested -->
        <div class="max-w-4xl mx-auto p-4 lg:p-8">
            
            <!-- Paper/Card Container -->
            <div x-ref="bookContent"
                 :style="'font-size: ' + fontSize + 'px'"
                 class="rounded-lg shadow-lg p-6 lg:p-12 relative transition-all duration-200"
                 style="background-color: #ffffff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
                
                <!-- Content -->
                <div class="prose prose-lg max-w-none text-justify leading-loose text-gray-800 font-serif space-y-6" style="line-height: 2;">
                    
                    <h2 class="text-center font-bold text-[1.5em] mb-8 text-black" style="font-family: inherit;">باب صفة الصلاة</h2>
    
                    <p>
                        وسننها قبل الدخول فيها شيئان : الأذان والإقامة ، وبعد الدخول فيها شيئان : التشهد الأول والقنوت في الصبح ، وفي الوتر في النصف الثاني من شهر رمضان . وهيئات : وهي ما عدا ذلك .
                        فأما <mark class="bg-yellow-200 text-black px-1 rounded">الصلاة</mark> فقيل : هي الدعاء . وقيل : الرحمة . وقيل : التبريك .
                    </p>
    
                    <p>
                        قال الله تعالى : <span class="text-green-800 font-bold">{ وصل عليهم إن صلاتك سكن لهم }</span> [ التوبة : 103 ] أي : ادع لهم . وقال النبي صلى الله عليه وسلم : ( إذا دعي أحدكم إلى طعام فليجب ، فإن كان مفطرا فليأكل ، وإن كان صائما فليصل ) أي : فليدع . وقالت العرب : صلى ، إذا دعا .
                    </p>
    
                    <div class="my-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <p class="text-gray-600 text-[0.9em] italic text-center">
                            "مسألة : حكم تارك <mark class="bg-yellow-200 text-black px-1 rounded">الصلاة</mark>"
                        </p>
                    </div>
    
                    <p>
                        وأما في الشرع : فهي أقوال وأفعال مفتتحة بالتكبير ، مختتمة بالتسليم ، بشرائط مخصوصة . 
                        واختلف أصحابنا في تسميتها <mark class="bg-yellow-200 text-black px-1 rounded">صلاة</mark> : هل هو منقول أو مبقى على موضوعه في اللغة ؟
                        فمنهم من قال : هو مبقى على موضوعه في اللغة ; لأن <mark class="bg-yellow-200 text-black px-1 rounded">الصلاة</mark> تشتمل على الدعاء ، فسميت باسم ما تشتمل عليه .
                        ومنهم من قال : هو منقول ; لأن الاسم يتناول الدعاء وغيره ، فصار اسما لهذه العبادة ، كالدابة : كانت اسما لكل ما يدب ، فصارت اسما لبعض ما يدب . وهو الصحيح ; لأن الاسم الشرعي إذا أمكن إثباته لا يصار إلى اللغوي .
                    </p>
    
                    <p class="text-gray-400 text-sm text-center mt-12 mb-4">- 412 -</p>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Reading Progress / Footer (Fixed Bottom) -->
    <div class="flex-none px-6 py-3 border-t border-gray-200 bg-white flex justify-between items-center relative z-10">
        <div class="flex items-center gap-4">
            <button class="p-2 hover:bg-gray-100 rounded-full text-gray-500 disabled:opacity-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <span class="text-sm font-medium text-gray-600">صفحة 412</span>
            <button class="p-2 hover:bg-gray-100 rounded-full text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
        </div>

        <div class="flex gap-2">
            <!-- Text Settings Dropdown -->
            <div class="relative">
                <button @click="settingsOpen = !settingsOpen" 
                        @click.outside="settingsOpen = false"
                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors" 
                        title="تنسيقات النص"
                        :class="{'bg-gray-100 text-green-600': settingsOpen}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="settingsOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-full left-0 mb-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-20 flex flex-col p-2">
                    
                    <div class="px-2 py-1 text-xs font-bold text-gray-400 mb-1">العرض</div>

                    <!-- Font Size -->
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 mb-2">
                        <button @click="changeFontSize(-2)" class="p-1 hover:bg-white rounded shadow-sm text-gray-600 disabled:opacity-50" :disabled="fontSize <= 12">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </button>
                        <span class="text-sm font-bold text-gray-700 select-none">حجم الخط</span>
                        <button @click="changeFontSize(2)" class="p-1 hover:bg-white rounded shadow-sm text-gray-600 disabled:opacity-50" :disabled="fontSize >= 32">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>

                    <!-- Harakat Toggle (Button Style) -->
                    <button @click="toggleHarakat()" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors w-full text-right group">
                         <div class="p-1.5 rounded-md bg-gray-100 text-gray-500 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/><circle cx="8" cy="4" r="1.5" fill="currentColor"/><circle cx="16" cy="10" r="1.5" fill="currentColor"/><circle cx="12" cy="16" r="1.5" fill="currentColor"/></svg>
                         </div>
                         <span class="font-medium text-sm" x-text="harakatEnabled ? 'إخفاء الحركات' : 'إظهار الحركات'"></span>
                    </button>

                    <div class="my-1 border-t border-gray-100"></div>

                    <!-- Fullscreen -->
                    <button @click="toggleFullscreen(); settingsOpen = false" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors w-full text-right group">
                         <div class="p-1.5 rounded-md bg-gray-100 text-gray-500 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                         </div>
                         <span class="font-medium text-sm">ملء الشاشة</span>
                    </button>
                </div>
            </div>

            <button @click="downloadModalOpen = true" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded" title="تنزيل الصفحة">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </button>
        </div>
    </div>

    <!-- Download Options Modal -->
    <div x-show="downloadModalOpen" 
         class="absolute inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden"
             @click.outside="if(!isDownloading) downloadModalOpen = false">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800">تنزيل الصفحة</h3>
                <button @click="downloadModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-3">
                <p class="text-sm text-gray-500 mb-4 text-center">اختر الصيغة المناسبة لتنزيل الصفحة الحالية مع التظليل:</p>
                
                <button @click="downloadPage('image')" 
                        :disabled="isDownloading"
                        class="w-full flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 text-green-600 rounded-lg group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">صورة (PNG)</div>
                            <div class="text-xs text-gray-500">جودة عالية، مناسبة للمشاركة</div>
                        </div>
                    </div>
                </button>

                <button @click="downloadPage('pdf')" 
                        :disabled="isDownloading"
                        class="w-full flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-red-500 hover:bg-red-50 transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 text-red-600 rounded-lg group-hover:bg-red-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">ملف PDF</div>
                            <div class="text-xs text-gray-500">مناسبة للطباعة</div>
                        </div>
                    </div>
                </button>
                
                <div x-show="isDownloading" class="flex justify-center items-center gap-2 text-sm text-green-600 mt-2 font-medium">
                    <svg class="animate-spin h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>جاري تحضير الملف...</span>
                </div>
            </div>
        </div>
    </div>
</div>
