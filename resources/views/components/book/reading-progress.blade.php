<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

@props([
    'currentPageNum' => 1,
    'totalPages' => 1,
    'book' => null,
    'currentPage' => null,
    'nextPage' => null,
    'previousPage' => null
])

@php
    $progress = $totalPages > 0 ? round(($currentPageNum / $totalPages) * 100) : 0;
    $volumes = $book?->volumes ?? collect();
@endphp

<div class="fixed bottom-4 left-4 z-30">
    <style>
        .reading-menu-dropdown {
            position: absolute;
            bottom: 100%;
            left: 0;
            background: var(--bg-paper);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-main);
            box-shadow: var(--shadow-dropdown);
            min-width: 200px;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            display: none;
            flex-direction: column;
            gap: 0.25rem;
            z-index: 40;
        }
        .reading-menu-dropdown.show {
            display: flex;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            color: var(--text-main);
            font-family: var(--font-ui);
            font-size: 0.9rem;
            transition: all 0.2s;
            background: transparent;
            width: 100%;
            text-align: right;
            border: none;
            cursor: pointer;
        }
        .menu-item:hover {
            background-color: var(--bg-hover);
            color: var(--accent-color);
        }
        .menu-item svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }
        .menu-divider {
            height: 1px;
            background: var(--border-color);
            margin: 0.25rem 0;
        }
    </style>
    
    <div class="relative">
        <div class="reading-menu-dropdown" id="reading-menu">
             <!-- Volume Selector (if book has volumes) -->
             @if($volumes->isNotEmpty())
                 <div class="lg:hidden pb-2 mb-2 border-b border-gray-200" style="border-color: var(--border-color);">
                    <div style="padding: 5px 10px; font-weight: bold; color: var(--text-muted); font-size: 0.75rem;">المجلد</div>
                    <select class="w-full bg-transparent text-sm p-2 rounded cursor-pointer" 
                            style="font-family: var(--font-ui); color: var(--text-main); text-align: right; border: 1px solid var(--border-color);"
                            id="mobile-part-selector"
                            onchange="window.location.href='/book/{{ $book?->id }}/' + this.selectedOptions[0].dataset.page">
                        @foreach($volumes as $vol)
                            <option value="{{ $vol->id }}" data-page="{{ $vol->pages()->min('page_number') ?? 1 }}">
                                {{ $vol->display_name ?? 'المجلد ' . $vol->number }}
                            </option>
                        @endforeach
                    </select>
                 </div>
             @endif

             <div style="padding: 5px 10px; font-weight: bold; color: var(--text-muted); font-size: 0.75rem;">العرض</div>
            
            <button class="menu-item" id="menu-increase">
                <svg viewBox="0 0 24 24"><path d="M12 4V20M4 12H20"/></svg>
                <span>تكبير الخط</span>
            </button>
            <button class="menu-item" id="menu-decrease">
                <svg viewBox="0 0 24 24"><path d="M5 12H19"/></svg>
                <span>تصغير الخط</span>
            </button>
            
            <button class="menu-item" id="toggle-harakat">
                <svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/><circle cx="8" cy="4" r="1.5" fill="currentColor"/><circle cx="16" cy="10" r="1.5" fill="currentColor"/><circle cx="12" cy="16" r="1.5" fill="currentColor"/></svg>
                <span id="harakat-text">إخفاء الحركات</span>
            </button>
            
            <div class="menu-divider"></div>
            
            <button class="menu-item" id="fullscreen-btn">
                <svg viewBox="0 0 24 24" id="fullscreen-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                <span id="fullscreen-text">توسيع الشاشة</span>
            </button>

            <div class="menu-divider"></div>
            
            <button class="menu-item" id="menu-download">
                <svg viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>تنزيل الصفحة</span>
            </button>
            
            <button class="menu-item" id="menu-share" onclick="navigator.share?.({title: '{{ $book?->title }}', url: window.location.href})">
                <svg viewBox="0 0 24 24"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                <span>مشاركة الكتاب</span>
            </button>
        </div>
    </div>
    
    <div class="flex items-center gap-3 px-4 py-2 rounded-full shadow-lg" 
         style="background-color: var(--bg-sidebar); border: 1px solid var(--border-color); box-shadow: var(--shadow-soft);">
        
        <!-- Circular Progress Indicator -->
        <div class="relative w-10 h-10">
            <svg class="w-10 h-10 transform -rotate-90" viewBox="0 0 36 36">
                <!-- Background Circle -->
                <path class="text-gray-200"
                      stroke="currentColor"
                      stroke-width="3"
                      fill="none"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                
                <!-- Progress Circle -->
                @php
                    $circumference = 2 * 3.14159 * 15.9155;
                    $strokeDasharray = ($progress / 100) * $circumference . ', ' . $circumference;
                @endphp
                <path class="transition-all duration-300"
                      stroke="var(--accent-color)"
                      stroke-width="3"
                      stroke-linecap="round"
                      fill="none"
                      stroke-dasharray="{{ $strokeDasharray }}"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                      id="progress-circle"/>
            </svg>
            
            <!-- Percentage Text -->
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-xs font-bold" style="color: var(--accent-color); font-family: var(--font-ui);" id="progress-percentage">
                    {{ $progress }}%
                </span>
            </div>
        </div>
        
        <!-- Volume Selector (Desktop) -->
        @if($volumes->isNotEmpty())
            <div class="hidden lg:block border-l border-gray-200 pl-3 ml-1" style="border-color: var(--border-color);">
                <select class="bg-transparent text-sm focus:outline-none cursor-pointer" 
                        style="font-family: var(--font-ui); color: var(--text-main);"
                        id="part-selector"
                        onchange="window.location.href='/book/{{ $book?->id }}/' + this.selectedOptions[0].dataset.page">
                    @foreach($volumes as $vol)
                        <option value="{{ $vol->id }}" data-page="{{ $vol->pages()->min('page_number') ?? 1 }}">
                            {{ $vol->display_name ?? 'المجلد ' . $vol->number }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Page Counter -->
        <div class="flex flex-col text-xs gap-0" style="font-family: var(--font-ui); color: var(--text-secondary);">
            <div class="flex items-center gap-1">
                @if($currentPage?->original_page_number)
                    <span>الصفحة</span>
                    <div class="bg-primary-50 px-2 py-0.5 rounded border border-primary-100 font-bold text-primary-700">
                        {{ $currentPage->original_page_number }}
                    </div>
                    <span class="text-[10px] text-gray-400 mr-1">(تسللي: {{ $currentPageNum }})</span>
                @else
                    <span>تسللي</span>
                    <input type="number" 
                           value="{{ $currentPageNum }}" 
                           min="1" 
                           max="{{ $totalPages }}"
                           id="page-input"
                           class="w-10 text-center bg-transparent border-b border-gray-300 focus:border-green-600 focus:outline-none transition-colors"
                           style="color: var(--text-main);"
                           onchange="window.location.href='/book/{{ $book?->id }}/' + this.value">
                    <span id="total-pages-text">/ {{ $totalPages }}</span>
                @endif
            </div>
        </div>
        
        <!-- Navigation Buttons -->
        <div class="flex items-center gap-1">
            <!-- Previous Page -->
            @if($previousPage)
                <a href="{{ route('book.read', ['bookId' => $book?->id, 'pageNumber' => $previousPage->page_number]) }}" 
                   class="p-1.5 rounded hover:bg-gray-100 transition-colors" 
                   title="الصفحة السابقة">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="p-1.5 text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
            
            <!-- Next Page -->
            @if($nextPage)
                <a href="{{ route('book.read', ['bookId' => $book?->id, 'pageNumber' => $nextPage->page_number]) }}" 
                   class="p-1.5 rounded hover:bg-gray-100 transition-colors" 
                   title="الصفحة التالية">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @else
                <span class="p-1.5 text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"></path>
                    </svg>
                </span>
            @endif
            
            <!-- More Menu Button -->
            <button class="p-1.5 rounded hover:bg-gray-100 transition-colors" 
                    title="المزيد"
                    id="reading-menu-toggle">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Download Modal -->
<div id="download-modal" style="display: none; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); width: 90%; max-width: 400px; padding: 0; overflow: hidden;" onclick="event.stopPropagation()">
        
        <div style="padding: 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb;">
            <h3 style="font-weight: bold; color: #1f2937; margin: 0;">تنزيل الصفحة</h3>
            <button onclick="closeDownloadModal()" style="color: #9ca3af; background: none; border: none; cursor: pointer; padding: 0.25rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <p style="font-size: 0.875rem; color: #6b7280; text-align: center; margin: 0 0 1rem 0;">اختر الصيغة المناسبة لتنزيل الصفحة مع رمز QR:</p>
            
            <button onclick="downloadPageAs('image')" id="download-image-btn" style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#10b981'; this.style.background='#f0fdf4'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; background: #d1fae5; color: #059669; border-radius: 8px;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: #1f2937;">صورة (PNG)</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">جودة عالية، مناسبة للمشاركة</div>
                    </div>
                </div>
            </button>

            <button onclick="downloadPageAs('pdf')" id="download-pdf-btn" style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.background='#fef2f2'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; background: #fecaca; color: #dc2626; border-radius: 8px;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: #1f2937;">ملف PDF</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">مناسبة للطباعة</div>
                    </div>
                </div>
            </button>
            
            <div id="download-progress" style="display: none; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.875rem; color: #059669; margin-top: 0.5rem; font-weight: 500;">
                <svg style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>جاري تحضير الملف...</span>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menu Toggle Logic
    const menuToggleBtn = document.getElementById('reading-menu-toggle');
    const menuDropdown = document.getElementById('reading-menu');
    
    menuToggleBtn?.addEventListener('click', function(e) {
        e.stopPropagation();
        menuDropdown.classList.toggle('show');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (menuDropdown && !menuDropdown.contains(e.target) && e.target !== menuToggleBtn) {
            menuDropdown.classList.remove('show');
        }
    });

    // Fullscreen Toggle Logic
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const fullscreenText = document.getElementById('fullscreen-text');
    
    fullscreenBtn?.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                if(fullscreenText) fullscreenText.textContent = 'الخروج من الشاشة الكاملة';
                menuDropdown?.classList.remove('show');
            }).catch(err => {
                alert('لا يمكن تفعيل وضع ملء الشاشة: ' + err.message);
            });
        } else {
            document.exitFullscreen().then(() => {
                if(fullscreenText) fullscreenText.textContent = 'توسيع الشاشة';
                menuDropdown?.classList.remove('show');
            });
        }
    });
    
    // Update fullscreen text when user exits via Esc key
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement && fullscreenText) {
            fullscreenText.textContent = 'توسيع الشاشة';
        }
    });
    
    // Font Size Controls
    const contentArea = document.querySelector('.book-content, .prose');
    let currentFontSize = parseInt(localStorage.getItem('bookFontSize')) || 18;
    if (contentArea) contentArea.style.fontSize = currentFontSize + 'px';
    
    document.getElementById('menu-increase')?.addEventListener('click', function() {
        currentFontSize = Math.min(currentFontSize + 2, 32);
        if (contentArea) contentArea.style.fontSize = currentFontSize + 'px';
        localStorage.setItem('bookFontSize', currentFontSize);
        menuDropdown?.classList.remove('show');
    });
    
    document.getElementById('menu-decrease')?.addEventListener('click', function() {
        currentFontSize = Math.max(currentFontSize - 2, 12);
        if (contentArea) contentArea.style.fontSize = currentFontSize + 'px';
        localStorage.setItem('bookFontSize', currentFontSize);
        menuDropdown?.classList.remove('show');
    });
    
    // Harakat (Diacritics) Toggle
    const harakatBtn = document.getElementById('toggle-harakat');
    const harakatText = document.getElementById('harakat-text');
    let harakatVisible = localStorage.getItem('harakatVisible') !== 'false';
    
    // Arabic diacritics regex pattern
    const harakatPattern = /[\u064B-\u065F\u0670]/g;
    let originalContent = null;
    
    function updateHarakatDisplay() {
        const contentEl = document.querySelector('.book-content, .prose');
        if (!contentEl) return;
        
        if (!harakatVisible) {
            // Store original and remove harakat
            if (!originalContent) {
                originalContent = contentEl.innerHTML;
            }
            contentEl.innerHTML = contentEl.innerHTML.replace(harakatPattern, '');
            if(harakatText) harakatText.textContent = 'إظهار الحركات';
        } else {
            // Restore original content with harakat
            if (originalContent) {
                contentEl.innerHTML = originalContent;
            }
            if(harakatText) harakatText.textContent = 'إخفاء الحركات';
        }
    }
    
    // Initialize based on saved preference
    if (!harakatVisible) {
        setTimeout(updateHarakatDisplay, 100);
    }
    
    harakatBtn?.addEventListener('click', function() {
        harakatVisible = !harakatVisible;
        localStorage.setItem('harakatVisible', harakatVisible);
        
        // Reload to apply changes properly (simplest approach)
        location.reload();
    });
    
    // Download Modal Functions
    const downloadModal = document.getElementById('download-modal');
    const downloadBtn = document.getElementById('menu-download');
    
    downloadBtn?.addEventListener('click', function() {
        downloadModal.style.display = 'flex';
        menuDropdown?.classList.remove('show');
    });
    
    downloadModal?.addEventListener('click', function(e) {
        if (e.target === downloadModal) {
            closeDownloadModal();
        }
    });
});

