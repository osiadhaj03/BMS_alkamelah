{{-- Settings Modal - نافذة الإعدادات --}}

<div 
    class="modal-overlay"
    x-show="$wire.showSettingsModal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click.self="$wire.showSettingsModal = false"
    @keydown.escape.window="$wire.showSettingsModal = false"
    x-cloak
>
    <div 
        class="modal-content modal-settings"
        x-show="$wire.showSettingsModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
    >
        {{-- رأس النافذة --}}
        <div class="modal-header">
            <h3 class="modal-title">
                <svg viewBox="0 0 24 24" fill="currentColor" class="modal-title-icon">
                    <path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58z"/>
                </svg>
                إعدادات القراءة
            </h3>
            <button 
                class="modal-close"
                @click="$wire.showSettingsModal = false"
            >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        
        {{-- محتوى الإعدادات --}}
        <div class="modal-body">
            {{-- حجم الخط --}}
            <div class="setting-group">
                <label class="setting-label">حجم الخط</label>
                <div class="setting-control font-size-control">
                    <button 
                        class="setting-btn"
                        wire:click="decreaseFontSize"
                        title="تصغير"
                    >
                        <span>أ-</span>
                    </button>
                    <span class="font-size-value">{{ $fontSize }}px</span>
                    <button 
                        class="setting-btn"
                        wire:click="increaseFontSize"
                        title="تكبير"
                    >
                        <span>أ+</span>
                    </button>
                </div>
            </div>
            
            {{-- نوع الخط --}}
            <div class="setting-group">
                <label class="setting-label">نوع الخط</label>
                <div class="setting-control font-family-control">
                    <button 
                        class="font-btn {{ $fontFamily === 'Amiri' ? 'active' : '' }}"
                        wire:click="setFontFamily('Amiri')"
                        style="font-family: 'Amiri', serif;"
                    >
                        أميري
                    </button>
                    <button 
                        class="font-btn {{ $fontFamily === 'Tajawal' ? 'active' : '' }}"
                        wire:click="setFontFamily('Tajawal')"
                        style="font-family: 'Tajawal', sans-serif;"
                    >
                        تجوال
                    </button>
                    <button 
                        class="font-btn {{ $fontFamily === 'Noto Kufi Arabic' ? 'active' : '' }}"
                        wire:click="setFontFamily('Noto Kufi Arabic')"
                        style="font-family: 'Noto Kufi Arabic', sans-serif;"
                    >
                        نوتو كوفي
                    </button>
                </div>
            </div>
            
            {{-- الثيم --}}
            <div class="setting-group">
                <label class="setting-label">المظهر</label>
                <div class="setting-control theme-control">
                    <button 
                        class="theme-btn theme-light {{ $theme === 'light' ? 'active' : '' }}"
                        wire:click="setTheme('light')"
                        title="فاتح"
                    >
                        <span class="theme-preview"></span>
                        <span class="theme-name">فاتح</span>
                    </button>
                    <button 
                        class="theme-btn theme-sepia {{ $theme === 'sepia' ? 'active' : '' }}"
                        wire:click="setTheme('sepia')"
                        title="سيبيا"
                    >
                        <span class="theme-preview"></span>
                        <span class="theme-name">سيبيا</span>
                    </button>
                    <button 
                        class="theme-btn theme-dark {{ $theme === 'dark' ? 'active' : '' }}"
                        wire:click="setTheme('dark')"
                        title="داكن"
                    >
                        <span class="theme-preview"></span>
                        <span class="theme-name">داكن</span>
                    </button>
                </div>
            </div>
            
            {{-- التشكيل --}}
            <div class="setting-group">
                <label class="setting-label">التشكيل</label>
                <div class="setting-control toggle-control">
                    <button 
                        class="toggle-btn {{ $showDiacritics ? 'active' : '' }}"
                        wire:click="toggleDiacritics"
                    >
                        <span class="toggle-track">
                            <span class="toggle-thumb"></span>
                        </span>
                        <span class="toggle-label">
                            {{ $showDiacritics ? 'مفعّل' : 'معطّل' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- تذييل النافذة --}}
        <div class="modal-footer">
            <p class="settings-hint">
                <svg viewBox="0 0 24 24" fill="currentColor" class="hint-icon">
                    <path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                </svg>
                يتم حفظ الإعدادات تلقائياً
            </p>
        </div>
    </div>
</div>
