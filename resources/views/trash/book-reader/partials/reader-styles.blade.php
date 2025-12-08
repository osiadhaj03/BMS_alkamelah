/* ═══════════════════════════════════════════════════════════ */
/* Book Reader - Main Styles */
/* ═══════════════════════════════════════════════════════════ */

/* Spacing Variables */
.book-reader {
    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
    --spacing-2xl: 48px;
    
    --mini-sidebar-width: 56px;
    --sidebar-width: 320px;
    --header-height: 56px;
    --nav-bar-height: 56px;
    
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 16px;
    --radius-full: 9999px;
    
    --transition-fast: 150ms ease;
    --transition-normal: 250ms ease;
    
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
    --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Base Layout */
.book-reader {
    display: flex;
    min-height: 100vh;
    background: var(--reader-bg);
    color: var(--reader-text);
    font-family: 'Amiri', serif;
}

/* ═══════════════════════════════════════════════════════════ */
/* Mini Sidebar */
/* ═══════════════════════════════════════════════════════════ */

.mini-sidebar {
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    background: var(--card-bg);
    border-radius: var(--radius-lg) 0 0 var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 100;
}

.mini-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    color: var(--reader-text-secondary);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.mini-icon svg {
    width: 22px;
    height: 22px;
}

.mini-icon:hover {
    background: var(--hover-bg);
    color: var(--reader-text);
}

.mini-icon.active {
    background: var(--primary-color);
    color: white;
}

.mini-sidebar-divider {
    width: 24px;
    height: 1px;
    background: var(--border-color);
    margin: var(--spacing-xs) 0;
}

/* ═══════════════════════════════════════════════════════════ */
/* Reader Main Container */
/* ═══════════════════════════════════════════════════════════ */

.reader-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-right: var(--mini-sidebar-width);
    min-height: 100vh;
}

/* ═══════════════════════════════════════════════════════════ */
/* Header */
/* ═══════════════════════════════════════════════════════════ */

.reader-header {
    position: sticky;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: var(--header-height);
    padding: 0 var(--spacing-lg);
    background: var(--card-bg);
    border-bottom: 1px solid var(--border-color);
    z-index: 90;
}

.header-right {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.menu-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: none;
    background: var(--hover-bg);
    color: var(--reader-text);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.menu-toggle-btn:hover {
    background: var(--primary-light);
    color: var(--primary-color);
}

.menu-toggle-btn svg {
    width: 22px;
    height: 22px;
}

.mobile-menu-toggle {
    display: none;
}

.header-book-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.book-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--reader-text);
    margin: 0;
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.book-author {
    font-size: 13px;
    color: var(--reader-text-secondary);
    padding-right: var(--spacing-sm);
    border-right: 2px solid var(--border-color);
}

.header-left {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

/* Toolbar Pill */
.toolbar-pill {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs);
    background: var(--hover-bg);
    border-radius: var(--radius-full);
}

.toolbar-divider {
    width: 1px;
    height: 20px;
    background: var(--border-color);
    margin: 0 var(--spacing-xs);
}

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: var(--reader-text-secondary);
    border-radius: var(--radius-full);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.btn-icon svg {
    width: 20px;
    height: 20px;
}

.btn-icon:hover {
    background: var(--card-bg);
    color: var(--reader-text);
}

.btn-icon.btn-text {
    width: auto;
    padding: 0 var(--spacing-sm);
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
}

.btn-icon.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 0 var(--spacing-md);
    width: auto;
}

.btn-icon.btn-primary:hover {
    background: var(--primary-hover);
}

.btn-text-label {
    margin-right: var(--spacing-xs);
    font-size: 13px;
}

/* ═══════════════════════════════════════════════════════════ */
/* Workspace */
/* ═══════════════════════════════════════════════════════════ */

.reader-workspace {
    flex: 1;
    display: flex;
    position: relative;
    overflow: hidden;
}

.sidebar-overlay {
    display: none;
}

/* ═══════════════════════════════════════════════════════════ */
/* Sidebar - TOC */
/* ═══════════════════════════════════════════════════════════ */

.reader-sidebar {
    width: var(--sidebar-width);
    height: calc(100vh - var(--header-height) - var(--nav-bar-height));
    display: flex;
    flex-direction: column;
    background: var(--card-bg);
    border-left: 1px solid var(--border-color);
    overflow: hidden;
    flex-shrink: 0;
    transition: width var(--transition-normal), opacity var(--transition-fast);
}

