/**
 * Book Reader Alpine.js Component
 * ================================
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('bookReader', (config) => ({
        // State
        bookId: config.bookId,
        pageNumber: config.pageNumber,
        totalPages: config.totalPages,
        theme: config.theme || 'light',
        fontSize: config.fontSize || 22,
        showSidebar: config.showSidebar ?? true,
        
        // Touch/Swipe
        touchStartX: 0,
        touchEndX: 0,
        
        // Init
        init() {
            this.loadPreferences();
            this.applyTheme();
            this.setupKeyboardShortcuts();
            this.setupTouchNavigation();
            this.setupLivewireListeners();
        },
        
        // ═══════════════════════════════════════════════════════════
        // Preferences
        // ═══════════════════════════════════════════════════════════
        
        loadPreferences() {
            const saved = localStorage.getItem('book-reader-preferences');
            if (saved) {
                try {
                    const prefs = JSON.parse(saved);
                    if (prefs.theme) this.theme = prefs.theme;
                    if (prefs.fontSize) this.fontSize = prefs.fontSize;
                } catch (e) {}
            }
        },
        
        savePreferences() {
            localStorage.setItem('book-reader-preferences', JSON.stringify({
                theme: this.theme,
                fontSize: this.fontSize,
            }));
        },
        
        // ═══════════════════════════════════════════════════════════
        // Theme
        // ═══════════════════════════════════════════════════════════
        
        applyTheme() {
            document.documentElement.setAttribute('data-theme', this.theme);
            
            const themeColors = {
                light: '#ffffff',
                dark: '#1a1a2e',
                sepia: '#f8f0e3'
            };
            
            let metaTheme = document.querySelector('meta[name="theme-color"]');
            if (metaTheme) {
                metaTheme.content = themeColors[this.theme] || themeColors.light;
            }
        },
        
        // ═══════════════════════════════════════════════════════════
        // Keyboard Shortcuts
        // ═══════════════════════════════════════════════════════════
        
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }
                
                switch (e.key) {
                    case 'ArrowRight':
                        e.preventDefault();
                        this.$wire.previousPage();
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.$wire.nextPage();
                        break;
                    case 'Home':
                        e.preventDefault();
                        this.$wire.goToPage(1);
                        break;
                    case 'End':
                        e.preventDefault();
                        this.$wire.goToPage(this.totalPages);
                        break;
                    case 'f':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.showSearchModal = true;
                        }
                        break;
                    case 'Escape':
                        this.$wire.showSearchModal = false;
                        this.$wire.showSettingsModal = false;
                        break;
                }
            });
        },
        
        // ═══════════════════════════════════════════════════════════
        // Touch Navigation
        // ═══════════════════════════════════════════════════════════
        
        setupTouchNavigation() {
            const content = this.$el.querySelector('.reader-content');
            if (!content) return;
            
            content.addEventListener('touchstart', (e) => {
                this.touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            content.addEventListener('touchend', (e) => {
                this.touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe();
            }, { passive: true });
        },
        
        handleSwipe() {
            const threshold = 50;
            const diff = this.touchStartX - this.touchEndX;
            
            if (Math.abs(diff) < threshold) return;
            
            if (diff > 0) {
                this.$wire.previousPage();
            } else {
                this.$wire.nextPage();
            }
        },
        
        // ═══════════════════════════════════════════════════════════
        // Livewire Listeners
        // ═══════════════════════════════════════════════════════════
        
        setupLivewireListeners() {
            Livewire.on('page-loaded', (data) => {
                this.pageNumber = data.pageNumber;
                this.scrollToTop();
                this.updateUrl(data.pageNumber);
            });
            
            Livewire.on('theme-changed', (data) => {
                this.theme = data.theme;
                this.applyTheme();
                this.savePreferences();
            });
        },
        
        // ═══════════════════════════════════════════════════════════
        // Utilities
        // ═══════════════════════════════════════════════════════════
        
        scrollToTop() {
            const content = this.$el.querySelector('.reader-content');
            if (content) {
                content.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        updateUrl(pageNumber) {
            const url = new URL(window.location);
            const pathParts = url.pathname.split('/');
            const lastPart = pathParts[pathParts.length - 1];
            
            if (!isNaN(parseInt(lastPart))) {
                pathParts[pathParts.length - 1] = pageNumber;
            } else {
                pathParts.push(pageNumber);
            }
            
            url.pathname = pathParts.join('/');
            window.history.replaceState({}, '', url);
        },
        
        shareBook() {
            const title = document.querySelector('.book-title')?.textContent || 'كتاب';
            const url = window.location.href;
            
            if (navigator.share) {
                navigator.share({ title, url }).catch(() => this.copyToClipboard(url));
            } else {
                this.copyToClipboard(url);
            }
        },
        
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToast('تم نسخ الرابط');
            });
        },
        
        showToast(message) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed;
                bottom: 100px;
                left: 50%;
                transform: translateX(-50%);
                padding: 12px 24px;
                background: var(--card-bg);
                color: var(--reader-text);
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }
    }));
});
