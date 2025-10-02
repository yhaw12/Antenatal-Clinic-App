<!DOCTYPE html>
<html lang="en" class="transition-all duration-300">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', config('app.name', 'ANC Clinic'))</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="color-scheme" content="light dark">
    <style>
      /* Small extra utilities that are safe to inline (keeps file standalone) */
      .spinner-primary {
        display:inline-block;
        width:18px;height:18px;border:3px solid rgba(0,0,0,0.12);border-top-color:currentColor;border-radius:50%;animation:spin 1s linear infinite;
      }
      @keyframes spin { to { transform: rotate(360deg); } }
      @media (prefers-reduced-motion: reduce) {
        .spinner-primary { animation: none; }
        .transition-all, .animate-pulse, .animate-spin { transition: none !important; animation: none !important; }
      }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-slate-900 text-gray-900 dark:text-gray-100 font-inter antialiased min-h-screen flex flex-col">

    <!-- Global Loader with Enhanced Animation & Progress -->
    <div id="global-loader"
         class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 transition-opacity duration-300"
         aria-hidden="true" role="dialog" aria-live="polite" aria-atomic="true">
        <div class="relative bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl flex items-center gap-4 border border-gray-200 dark:border-gray-700 max-w-md w-full">
            <!-- Spinner (always present) -->
            <div class="flex-shrink-0" aria-hidden="true">
                <svg class="h-10 w-10 text-blue-600" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" stroke-opacity="0.15"></circle>
                    <path id="global-loader-spinner" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z"></path>
                </svg>
            </div>

            <!-- Message area: hidden when no message passed to loader.show() -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span id="global-loader-message" class="text-base font-semibold text-gray-800 dark:text-gray-200">Loading...</span>
                    <button id="global-loader-close" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-300" aria-label="Close loading overlay">Close</button>
                </div>

                <div id="global-loader-progress-wrap" class="mt-3 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden" aria-hidden="true">
                    <div id="global-loader-progress" class="h-full w-0 bg-gradient-to-r from-blue-500 to-emerald-500 transition-all duration-300"></div>
                </div>

                <div id="global-loader-sub" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Please wait...</div>
            </div>
        </div>
    </div>

    <!-- Enhanced Topbar -->
    <header class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 shadow-lg sticky top-0 z-40 transition-all duration-300 supports-backdrop-blur:bg-opacity-90">
        @include('partials.topbar')
    </header>

    <!-- Page content -->
    <div class="flex-1 relative">
        <div class="absolute inset-0 bg-gradient-to-br from-transparent via-blue-50/20 to-emerald-50/20 dark:via-gray-900/50 dark:to-slate-900/50 pointer-events-none"></div>
        <div class="relative z-10 max-w-7xl mx-auto w-full px-4 py-2 flex flex-col lg:flex-row gap-2">
            @auth
            <aside class="lg:w-50 flex-shrink-0 hidden lg:block transition-all duration-300" id="sidebar">
                @include('partials.sidebar')
            </aside>
            @endauth

            <main class="flex-1 min-w-0 overflow-y-auto">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-2 transition-all duration-300">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Enhanced Footer -->
    <footer class="mt-12 border-t border-gray-200/30 dark:border-gray-700/30 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
        @include('partials.anc_footer')
    </footer>

    <!-- Mobile Sidebar Overlay -->
    @auth
    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/60 hidden lg:hidden z-40 transition-opacity duration-300 backdrop-blur-sm" aria-hidden="true"></div>
    <div id="mobile-sidebar" class="fixed top-0 left-0 h-full w-80 bg-white dark:bg-gray-900 transform -translate-x-full lg:hidden z-50 transition-transform duration-300 ease-in-out shadow-2xl border-r border-gray-200 dark:border-gray-700" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Mobile sidebar">
        @include('partials.sidebar')
        <button id="close-mobile-sidebar" class="absolute top-4 right-4 p-3 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" aria-label="Close sidebar">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    @endauth

    @stack('scripts')

    <!-- Core Scripts -->
    <script>
    (function() {
        'use strict';

        // Respect reduced motion
        const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // App Namespace
        window.App = window.App || {};

        // Helpers
        const escapeHtml = (text) => {
            if (text === undefined || text === null) return '';
            const div = document.createElement('div');
            div.textContent = String(text);
            return div.innerHTML;
        };

        const debounce = (fn, wait) => {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn(...args), wait);
            };
        };

        // ---------- Loader ----------
        class Loader {
            constructor() {
                this.loader = document.getElementById('global-loader');
                this.messageEl = document.getElementById('global-loader-message');
                this.subEl = document.getElementById('global-loader-sub');
                this.progressEl = document.getElementById('global-loader-progress');
                this.progressWrap = document.getElementById('global-loader-progress-wrap');
                this.closeBtn = document.getElementById('global-loader-close');
                this.timeoutId = null;
                this.defaultTimeout = 15000;
                this.progress = 0;
                this._animating = false;

                // By default clicking close will forcibly hide (only visible when message shown)
                if (this.closeBtn) this.closeBtn.addEventListener('click', () => this.hide(true));
            }

            /**
             * show(message = '', timeout = defaultTimeout, sub = '')
             * - If `message` is falsy ('' / null / undefined), loader shows spinner alone (no text, no close, no progress)
             * - If `message` is provided, it will show message, subtext and progress bar (previous behavior)
             */
            show(message = '', timeout = this.defaultTimeout, sub = '') {
                if (!this.loader) return;

                const hasMessage = !!message;

                // Make loader visible
                this.loader.classList.remove('hidden');
                this.loader.style.opacity = '1';
                this.loader.setAttribute('aria-hidden', 'false');

                // Manage message/sub/close visibility
                if (this.messageEl) {
                    this.messageEl.textContent = message;
                    this.messageEl.style.display = hasMessage ? '' : 'none';
                }
                if (this.subEl) {
                    this.subEl.textContent = sub;
                    this.subEl.style.display = hasMessage ? '' : 'none';
                }
                if (this.closeBtn) {
                    this.closeBtn.style.display = hasMessage ? '' : 'none';
                }

                // Manage progress bar visibility/initial width
                if (this.progressWrap) {
                    this.progressWrap.style.display = hasMessage ? '' : 'none';
                }
                if (this.progressEl) {
                    this.progressEl.style.width = '0%';
                }

                this.progress = 0;
                if (!prefersReducedMotion && hasMessage) {
                    // animate progress only when message/progress is visible
                    this.animateProgress();
                } else {
                    // ensure progress animation not running when spinner-only
                    this._animating = false;
                    if (this._progressTimer) {
                        clearTimeout(this._progressTimer);
                        this._progressTimer = null;
                    }
                }

                // Setup timeout
                if (timeout) {
                    if (this.timeoutId) clearTimeout(this.timeoutId);
                    this.timeoutId = setTimeout(() => {
                        this.hide();
                        console.warn('Loader timeout reached');
                    }, timeout);
                }
            }

            animateProgress() {
                if (!this.progressEl) return;
                if (this._animating) return;
                this._animating = true;
                const step = () => {
                    if (this.progress < 95) {
                        // increase slowly; randomize a little for realism
                        this.progress = Math.min(95, this.progress + (Math.random() * 8));
                        this.progressEl.style.width = `${Math.floor(this.progress)}%`;
                        this._progressTimer = setTimeout(step, 200 + Math.random() * 200);
                    } else {
                        // pause near completion until hide is called
                        this._animating = false;
                    }
                };
                step();
            }

            hide(force = false) {
                if (!this.loader) return;
                if (this.progressEl) this.progressEl.style.width = '100%';
                if (this.timeoutId) {
                    clearTimeout(this.timeoutId);
                    this.timeoutId = null;
                }

                // fade out smoothly unless reduced motion
                if (!prefersReducedMotion) {
                    this.loader.style.opacity = '0';
                    setTimeout(() => {
                        if (this.loader) this.loader.classList.add('hidden');
                        this.loader.setAttribute('aria-hidden', 'true');
                        // reset UI pieces
                        if (this.messageEl) { this.messageEl.textContent = ''; this.messageEl.style.display = ''; }
                        if (this.subEl) { this.subEl.textContent = ''; this.subEl.style.display = ''; }
                        if (this.closeBtn) this.closeBtn.style.display = '';
                        if (this.progressEl) this.progressEl.style.width = '0%';
                        if (this.progressWrap) this.progressWrap.style.display = '';
                        this.progress = 0;
                    }, 300);
                } else {
                    this.loader.classList.add('hidden');
                    this.loader.setAttribute('aria-hidden', 'true');
                    if (this.messageEl) { this.messageEl.textContent = ''; this.messageEl.style.display = ''; }
                    if (this.subEl) { this.subEl.textContent = ''; this.subEl.style.display = ''; }
                    if (this.closeBtn) this.closeBtn.style.display = '';
                    if (this.progressEl) this.progressEl.style.width = '0%';
                    if (this.progressWrap) this.progressWrap.style.display = '';
                    this.progress = 0;
                }
            }
        }

        // ---------- Theme Manager ----------
        class ThemeManager {
            constructor() {
                this.storageKey = 'anc_theme_pref_v2';
                this.icon = document.getElementById('theme-icon');
                this.button = document.getElementById('theme-toggle-main');
                this.html = document.documentElement;
                this.mq = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
                this.init();
            }

            getCurrentTheme() {
                return localStorage.getItem(this.storageKey) || 'system';
            }

            applyTheme(pref) {
                let isDark;
                if (pref === 'dark') {
                    isDark = true;
                } else if (pref === 'light') {
                    isDark = false;
                } else {
                    isDark = !!(this.mq && this.mq.matches);
                }

                this.html.classList.toggle('dark', isDark);

                if (this.button) this.button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                this.renderIcon && this.renderIcon(isDark);
                try { localStorage.setItem(this.storageKey, pref); } catch (e) { /* ignore */ }
            }

            renderIcon(isDark) {
                if (!this.icon) return;
                this.icon.innerHTML = isDark
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-11.66l-.7.7M4.04 4.04l-.7.7M21 12h-1M4 12H3m15.66 5.66l-.7-.7M4.04 19.96l-.7-.7"/>';
            }

            cycleTheme() {
                const current = this.getCurrentTheme();
                const next = current === 'dark' ? 'light' : current === 'light' ? 'system' : 'dark';
                this.applyTheme(next);
            }

            init() {
                const saved = this.getCurrentTheme();
                this.applyTheme(saved);
                if (this.button) this.button.addEventListener('click', () => this.cycleTheme());
                if (this.mq && this.mq.addEventListener) {
                    this.mq.addEventListener('change', () => {
                        if (this.getCurrentTheme() === 'system') this.applyTheme('system');
                    });
                }
            }
        }

        // ---------- Toast (Accessible) ----------
        window.toast = function(message, type = 'success', duration = 5000) {
            const regionId = 'site-toast-region';
            let liveRegion = document.getElementById(regionId);
            if (!liveRegion) {
                liveRegion = document.createElement('div');
                liveRegion.id = regionId;
                liveRegion.setAttribute('aria-live', 'polite');
                liveRegion.setAttribute('aria-atomic', 'true');
                liveRegion.className = 'sr-only';
                document.body.appendChild(liveRegion);
            }

            const toastEl = document.createElement('div');
            toastEl.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 ease-out ${type === 'error' ? 'bg-red-600' : type === 'warn' ? 'bg-yellow-600' : 'bg-emerald-600'} text-white max-w-sm`;
            toastEl.setAttribute('role', 'status');

            toastEl.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'error' ? 'M12 9v2m0 4h.01' : type === 'warn' ? 'M12 8v4m0 4h.01' : 'M5 13l4 4L19 7'}"></path>
                    </svg>
                    <span class="font-medium">${escapeHtml(message)}</span>
                </div>
            `;
            document.body.appendChild(toastEl);

            // announce to screen reader
            liveRegion.textContent = message;

            // Animate in
            requestAnimationFrame(() => toastEl.classList.remove('translate-x-full'));

            // Animate out
            setTimeout(() => {
                toastEl.classList.add('translate-x-full');
                setTimeout(() => toastEl.remove(), 350);
            }, duration);
        };

        // ---------- Search Manager ----------
        class SearchManager {
            constructor() {
                this.input = document.getElementById('topbar-search');
                this.results = document.getElementById('topbar-search-results');
                this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || window.location.origin;
                this.csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                this.debounced = debounce(this.doSearch.bind(this), 250);
                this.searchHistory = JSON.parse(localStorage.getItem('anc_search_history') || '[]');
                this.maxHistory = 5;
                if (this.input) this.init();
            }

            init() {
                // Shortcut to focus search
                document.addEventListener('keydown', (e) => {
                    if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                        e.preventDefault();
                        this.input.focus();
                        this.input.select();
                    }
                });

                this.input.addEventListener('input', (e) => {
                    const query = e.target.value.trim();
                    if (!query) {
                        this.showHistory();
                        return;
                    }
                    this.debounced(query);
                });

                // keyboard nav
                this.input.addEventListener('keydown', (e) => {
                    const visible = this.results && !this.results.classList.contains('hidden');
                    if (!visible) return;

                    const items = Array.from(this.results.querySelectorAll('[role="option"]'));
                    if (!items.length) return;
                    let activeIndex = items.findIndex(item => item.getAttribute('aria-selected') === 'true');

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const next = activeIndex + 1 < items.length ? activeIndex + 1 : 0;
                        this.setActive(items, next);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const prev = activeIndex > 0 ? activeIndex - 1 : items.length - 1;
                        this.setActive(items, prev);
                    } else if (e.key === 'Enter' && activeIndex >= 0) {
                        e.preventDefault();
                        items[activeIndex].click();
                    } else if (e.key === 'Escape') {
                        this.hideResults();
                        this.input.blur();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!this.input.contains(e.target) && !this.results.contains(e.target)) {
                        this.hideResults();
                    }
                });

                this.showHistory();
            }

            showHistory() {
                if (!this.results) return;
                if (this.searchHistory.length === 0) {
                    this.results.innerHTML = '<p class="p-3 text-sm text-gray-500 italic">Start typing to search</p>';
                } else {
                    this.results.innerHTML = `
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Recent Searches</h5>
                            ${this.searchHistory.slice(0, this.maxHistory).map(query => `
                                <a href="${this.baseUrl}/search?q=${encodeURIComponent(query)}" role="option" aria-selected="false" class="block p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">${escapeHtml(query)}</a>
                            `).join('')}
                        </div>
                    `;
                }
                this.results.classList.remove('hidden');
                this.input.setAttribute('aria-expanded', 'true');

                // allow clicking history items
                this.results.querySelectorAll('a[role="option"]').forEach(a => {
                    a.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        const href = a.getAttribute('href');
                        window.location.href = href;
                    });
                });
            }

            setActive(items, index) {
                items.forEach((item, i) => {
                    item.setAttribute('aria-selected', i === index ? 'true' : 'false');
                    item.classList.toggle('bg-blue-50 dark:bg-blue-900/50', i === index);
                    item.classList.toggle('text-blue-700 dark:text-blue-300', i === index);
                });
                if (items[index]) items[index].scrollIntoView({ block: 'nearest' });
            }

            async doSearch(query) {
                if (!query || !this.results) return;
                this.input.setAttribute('aria-busy', 'true');
                this.results.innerHTML = '<div class="p-3"><div class="spinner-primary inline-block mr-2" aria-hidden="true"></div>Searching...</div>';
                this.results.classList.remove('hidden');

                try {
                    const res = await fetch(`${this.baseUrl}/search?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf || '', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const data = await res.json();
                    this.render(data);
                    this.saveToHistory(query);
                } catch (err) {
                    console.error('Search error:', err);
                    this.results.innerHTML = '<p class="p-3 text-sm text-red-600 dark:text-red-400">Search failed. Please try again.</p>';
                } finally {
                    this.input.removeAttribute('aria-busy');
                }
            }

            render(results = []) {
                if (!this.results) return;
                if (!Array.isArray(results) || results.length === 0) {
                    this.results.innerHTML = '<p class="p-3 text-sm text-gray-500 italic">No results found</p>';
                    return;
                }

                this.results.innerHTML = results.map((item, index) => {
                    const url = escapeHtml(item.url || '#');
                    const name = escapeHtml(item.name || 'Item');
                    const type = escapeHtml(item.type || 'Record');
                    const icon = escapeHtml(item.icon || (item.type ? item.type.charAt(0).toUpperCase() : 'P'));
                    return `
                        <a href="${url}" role="option" aria-selected="false" class="block p-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                                    ${icon}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white truncate">${name}</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${type}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    `;
                }).join('');

                // Make results navable and clickable
                this.results.querySelectorAll('a[role="option"]').forEach(a => {
                    a.addEventListener('click', (ev) => {
                        // let link behave normally (navigate)
                    });
                });

                this.input.setAttribute('aria-expanded', 'true');
            }

            saveToHistory(query) {
                try {
                    let history = JSON.parse(localStorage.getItem('anc_search_history') || '[]');
                    history = history.filter(q => q !== query);
                    history.unshift(query);
                    history = history.slice(0, this.maxHistory);
                    localStorage.setItem('anc_search_history', JSON.stringify(history));
                } catch (e) { /* ignore storage errors */ }
            }

            hideResults() {
                if (!this.results) return;
                this.results.classList.add('hidden');
                this.input.setAttribute('aria-expanded', 'false');
                this.results.innerHTML = '';
            }
        }

        // ---------- Sidebar Manager (gestures/focus trap) ----------
        class SidebarManager {
    constructor() {
        this.mobileSidebar = document.getElementById('mobile-sidebar');
        this.overlay = document.getElementById('mobile-sidebar-overlay');
        this.openBtn = document.getElementById('mobile-menu-button');
        this.closeBtn = document.getElementById('close-mobile-sidebar');
        this.isOpen = false;
        this._startX = 0;
        this._currentX = 0;

        if (this.mobileSidebar && this.overlay && this.openBtn && this.closeBtn) {
            this.init();
            this.initGestures();
        } else {
            // Provide graceful no-op if elements missing
            // still attach openBtn if present
            if (this.openBtn) this.openBtn.addEventListener('click', () => this.open());
        }
    }

    init() {
        this.openBtn?.addEventListener('click', () => this.open());
        this.closeBtn?.addEventListener('click', () => this.close());
        this.overlay?.addEventListener('click', () => this.close());
        // close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) this.close();
        });
    }

    initGestures() {
        // allow swipe from left edge to open
        let touchStartX = 0;
        let touchStartTime = 0;
        document.addEventListener('touchstart', (e) => {
            if (e.touches && e.touches[0]) {
                touchStartX = e.touches[0].clientX;
                touchStartTime = Date.now();
            }
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (!touchStartX) return;
            const dx = (e.changedTouches[0].clientX || 0) - touchStartX;
            const dt = Date.now() - touchStartTime;
            // quick swipe from left edge
            if (touchStartX < 25 && dx > 60 && dt < 500) {
                this.open();
            }
            touchStartX = 0;
        }, { passive: true });

        // drag-to-close on sidebar itself
        let startX = 0, dragging = false;
        this.mobileSidebar.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            dragging = true;
        }, { passive: true });

        this.mobileSidebar.addEventListener('touchmove', (e) => {
            if (!dragging) return;
            const currentX = e.touches[0].clientX;
            const diff = currentX - startX;
            if (diff < 0) {
                this.mobileSidebar.style.transform = `translateX(${Math.max(diff, -320)}px)`;
            }
        }, { passive: true });

        this.mobileSidebar.addEventListener('touchend', (e) => {
            if (!dragging) return;
            dragging = false;
            const endX = e.changedTouches[0].clientX;
            const diff = endX - startX;
            if (diff < -60) this.close();
            else this.mobileSidebar.style.transform = 'translateX(0)';
        }, { passive: true });
    }

    // small focus trap for accessibility when sidebar opens
    trapFocus() {
        const focusable = this.mobileSidebar.querySelectorAll('a,button,input,textarea,select,[tabindex]:not([tabindex="-1"])');
        if (!focusable || !focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        const handleKey = (e) => {
            if (e.key !== 'Tab') return;
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        };
        document.addEventListener('keydown', handleKey);
        this._focusTrapHandler = handleKey;
        // focus the first focusable element
        first.focus();
    }

    releaseTrap() {
        if (this._focusTrapHandler) {
            document.removeEventListener('keydown', this._focusTrapHandler);
            this._focusTrapHandler = null;
        }
    }

    open() {
        if (!this.mobileSidebar || !this.overlay) return;
        this.isOpen = true;
        this.mobileSidebar.setAttribute('aria-hidden', 'false');
        document.body.classList.add('mobile-sidebar-open'); 
        this.openBtn.classList.add('hamburger-open'); 
        this.trapFocus();
    }

    close() {
        if (!this.mobileSidebar || !this.overlay) return;
        this.isOpen = false;
        this.mobileSidebar.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('mobile-sidebar-open'); 
        this.openBtn.classList.remove('hamburger-open'); 
        this.mobileSidebar.style.transform = 'translateX(0)';
        this.releaseTrap();
    }
}

        // ---------- Global utility: safe element selector ----------
        const q = (selector) => document.querySelector(selector);

        // ---------- Global Initialization ----------
        document.addEventListener('DOMContentLoaded', () => {
            App.loader = new Loader();
            App.theme = new ThemeManager();
            // init optional managers only if their DOM pieces exist
            try {
                @auth
                App.notifications = new NotificationManager();
                App.search = new SearchManager();
                App.sidebar = new SidebarManager();
                @endauth
            } catch (e) {
                console.warn('Some features failed to initialize', e);
            }

            // user menu toggles
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !userMenu.classList.contains('hidden');
                    userMenu.classList.toggle('hidden');
                    userMenuButton.setAttribute('aria-expanded', (!isOpen).toString());
                    if (!isOpen) userMenu.querySelector('a,button')?.focus();
                });

                document.addEventListener('click', (e) => {
                    if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                        userMenuButton.setAttribute('aria-expanded', 'false');
                    }
                });

                userMenu.addEventListener('keydown', (e) => {
                    const links = Array.from(userMenu.querySelectorAll('a, button'));
                    let currentIndex = links.findIndex(link => document.activeElement === link);
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        links[(currentIndex + 1) % links.length].focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        links[(currentIndex - 1 + links.length) % links.length].focus();
                    } else if (e.key === 'Escape') {
                        userMenu.classList.add('hidden');
                        userMenuButton.focus();
                    }
                });
            }

            // Auto-hide loader on page load complete
            if (document.readyState === 'loading') {
                document.addEventListener('readystatechange', () => {
                    if (document.readyState === 'complete') App.loader.hide();
                });
            } else {
                App.loader.hide();
            }

            // Form submission shows loader (skip elements with data-no-loader)
            document.addEventListener('submit', (e) => {
                if (e.target && e.target.tagName === 'FORM' && !e.target.hasAttribute('data-no-loader')) {
                    // By default spinner-only (no message). Pass a message if you want text+progress.
                    App.loader?.show('', 10000);
                }
            });

            // Global error handling
            window.addEventListener('error', (e) => {
                console.error('Global error:', e.error || e);
                App.loader?.hide();
                toast('An unexpected error occurred. Please refresh the page.', 'error');
            });

            window.addEventListener('unhandledrejection', (e) => {
                console.error('Unhandled promise rejection:', e.reason);
                App.loader?.hide();
                toast('A network error occurred. Please check your connection.', 'error');
            });
        });
    })();
    </script>
</body>
</html>