.reader-sidebar.sidebar-hidden {
    width: 0;
    opacity: 0;
    overflow: hidden;
    border-left: none;
}

.reader-sidebar.sidebar-visible {
    width: var(--sidebar-width);
    opacity: 1;
}

.sidebar-header {
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--border-color);
}

.sidebar-title {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: 16px;
    font-weight: 600;
    color: var(--reader-text);
    margin: 0 0 var(--spacing-md);
}

.sidebar-title-icon {
    width: 20px;
    height: 20px;
    color: var(--primary-color);
}

.sidebar-search {
    position: relative;
}

.sidebar-search-input {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    padding-left: 36px;
    background: var(--hover-bg);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    font-size: 14px;
    color: var(--reader-text);
    transition: all var(--transition-fast);
}

.sidebar-search-input::placeholder {
    color: var(--reader-text-secondary);
}

.sidebar-search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    background: var(--card-bg);
}

.sidebar-search-icon {
    position: absolute;
    left: var(--spacing-sm);
    top: 50%;
    transform: translateY(-50%);
    color: var(--reader-text-secondary);
    pointer-events: none;
}

.sidebar-search-icon svg {
    width: 18px;
    height: 18px;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: var(--spacing-sm);
}

/* TOC Items */
.toc-volume {
    margin-bottom: var(--spacing-xs);
}

.toc-volume-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--hover-bg);
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    text-align: right;
    transition: all var(--transition-fast);
}

.toc-volume-header:hover {
    background: var(--primary-light);
}

.toc-volume-icon {
    color: var(--primary-color);
}

.toc-volume-icon svg {
    width: 18px;
    height: 18px;
}

.toc-volume-title {
    flex: 1;
    font-size: 14px;
    font-weight: 600;
    color: var(--reader-text);
}

.toc-chevron {
    transition: transform var(--transition-fast);
}

.toc-chevron svg {
    width: 18px;
    height: 18px;
    color: var(--reader-text-secondary);
}

.toc-volume-header.expanded .toc-chevron {
    transform: rotate(180deg);
}

.toc-chapters {
    padding-right: var(--spacing-lg);
    margin-top: var(--spacing-xs);
}

/* Chapter Item */
.toc-chapter {
    --level-indent: calc(var(--level, 0) * 16px);
}

.toc-chapter-row {
    display: flex;
    align-items: center;
    margin-right: var(--level-indent);
}

.toc-expand-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: transparent;
    border: none;
    color: var(--reader-text-secondary);
    cursor: pointer;
    transition: transform var(--transition-fast);
    flex-shrink: 0;
}

.toc-expand-btn.expanded {
    transform: rotate(180deg);
}

.toc-expand-btn svg {
    width: 16px;
    height: 16px;
}

.toc-expand-placeholder {
    width: 24px;
    flex-shrink: 0;
}

.toc-chapter-link {
    flex: 1;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-md);
    background: transparent;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    text-align: right;
    transition: all var(--transition-fast);
}

.toc-chapter-link:hover {
    background: var(--hover-bg);
}

.toc-chapter-link.active {
    background: var(--primary-light);
    color: var(--primary-color);
}

.toc-chapter-icon {
    color: var(--reader-text-secondary);
    flex-shrink: 0;
}

.toc-chapter-icon svg {
    width: 16px;
    height: 16px;
}

.toc-chapter-title {
    flex: 1;
    font-size: 13px;
    color: var(--reader-text);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.toc-chapter-page {
    font-size: 11px;
    color: var(--reader-text-secondary);
    padding: 2px 6px;
    background: var(--hover-bg);
    border-radius: var(--radius-sm);
    flex-shrink: 0;
}

.toc-children {
    margin-top: var(--spacing-xs);
}

.toc-empty {
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--reader-text-secondary);
}

/* Sidebar Footer */
.sidebar-footer {
    padding: var(--spacing-md);
    border-top: 1px solid var(--border-color);
}

.reading-progress {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.progress-text {
    font-size: 12px;
    color: var(--reader-text-secondary);
}

.progress-bar {
    height: 4px;
    background: var(--hover-bg);
    border-radius: var(--radius-full);
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--primary-color);
    border-radius: var(--radius-full);
    transition: width var(--transition-normal);
}

/* ═══════════════════════════════════════════════════════════ */
/* Content Area */
/* ═══════════════════════════════════════════════════════════ */

.reader-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: var(--spacing-lg);
    overflow-y: auto;
    min-height: calc(100vh - var(--header-height) - var(--nav-bar-height));
}

