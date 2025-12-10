<!DOCTYPE html>
<html lang="en" class="transition-all duration-300">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', config('app.name', 'ANC Clinic'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="color-scheme" content="light dark">
</head>

<body class="bg-app text-body font-inter antialiased min-h-screen flex flex-col">

    {{-- Skip-link --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-3 py-2 rounded-md text-sm font-medium ring-2 bg-surface text-body ring-brand">
        Skip to content
    </a>

    {{-- GLOBAL LOADER --}}
    <div id="global-loader" 
         class="hidden fixed inset-0 z-[100] items-center justify-center transition-opacity duration-300 opacity-0"
         aria-hidden="true">
        <div class="absolute inset-0 bg-white/60 dark:bg-black/60 backdrop-blur-sm transition-all"></div>
        <div class="relative z-10 flex flex-col items-center justify-center text-center p-6">
            <svg class="w-12 h-12 text-brand animate-spin mb-4 drop-shadow-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div class="space-y-1 mb-4">
                <h3 id="global-loader-message" class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">Loading...</h3>
            </div>
            <div id="global-loader-progress-wrap" class="w-48 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden hidden shadow-inner">
                <div id="global-loader-progress" class="h-full bg-brand rounded-full transition-all duration-300 ease-out" style="width:0%"></div>
            </div>
            <button id="global-loader-close" class="mt-6 text-xs font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 underline underline-offset-2 hidden transition-colors">
                Cancel
            </button>
        </div>
    </div>

    {{-- Topbar --}}
    <header class="site-topbar sticky top-0 z-40 backdrop-blur supports-backdrop-blur:bg-opacity-90 border-b border-border bg-surface/80">
        @include('partials.topbar')
    </header>

    {{-- Main content --}}
    <div id="main-content" class="flex-1 relative w-full" tabindex="-1">
        <div class="absolute inset-0 pointer-events-none page-overlay-gradient"></div>

        <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 lg:py-8 flex gap-8">

            {{-- DESKTOP SIDEBAR --}}
            @auth
            <aside id="sidebar" class="hidden lg:block w-64 flex-shrink-0">
                <div class="sticky top-24 space-y-6">
                    @include('partials.sidebar')
                </div>
            </aside>
            @endauth

            {{-- MAIN AREA --}}
            <main class="flex-1 min-w-0">
                {{-- Removed the wrapping .card class here to let inner content define its own cards --}}
                <div class="space-y-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="site-footer mt-auto border-t border-border bg-surface/50">
        @include('partials.anc_footer')
    </footer>

    {{-- SCRIPTS --}}
    <script>
        window.__CSRF_TOKEN = "{{ csrf_token() }}";
        window.__NOTIF_API = {
            latest: "{{ route('notifications.api.latest') }}",
            clear : "{{ route('notifications.api.clear') }}",
            markRead: "{{ route('notifications.api.markRead') }}"
        };

        // 1. Loader
        class Loader {
            constructor() {
                this.loader = document.getElementById('global-loader');
                this.messageEl = document.getElementById('global-loader-message');
                this.progressWrap = document.getElementById('global-loader-progress-wrap');
                this.progressEl = document.getElementById('global-loader-progress');
                this.closeBtn = document.getElementById('global-loader-close');
                this.timeoutId = null;
                this.progress = 0;
                this._animating = false;

                if(this.closeBtn) {
                    this.closeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.hide();
                    });
                }
            }

            show(message = '') {
                if (!this.loader) return;
                
                // Reset cancel button visibility every time we show
                if (this.closeBtn) this.closeBtn.style.display = 'none';

                const hasMessage = !!message;

                this.loader.classList.remove('hidden');
                requestAnimationFrame(() => {
                    this.loader.classList.add('flex');
                    this.loader.style.opacity = '1';
                });
                
                this.loader.setAttribute('aria-hidden', 'false');
                if (this.messageEl) this.messageEl.textContent = message || 'Loading...';
                
                if (this.progressWrap) {
                    this.progressWrap.classList.toggle('hidden', !hasMessage);
                    this.progressEl.style.width = '0%';
                    this.progress = 0;
                    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && hasMessage) {
                        this.animateProgress();
                    }
                }

                // Only show cancel button if loading takes unusually long (e.g. 10s)
                // implying something might be stuck, but generally we assume it works.
                if (this.timeoutId) clearTimeout(this.timeoutId);
                this.timeoutId = setTimeout(() => {
                    if(this.closeBtn && this.loader.style.opacity === '1') {
                        this.closeBtn.style.display = 'block';
                    }
                }, 10000); 
            }

            animateProgress() {
                if (this._animating) return;
                this._animating = true;
                const step = () => {
                    if (this.loader.style.opacity === '0') {
                         this._animating = false; 
                         return;
                    }
                    if (this.progress < 90) {
                        const increment = Math.random() * (this.progress > 70 ? 2 : 8);
                        this.progress = Math.min(90, this.progress + increment);
                        this.progressEl.style.width = `${Math.floor(this.progress)}%`;
                        setTimeout(step, 200 + Math.random() * 300);
                    } else {
                        this._animating = false;
                    }
                };
                step();
            }

            hide() {
                if (!this.loader) return;
                if (this.progressEl) this.progressEl.style.width = '100%';
                if (this.timeoutId) clearTimeout(this.timeoutId);
                
                this.loader.style.opacity = '0';
                
                setTimeout(() => {
                    this.loader.classList.add('hidden');
                    this.loader.classList.remove('flex');
                    this.loader.setAttribute('aria-hidden', 'true');
                    this.progress = 0;
                    if (this.progressEl) this.progressEl.style.width = '0%';
                    if (this.messageEl) this.messageEl.textContent = 'Loading...';
                    if (this.closeBtn) this.closeBtn.style.display = 'none';
                }, 300);
            }
        }

        // 2. Theme Manager
        class ThemeManager {
            constructor() {
                this.storageKey = 'anc_theme_pref_v2';
                this.button = document.getElementById('theme-toggle-main');
                this.icon   = document.getElementById('theme-icon');
                this.html   = document.documentElement;
                this.init();
            }

            // Icons
            get sunIcon() { return `<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.263l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />`; }
            get moonIcon() { return `<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />`; }

            applyTheme(isDark) {
                if (isDark) {
                    this.html.classList.add('dark');
                    this.html.style.colorScheme = 'dark';
                    if(this.icon) this.icon.innerHTML = this.sunIcon;
                    localStorage.setItem(this.storageKey, 'dark');
                } else {
                    this.html.classList.remove('dark');
                    this.html.style.colorScheme = 'light';
                    if(this.icon) this.icon.innerHTML = this.moonIcon;
                    localStorage.setItem(this.storageKey, 'light');
                }
            }

            init() {
                const saved = localStorage.getItem(this.storageKey);
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                let isDark = saved === 'dark' || (!saved && systemDark);
                
                this.applyTheme(isDark);

                this.button?.addEventListener('click', (e) => {
                    e.preventDefault();
                    isDark = !isDark;
                    this.applyTheme(isDark);
                });
            }
        }

        // 3. Toast
        window.toast = function(message, type = 'success', duration = 5000) {
            const container = document.createElement('div');
            container.className = `fixed top-4 right-4 z-[9999] p-4 rounded-xl shadow-lg max-w-sm text-white flex items-center gap-3 transform translate-x-full transition-transform duration-300`;
            container.setAttribute('role', 'status');
            container.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

            const colors = { success: 'bg-emerald-600', error: 'bg-red-600', warn: 'bg-amber-500', info: 'bg-blue-600' };
            container.classList.add(colors[type] || colors.success);

            container.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="${type === 'error' ? 'M12 9v2m0 4h.01' : 'M5 13l4 4L19 7'}"></path>
                </svg>
                <span class="font-medium text-sm">${message}</span>
                <button class="ml-auto text-white/70 hover:text-white" aria-label="Dismiss">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;

            document.body.appendChild(container);
            requestAnimationFrame(() => container.classList.remove('translate-x-full'));

            const dismiss = () => {
                container.classList.add('translate-x-full');
                setTimeout(() => container.remove(), 350);
            };
            container.querySelector('button').addEventListener('click', dismiss);
            setTimeout(dismiss, duration);
        };

        // 4. Init
        document.addEventListener('DOMContentLoaded', () => {
            window.App = window.App || {};
            window.App.loader = new Loader();
            window.App.theme  = new ThemeManager();

            // Hide loader on initial page load
            if (document.readyState === 'complete') {
                window.App.loader.hide();
            } else {
                window.addEventListener('load', () => window.App.loader.hide());
            }

            // Handle Form Submissions Smartly
            document.addEventListener('submit', e => {
                const form = e.target;
                if (form.tagName === 'FORM' && !form.hasAttribute('data-no-loader')) {
                    // Check validity first. If invalid, browser handles it, we do NOT show loader.
                    if (!form.checkValidity()) return;

                    // Show loader immediately
                    window.App.loader.show();

                    // If Laravel returns validation errors, the page reloads (loader hides on new page load).
                    // BUT, if it's an Ajax form or if the user clicks "Back" to fix an error, we must hide it.
                }
            });

            // Handle Back/Forward Cache (Mobile Safari/Chrome issue)
            // If user submits form, gets error, hits "Back", loader should be gone.
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    window.App.loader.hide();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>