<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div x-data="{
    settingsOpen: false,
    downloadModalOpen: false,
    bookInfoOpen: false,
    fontSize: 18,
    harakatEnabled: true,
    originalContent: '',
    isDownloading: false,
    isNavigating: false,
    
    get result() {
        return $store.search.selectedResult;
    },

    init() {
        // Watch for result changes to update originalContent
        this.$watch('result', (newResult) => {
            if (newResult) {
                this.$nextTick(() => {
                    // حفظ المحتوى الأصلي مباشرة من result
                    this.originalContent = newResult.highlighted_content || newResult.content || '';
                    this.harakatEnabled = true;
                    
                    // تحديث العرض
                    const content = this.$refs.bookContent;
                    if (content) {
                        const contentDiv = content.querySelector('.prose');
                        if (contentDiv) {
                            contentDiv.querySelector('div').innerHTML = this.originalContent;
                        }
                    }
                });
            }
        });
    },

    toggleHarakat() {
        this.harakatEnabled = !this.harakatEnabled;
        const content = this.$refs.bookContent;
        if (!content) return;

        const contentDiv = content.querySelector('.prose div');
        if (!contentDiv) return;

        if (this.harakatEnabled) {
            contentDiv.innerHTML = this.originalContent;
        } else {
            const harakatPattern = /[\u064B-\u065F\u0670]/g;
            contentDiv.innerHTML = this.originalContent.replace(harakatPattern, '');
        }
    },

    async goToPage(pageNumber) {
        if (!this.result || pageNumber < 1 || this.isNavigating) return;
        
        this.isNavigating = true;
        
        try {
            const response = await fetch(`/api/page/${this.result.book_id}/${pageNumber}/full-content`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.page) {
                // تحديث النتيجة في $store
                $store.search.selectedResult = {
                    ...this.result,
                    id: data.page.id,
                    page_number: data.page.page_number,
                    original_page_number: data.page.original_page_number,
                    content: data.page.full_content,
                    highlighted_content: data.page.full_content,
                    book_id: data.page.book_id,
                    book_title: data.page.book_title,
                    volume_title: data.page.volume_title,
                    chapter_title: data.page.chapter_title
                };
            } else {
                alert(data.error || 'فشل في تحميل الصفحة');
            }
        } catch (error) {
            console.error('Error loading page:', error);
            alert('حدث خطأ أثناء تحميل الصفحة: ' + error.message);
        } finally {
            this.isNavigating = false;
        }
    },

    goToPreviousPage() {
        if (!this.result) return;
        this.goToPage(this.result.page_number - 1);
    },

    goToNextPage() {
        if (!this.result) return;
        this.goToPage(this.result.page_number + 1);
    },

    jumpPages(delta) {
        if (!this.result) return;
        this.goToPage(this.result.page_number + delta);
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
        if (!this.result) return;
        
        this.isDownloading = true;
        
        const wrapper = document.createElement('div');
        wrapper.style.position = 'fixed';
        wrapper.style.top = '-9999px';
        wrapper.style.left = '-9999px';
        wrapper.style.width = '800px';
        wrapper.style.backgroundColor = '#fffbf2';
        wrapper.style.padding = '40px';
        wrapper.style.direction = 'rtl';
        wrapper.style.fontFamily = 'serif';
        wrapper.style.color = '#000';
        
        // Header Info
        const headerInfo = document.createElement('div');
        headerInfo.style.borderBottom = '1px solid #ddd';
        headerInfo.style.paddingBottom = '10px';
        headerInfo.style.marginBottom = '20px';
        headerInfo.style.textAlign = 'center';
        headerInfo.style.fontSize = '14px';
        headerInfo.style.color = '#555';
        headerInfo.innerHTML = `
            <span style='font-weight:bold; font-size: 16px; color: #000;'>${this.result.book_title || 'بدون عنوان'}</span> 
            <span> / صفحة ${this.result.page_number || '-'}</span>
        `;
        wrapper.appendChild(headerInfo);

        // Content Clone
        const contentClone = this.$refs.bookContent.cloneNode(true);
        contentClone.style.fontSize = '18px';
        contentClone.style.lineHeight = '2';
        contentClone.style.margin = '0';
        contentClone.style.maxWidth = 'none';
        wrapper.appendChild(contentClone);

        // Footer
        const footerInfo = document.createElement('div');
        footerInfo.style.marginTop = '30px';
        footerInfo.style.paddingTop = '10px';
        footerInfo.style.borderTop = '1px solid #eee';
        footerInfo.style.textAlign = 'center';
        footerInfo.style.fontSize = '12px';
        footerInfo.style.color = '#888';
        footerInfo.innerHTML = 'المكتبة الكاملة - مكتبة تكاملت موضوعاتها وكتبها';
        wrapper.appendChild(footerInfo);

        document.body.appendChild(wrapper);

        try {
            const canvas = await html2canvas(wrapper, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#fffbf2'
            });

            const fileName = `${this.result.book_title || 'page'}-${this.result.page_number || 'unknown'}`;

            if (type === 'image') {
                const link = document.createElement('a');
                link.download = fileName + '.png';
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
                doc.save(fileName + '.pdf');
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
}" 
class="h-full w-full flex flex-col bg-white">

    <!-- Empty State: No result selected -->
    <template x-if="!result">
        <div class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8">
            <svg class="w-20 h-20 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-lg font-medium mb-2">اختر نتيجة لعرضها</p>
            <p class="text-sm text-gray-300">اضغط على أي نتيجة من القائمة لعرض محتواها هنا</p>
        </div>
    </template>

    <!-- Result Content -->
    <template x-if="result">
        <div class="h-full flex flex-col">
            <!-- Breadcrumbs / Info Bar -->
            <div class="flex-none px-6 py-3 border-b border-gray-100 flex justify-between items-center bg-white z-10">
                <div class="flex items-center gap-2 text-sm text-gray-500 flex-wrap">
                    <span @click="bookInfoOpen = true" 
                          class="font-bold text-gray-800 text-[1rem] leading-[1.35rem] cursor-pointer hover:text-green-600 transition-colors underline decoration-dotted decoration-gray-300 hover:decoration-green-600" 
                          x-text="result.book_title || 'بدون عنوان'"></span>
                    
                    <template x-if="result.volume_title">
                        <span class="flex items-center gap-2">
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-600 font-medium" x-text="result.volume_title"></span>
                        </span>
                    </template>
                    
                    <template x-if="result.chapter_title">
                        <span class="flex items-center gap-2">
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-600" x-text="result.chapter_title"></span>
                        </span>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="'/book/' + result.book_id + '/' + result.page_number" 
                       x-show="result.book_id"
                       class="text-xs bg-green-100 hover:bg-green-200 px-3 py-1.5 rounded-full text-green-700 transition-colors">
                        فتح الكتاب الكامل
                    </a>
                </div>
            </div>

            <!-- Content Scroller -->
            <div class="flex-1 overflow-y-auto bg-white">
                <div class="max-w-4xl mx-auto p-4 lg:p-8">
                    
                    <!-- Paper/Card Container -->
                    <div x-ref="bookContent"
                         :style="'font-size: ' + fontSize + 'px'"
                         class="rounded-lg shadow-lg p-6 lg:p-12 relative transition-all duration-200"
                         style="background-color: #f5f1e8; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
                        
                        <!-- Content -->
                        <div class="prose prose-lg max-w-none text-justify leading-loose text-gray-800 font-serif" style="line-height: 2;">
                            <div x-html="result.highlighted_content || result.content || 'لا يوجد محتوى'"></div>
                        </div>

                        <!-- Page Number -->
                        <p class="text-gray-400 text-sm text-center mt-12 mb-4">
                            - صفحة <span x-text="result.page_number || '-'"></span> -
                        </p>
                    </div>
                    
                </div>
            </div>

            <!-- Footer with Controls -->
            <div class="flex-none px-6 py-3 border-t border-gray-200 bg-white flex justify-between items-center relative z-10">
                <!-- أزرار التنقل -->
                <div class="flex items-center gap-2">
                    <!-- القفز -10 صفحات -->
                    <button @click="jumpPages(-10)" 
                            :disabled="isNavigating || !result || result.page_number <= 10"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed" 
                            title="الرجوع 10 صفحات">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    </button>
                    
                    <!-- الصفحة السابقة -->
                    <button @click="goToPreviousPage()" 
                            :disabled="isNavigating || !result || result.page_number <= 1"
                            class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed font-medium" 
                            title="الصفحة السابقة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>السابقة</span>
                    </button>
                    
                    <!-- رقم الصفحة -->
                    <div class="px-4 py-2 bg-gray-50 rounded-lg">
                        <span class="text-sm font-bold text-gray-700">
                            صفحة <span x-text="result.page_number || '-'"></span>
                        </span>
                    </div>
                    
                    <!-- الصفحة التالية -->
                    <button @click="goToNextPage()" 
                            :disabled="isNavigating"
                            class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed font-medium" 
                            title="الصفحة التالية">
                        <span>التالية</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                    
                    <!-- القفز +10 صفحات -->
                    <button @click="jumpPages(10)" 
                            :disabled="isNavigating"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed" 
                            title="التقدم 10 صفحات">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    </button>
                    
                    <!-- Loading Indicator -->
                    <div x-show="isNavigating" class="flex items-center gap-2 text-sm text-green-600">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>جاري التحميل...</span>
                    </div>
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

                            <!-- Harakat Toggle -->
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

            <!-- Book Description Popup - Simple White Box -->
            <div x-show="bookInfoOpen" 
                 class="absolute inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="bookInfoOpen = false"
                 style="display: none;">
                
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[70vh] overflow-hidden"
                     @click.stop>
                    
                    <!-- Simple Header -->
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800" x-text="result.book_title || 'بدون عنوان'"></h3>
                        <button @click="bookInfoOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Description Content -->
                    <div class="p-6 overflow-y-auto max-h-[50vh]">
                        <!-- Description -->
                        <div x-show="result.book_description" class="text-gray-700 leading-relaxed text-justify">
                            <p x-text="result.book_description"></p>
                        </div>

                        <!-- No Description -->
                        <div x-show="!result.book_description" class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-400">لا يوجد وصف متاح لهذا الكتاب</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