.content-wrapper {
    width: 100%;
    max-width: 800px;
}

/* Paper Sheet */
.paper-sheet {
    background: var(--paper-bg);
    border-radius: var(--radius-lg);
    padding: var(--spacing-2xl);
    box-shadow: var(--shadow-lg);
    min-height: 500px;
    font-size: var(--font-size, 22px);
    font-family: var(--font-family, 'Amiri'), serif;
    line-height: 2;
    position: relative;
}

.chapter-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
    padding-bottom: var(--spacing-lg);
    border-bottom: 2px solid var(--border-color);
}

.chapter-title {
    font-size: 1.5em;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0 0 var(--spacing-sm);
}

.chapter-volume {
    font-size: 0.8em;
    color: var(--reader-text-secondary);
}

.chapter-ornament {
    font-size: 1.2em;
    color: var(--primary-color);
    margin-top: var(--spacing-md);
}

.chapter-text {
    text-align: justify;
    text-justify: inter-word;
}

.chapter-text p {
    margin-bottom: 1.2em;
    text-indent: 2em;
}

.empty-page {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--reader-text-secondary);
}

.empty-icon {
    width: 48px;
    height: 48px;
    margin-bottom: var(--spacing-md);
    opacity: 0.5;
}

.empty-page-number {
    display: block;
    margin-top: var(--spacing-sm);
    font-size: 0.9em;
}

.paper-footer {
    text-align: center;
    margin-top: var(--spacing-xl);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--border-color);
}

.page-ornament {
    font-size: 1.5em;
    color: var(--primary-color);
    opacity: 0.5;
}

/* Page Indicator */
.page-indicator {
    position: fixed;
    bottom: calc(var(--nav-bar-height) + var(--spacing-md));
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--card-bg);
    border-radius: var(--radius-full);
    box-shadow: var(--shadow-md);
    font-size: 14px;
    z-index: 50;
}

.page-current {
    font-weight: 600;
    color: var(--primary-color);
}

.page-separator {
    color: var(--reader-text-secondary);
}

.page-total {
    color: var(--reader-text-secondary);
}

/* ═══════════════════════════════════════════════════════════ */
/* Navigation Bar */
/* ═══════════════════════════════════════════════════════════ */

.reader-nav-bar {
    position: sticky;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: var(--nav-bar-height);
    padding: 0 var(--spacing-lg);
    background: var(--card-bg);
    border-top: 1px solid var(--border-color);
    z-index: 90;
}

.nav-controls {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.nav-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--hover-bg);
    border: none;
    border-radius: var(--radius-md);
    color: var(--reader-text);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.nav-btn svg {
    width: 24px;
    height: 24px;
}

.nav-btn:hover:not(.disabled) {
    background: var(--primary-light);
    color: var(--primary-color);
}

.nav-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.nav-page-input {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.page-input {
    width: 60px;
    padding: var(--spacing-sm);
    background: var(--hover-bg);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    color: var(--reader-text);
    transition: all var(--transition-fast);
}

.page-input:focus {
    outline: none;
    border-color: var(--primary-color);
    background: var(--card-bg);
}

.page-input::-webkit-inner-spin-button,
.page-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.page-total-label {
    font-size: 14px;
    color: var(--reader-text-secondary);
}

.nav-progress {
    flex: 1;
    max-width: 400px;
    margin: 0 var(--spacing-lg);
}

.progress-container {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.progress-percentage {
    font-size: 12px;
    font-weight: 600;
    color: var(--primary-color);
    min-width: 36px;
}

.progress-track {
    flex: 1;
    height: 6px;
    background: var(--hover-bg);
    border-radius: var(--radius-full);
    position: relative;
    overflow: visible;
}

.progress-bar-fill {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    background: var(--primary-color);
    border-radius: var(--radius-full);
    transition: width var(--transition-normal);
}

.progress-slider {
    position: absolute;
    top: 50%;
    right: 0;
    left: 0;
    transform: translateY(-50%);
    width: 100%;
    height: 20px;
    opacity: 0;
    cursor: pointer;
}

.nav-volume {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.volume-label {
    font-size: 13px;
    color: var(--reader-text-secondary);
}

.volume-select {
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--hover-bg);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    font-size: 13px;
    color: var(--reader-text);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.volume-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

/* ═══════════════════════════════════════════════════════════ */
/* Modals */
/* ═══════════════════════════════════════════════════════════ */

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
    padding: var(--spacing-lg);
}

.modal-content {
    width: 100%;
    max-width: 600px;
    max-height: 80vh;
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--spacing-md) var(--spacing-lg);
    border-bottom: 1px solid var(--border-color);
}

.modal-title {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: 18px;
    font-weight: 600;
    color: var(--reader-text);
    margin: 0;
}

.modal-title-icon {
    width: 22px;
    height: 22px;
    color: var(--primary-color);
}

.modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    border-radius: var(--radius-full);
    color: var(--reader-text-secondary);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.modal-close:hover {
    background: var(--hover-bg);
    color: var(--reader-text);
}

.modal-close svg {
    width: 20px;
    height: 20px;
}

.modal-body {
    flex: 1;
    padding: var(--spacing-lg);
    overflow-y: auto;
}

.modal-footer {
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--border-color);
}