function closeDownloadModal() {
    document.getElementById('download-modal').style.display = 'none';
}

async function downloadPageAs(type) {
    const downloadProgress = document.getElementById('download-progress');
    const imageBtn = document.getElementById('download-image-btn');
    const pdfBtn = document.getElementById('download-pdf-btn');
    
    downloadProgress.style.display = 'flex';
    imageBtn.disabled = true;
    pdfBtn.disabled = true;
    
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
        <span style='font-weight:bold; font-size: 16px; color: #000;'>{{ $book?->title ?? 'الكتاب' }}</span> 
        <span> / صفحة {{ $currentPageNum }}</span>
    `;
    wrapper.appendChild(headerInfo);

    // Content Clone
    const contentOriginal = document.querySelector('.book-content, .prose');
    if (contentOriginal) {
        const contentClone = contentOriginal.cloneNode(true);
        contentClone.style.fontSize = '18px';
        contentClone.style.lineHeight = '2';
        contentClone.style.margin = '0';
        contentClone.style.maxWidth = 'none';
        wrapper.appendChild(contentClone);
    }

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

    // QR Code في الزاوية اليسرى السفلية
    const qrContainer = document.createElement('div');
    qrContainer.id = 'qr-container-temp';
    qrContainer.style.position = 'absolute';
    qrContainer.style.bottom = '20px';
    qrContainer.style.left = '20px';
    qrContainer.style.backgroundColor = 'white';
    qrContainer.style.padding = '8px';
    qrContainer.style.borderRadius = '8px';
    qrContainer.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
    
    // رابط الصفحة الكامل
    const pageUrl = 'https://alkamelah.com/book/{{ $book?->id }}/' + {{ $currentPageNum }};
    
    // إنشاء QR code
    new QRCode(qrContainer, {
        text: pageUrl,
        width: 80,
        height: 80,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });

    wrapper.appendChild(qrContainer);
    document.body.appendChild(wrapper);

    // انتظار تحميل QR code
    await new Promise(resolve => setTimeout(resolve, 300));

    try {
        const canvas = await html2canvas(wrapper, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#fffbf2'
        });

        const fileName = `{{ $book?->title ?? 'page' }}-صفحة-{{ $currentPageNum }}`;

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
        
        closeDownloadModal();
    } catch (error) {
        console.error('Download failed:', error);
        alert('حدث خطأ أثناء التنزيل');
    } finally {
        document.body.removeChild(wrapper);
        downloadProgress.style.display = 'none';
        imageBtn.disabled = false;
        pdfBtn.disabled = false;
    }
}
</script>