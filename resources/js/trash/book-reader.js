/**
 * Book Reader Alpine.js Component
 * ================================
 * المكون الرئيسي لقارئ الكتب
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
            // Load saved preferences
            this.loadPreferences();
            
            // Apply theme
            this.applyTheme();
            
            // Setup keyboard shortcuts
            this.setupKeyboardShortcuts();
            
            // Setup touch/swipe navigation
            this.setupTouchNavigation();
            
            // Listen for Livewire updates
            this.setupLivewireListeners();
            
            // Prefetch adjacent pages
            this.$nextTick(() => {
                this.prefetchPages();
            });
        },
        
        // ═══════════════════════════════════════════════════════════
        // Preferences
        // ═══════════════════════════════════════════════════════════
        
        loadPreferences() {
            const saved = localStorage.getItem('book-reader-preferences');
            if (saved) {
                const prefs = JSON.parse(saved);
                if (prefs.theme) this.theme = prefs.theme;
                if (prefs.fontSize) this.fontSize = prefs.fontSize;
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
            this.$el.setAttribute('data-theme', this.theme);
            
            // Update meta theme-color
            const themeColors = {
                light: '#ffffff',
                dark: '#1a1a2e',
                sepia: '#f8f0e3'
            };
            
            let metaTheme = document.querySelector('meta[name="theme-color"]');
            if (!metaTheme) {
                metaTheme = document.createElement('meta');
                metaTheme.name = 'theme-color';
                document.head.appendChild(metaTheme);
            }
            metaTheme.content = themeColors[this.theme] || themeColors.light;
        },
        
        cycleTheme() {
            const themes = ['light', 'sepia', 'dark'];
            const currentIndex = themes.indexOf(this.theme);
            this.theme = themes[(currentIndex + 1) % themes.length];
            this.applyTheme();
            this.savePreferences();
            
            // Sync with Livewire
            this.$wire.setTheme(this.theme);
        },
        
        // ═══════════════════════════════════════════════════════════
        // Keyboard Shortcuts
        // ═══════════════════════════════════════════════════════════
        
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ignore if typing in input
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }
                
                switch (e.key) {
                    // Navigation
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
                    
                    // Search
                    case 'f':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.showSearchModal = true;
                        }
                        break;
                    
                    // Escape
                    case 'Escape':
                        this.$wire.showSearchModal = false;
                        this.$wire.showSettingsModal = false;
                        break;
                    
                    // Toggle sidebar
                    case 's':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.toggleSidebar();
                        }
                        break;
                    
                    // Toggle theme
                    case 't':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.cycleTheme();
                        }
                        break;
                    
                    // Font size
                    case '+':
                    case '=':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.increaseFontSize();
                        }
                        break;
                    case '-':
                        if (e.ctrlKey || e.metaKey) {
                            e.preventDefault();
                            this.$wire.decreaseFontSize();
                        }
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
            const threshold = 50; // Minimum swipe distance
            const diff = this.touchStartX - this.touchEndX;
            
            if (Math.abs(diff) < threshold) return;
            
            if (diff > 0) {
                // Swiped left (next page in RTL)
                this.$wire.previousPage();
            } else {
                // Swiped right (previous page in RTL)
                this.$wire.nextPage();
            }
        },
        
        // ═══════════════════════════════════════════════════════════
        // Livewire Listeners
        // ═══════════════════════════════════════════════════════════
        
        setupLivewireListeners() {
            // Listen for page changes
            Livewire.on('page-loaded', (data) => {
                this.pageNumber = data.pageNumber;
                this.scrollToTop();
                this.prefetchPages();
                this.updateUrl(data.pageNumber);
            });
            
            // Listen for theme changes
            Livewire.on('theme-changed', (data) => {
                this.theme = data.theme;
                this.applyTheme();
                this.savePreferences();
            });
            
            // Listen for font size changes
            Livewire.on('font-size-changed', (data) => {
                this.fontSize = data.fontSize;
                this.savePreferences();
            });
            
            // Listen for search result navigation
            Livewire.on('search-result-selected', (data) => {
                this.$wire.showSearchModal = false;
                this.highlightSearchTerm(data.query);
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
            url.pathname = url.pathname.replace(/\/\d+$/, `/${pageNumber}`);
            window.history.replaceState({}, '', url);
        },
        
        prefetchPages() {
            // Prefetch next and previous pages
            const pagesToPrefetch = [
                this.pageNumber - 1,
                this.pageNumber + 1,
                this.pageNumber - 2,
                this.pageNumber + 2,
            ].filter(p => p > 0 && p <= this.totalPages);
            
            // Notify Livewire to prefetch
            if (pagesToPrefetch.length > 0) {
                this.$wire.prefetchPages(pagesToPrefetch);
            }
        },
        
        highlightSearchTerm(query) {
            if (!query) return;
            
            const content = this.$el.querySelector('.chapter-text');
            if (!content) return;
            
            // Remove existing highlights
            content.querySelectorAll('.highlight').forEach(el => {
                el.replaceWith(el.textContent);
            });
            
            // Add new highlights (simple implementation)
            const walker = document.createTreeWalker(
                content,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            const matches = [];
            while (walker.nextNode()) {
                const node = walker.currentNode;
                const text = node.textContent;
                const index = text.indexOf(query);
                if (index !== -1) {
                    matches.push({ node, index, query });
                }
            }
            
            // Highlight first match and scroll to it
            if (matches.length > 0) {
                const { node, index, query } = matches[0];
                const range = document.createRange();
                range.setStart(node, index);
                range.setEnd(node, index + query.length);
                
                const span = document.createElement('span');
                span.className = 'highlight';
                range.surroundContents(span);
                
                span.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
        
        // ═══════════════════════════════════════════════════════════
        // Share
        // ═══════════════════════════════════════════════════════════
        
        shareBook() {
            const title = document.querySelector('.book-title')?.textContent || 'كتاب';
            const url = window.location.href;
            
            if (navigator.share) {
                navigator.share({ title, url })
                    .catch(() => this.copyToClipboard(url));
            } else {
                this.copyToClipboard(url);
            }
        },
        
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show toast notification
                this.showToast('تم نسخ الرابط');
            });
        },
        
        showToast(message) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'reader-toast';
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
                animation: fadeIn 0.3s ease;
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'fadeIn 0.3s ease reverse';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }
    }));
});

// ═══════════════════════════════════════════════════════════
// Reading Progress Bar (Scroll-based)
// ═══════════════════════════════════════════════════════════

class ReadingProgressIndicator {
    constructor() {
        this.init();
    }
    
    init() {
        const content = document.querySelector('.reader-content');
        if (!content) return;
        
        content.addEventListener('scroll', () => {
            this.updateProgress(content);
        });
    }
    
    updateProgress(element) {
        const scrollTop = element.scrollTop;
        const scrollHeight = element.scrollHeight - element.clientHeight;
        const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
        
        // Update any progress indicators
        document.documentElement.style.setProperty('--scroll-progress', `${progress}%`);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    new ReadingProgressIndicator();
});

// ═══════════════════════════════════════════════════════════
// Page Visibility API - Pause/Resume
// ═══════════════════════════════════════════════════════════

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        // Page is hidden - save reading position
        Livewire.dispatch('save-reading-position');
    }
});

// ═══════════════════════════════════════════════════════════
// Service Worker Registration (for offline support)
// ═══════════════════════════════════════════════════════════

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // You can register a service worker here for offline reading
        // navigator.serviceWorker.register('/sw-reader.js');
    });
}