/* Search Modal */
.search-input-wrapper {
    display: flex;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-lg);
}

.search-input-field {
    flex: 1;
    padding: var(--spacing-md);
    background: var(--hover-bg);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    font-size: 16px;
    color: var(--reader-text);
    transition: all var(--transition-fast);
}

.search-input-field::placeholder {
    color: var(--reader-text-secondary);
}

.search-input-field:focus {
    outline: none;
    border-color: var(--primary-color);
    background: var(--card-bg);
}

.search-submit-btn {
    padding: var(--spacing-md) var(--spacing-xl);
    background: var(--primary-color);
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.search-submit-btn:hover {
    background: var(--primary-hover);
}

.search-results {
    min-height: 200px;
}

.results-header {
    margin-bottom: var(--spacing-md);
}

.results-count {
    font-size: 13px;
    color: var(--reader-text-secondary);
}

.results-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.result-item {
    display: flex;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--hover-bg);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    text-align: right;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.result-item:hover {
    border-color: var(--primary-color);
    background: var(--primary-light);
}

.result-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 50px;
    padding: var(--spacing-sm);
    background: var(--card-bg);
    border-radius: var(--radius-md);
}

.result-page-label {
    font-size: 10px;
    color: var(--reader-text-secondary);
}

.result-page-number {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-color);
}

.result-content {
    flex: 1;
}

.result-excerpt {
    font-size: 14px;
    line-height: 1.6;
    color: var(--reader-text);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.result-excerpt mark,
.search-highlight {
    background: var(--highlight-bg);
    color: inherit;
    padding: 0 2px;
    border-radius: 2px;
}

.result-meta {
    display: flex;
    gap: var(--spacing-sm);
    margin-top: var(--spacing-xs);
}

.result-volume,
.result-chapter {
    font-size: 11px;
    color: var(--reader-text-secondary);
}

.no-results,
.search-hint {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-2xl);
    text-align: center;
}

.no-results-icon,
.hint-icon {
    width: 48px;
    height: 48px;
    color: var(--reader-text-secondary);
    margin-bottom: var(--spacing-md);
    opacity: 0.5;
}

.no-results p,
.search-hint p {
    font-size: 16px;
    color: var(--reader-text);
    margin: 0 0 var(--spacing-xs);
}

.no-results span,
.search-hint span {
    font-size: 13px;
    color: var(--reader-text-secondary);
}

/* Settings Modal */
.setting-group {
    margin-bottom: var(--spacing-lg);
}

.setting-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--reader-text);
    margin-bottom: var(--spacing-sm);
}

.setting-control {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.font-size-control {
    justify-content: center;
    background: var(--hover-bg);
    padding: var(--spacing-sm);
    border-radius: var(--radius-md);
}

.setting-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: var(--card-bg);
    border: none;
    border-radius: var(--radius-md);
    font-size: 16px;
    font-weight: 600;
    color: var(--reader-text);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.setting-btn:hover {
    background: var(--primary-light);
    color: var(--primary-color);
}

.font-size-value {
    min-width: 60px;
    text-align: center;
    font-size: 14px;
    color: var(--reader-text);
}

.font-family-control {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-sm);
}

.font-btn {
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--hover-bg);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-fast);
    color: var(--reader-text);
}

.font-btn:hover {
    background: var(--primary-light);
}

.font-btn.active {
    border-color: var(--primary-color);
    background: var(--primary-light);
}

.theme-control {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-sm);
}

.theme-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    background: transparent;
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.theme-btn:hover {
    background: var(--hover-bg);
}

.theme-btn.active {
    border-color: var(--primary-color);
}

.theme-preview {
    width: 48px;
    height: 32px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-color);
}

.theme-btn.theme-light .theme-preview {
    background: #ffffff;
}

.theme-btn.theme-sepia .theme-preview {
    background: #f8f0e3;
}

.theme-btn.theme-dark .theme-preview {
    background: #1a1a2e;
}

.theme-name {
    font-size: 12px;
    color: var(--reader-text);
}

.toggle-btn {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    background: transparent;
    border: none;
    cursor: pointer;
}

.toggle-track {
    position: relative;
    width: 48px;
    height: 26px;
    background: var(--hover-bg);
    border-radius: var(--radius-full);
    transition: background var(--transition-fast);
}

.toggle-btn.active .toggle-track {
    background: var(--primary-color);
}

.toggle-thumb {
    position: absolute;
    top: 3px;
    right: 3px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    box-shadow: var(--shadow-sm);
    transition: transform var(--transition-fast);
}

.toggle-btn.active .toggle-thumb {
    transform: translateX(-22px);
}

.toggle-label {
    font-size: 14px;
    color: var(--reader-text);
}

.settings-hint {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-xs);
    font-size: 12px;
    color: var(--reader-text-secondary);
    margin: 0;
}

.settings-hint .hint-icon {
    width: 16px;
    height: 16px;
    margin: 0;
    opacity: 1;
}

/* ═══════════════════════════════════════════════════════════ */
/* Loading */
/* ═══════════════════════════════════════════════════════════ */

.reader-loading {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--reader-bg-rgb), 0.8);
    backdrop-filter: blur(4px);
    z-index: 300;
}

.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-md);
}

.spinner {
    width: 50px;
    height: 50px;
    animation: rotate 2s linear infinite;
}

.spinner .path {
    stroke: var(--primary-color);
    stroke-linecap: round;
    animation: dash 1.5s ease-in-out infinite;
}

@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}

@keyframes dash {
    0% {
        stroke-dasharray: 1, 150;
        stroke-dashoffset: 0;
    }
    50% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -35;
    }
    100% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -124;
    }
}

.loading-spinner span {
    font-size: 14px;
    color: var(--reader-text-secondary);
}

/* ═══════════════════════════════════════════════════════════ */
/* Responsive */
/* ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .mini-sidebar {
        transform: translateY(-50%) translateX(100%);
        transition: transform var(--transition-normal);
    }
    
    .mini-sidebar:hover {
        transform: translateY(-50%) translateX(0);
    }
    
    .reader-main {
        margin-right: 0;
    }
    
    .reader-sidebar {
        position: fixed;
        top: var(--header-height);
        right: 0;
        height: calc(100vh - var(--header-height));
        z-index: 100;
        box-shadow: var(--shadow-lg);
    }
    
    .sidebar-overlay {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99;
    }
}

@media (max-width: 768px) {
    .mini-sidebar {
        top: auto;
        bottom: calc(var(--nav-bar-height) + var(--spacing-md));
        transform: none;
        border-radius: var(--radius-lg) 0 0 var(--radius-lg);
    }
    
    .mini-sidebar:hover {
        transform: none;
    }
    
    .reader-header {
        padding: 0 var(--spacing-md);
    }
    
    .mobile-menu-toggle {
        display: flex;
    }
    
    .book-author {
        display: none;
    }
    
    .book-title {
        max-width: 150px;
        font-size: 16px;
    }
    
    .toolbar-pill {
        gap: 0;
    }
    
    .toolbar-divider {
        display: none;
    }
    
    .btn-text-label {
        display: none;
    }
    
    .reader-content {
        padding: var(--spacing-md);
    }
    
    .paper-sheet {
        padding: var(--spacing-lg);
        border-radius: var(--radius-md);
    }
    
    .page-indicator {
        display: none;
    }
    
    .reader-nav-bar {
        padding: 0 var(--spacing-md);
    }
    
    .nav-progress {
        display: none;
    }
    
    .nav-volume {
        display: none;
    }
    
    .reader-sidebar {
        width: 280px;
    }
    
    .modal-content {
        margin: var(--spacing-sm);
        max-height: calc(100vh - var(--spacing-md));
    }
}

@media (max-width: 480px) {
    .reader-header {
        height: 48px;
    }
    
    .reader-nav-bar {
        height: 48px;
    }
    
    .book-title {
        max-width: 120px;
        font-size: 14px;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
    }
    
    .btn-icon svg {
        width: 18px;
        height: 18px;
    }
    
    .paper-sheet {
        padding: var(--spacing-md);
        font-size: 18px;
        line-height: 1.8;
    }
}
