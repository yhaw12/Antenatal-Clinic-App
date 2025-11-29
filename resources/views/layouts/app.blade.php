<!DOCTYPE html>
<html lang="en" class="transition-all duration-300">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- 
        ====================================================
        UPDATED GLOBAL LOADER (Clean, No Box, Glass Effect)
        ====================================================
    --}}
    <div id="global-loader" 
         class="hidden fixed inset-0 z-[100] items-center justify-center transition-opacity duration-300 opacity-0"
         aria-hidden="true">
        
        <!-- Glass Backdrop (Blurred background instead of solid box) -->
        <div class="absolute inset-0 bg-white/60 dark:bg-black/60 backdrop-blur-md transition-all"></div>

        <!-- Centered Floating Content -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center p-6">
            
            <!-- Spinner (Brand Color) -->
            <svg class="w-12 h-12 text-brand animate-spin mb-4 drop-shadow-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <!-- Text -->
            <div class="space-y-1 mb-4">
                <h3 id="global-loader-message" class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">Loading...</h3>
                {{-- <p id="global-loader-sub" class="text-sm text-gray-500 dark:text-gray-300 font-medium">Please wait a moment</p> --}}
            </div>

            <!-- Minimalist Progress Bar -->
            <div id="global-loader-progress-wrap" class="w-48 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden hidden shadow-inner">
                <div id="global-loader-progress" class="h-full bg-brand rounded-full transition-all duration-300 ease-out" style="width:0%"></div>
            </div>

            <!-- Close Button (Hidden by default, for stuck states) -->
            <button id="global-loader-close" class="mt-6 text-xs font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 underline underline-offset-2 hidden transition-colors">
                Cancel
            </button>
        </div>
    </div>

    {{-- Topbar --}}
    <header class="site-topbar sticky top-0 z-40 backdrop-blur supports-backdrop-blur:bg-opacity-90">
        @include('partials.topbar')
    </header>

    {{-- Main content --}}
    <div id="main-content" class="flex-1 relative" tabindex="-1">
        <div class="absolute inset-0 pointer-events-none page-overlay-gradient"></div>

        <div class="relative z-10 max-w-7xl mx-auto w-full px-4 py-4 flex gap-4">

            @auth
            <aside id="sidebar" class="hidden lg:block w-56 flex-shrink-0 border-surface rounded-lg p-2 card-hover">
                @include('partials.sidebar')
            </aside>
            @endauth

            <main class="flex-1 min-w-0">
                <div class="card p-4 card-hover">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="site-footer mt-10">
        @include('partials.anc_footer')
    </footer>

    {{-- -------------------------------------------------
         GLOBAL JS (loader, theme, CSRF, toast)
    -------------------------------------------------- --}}
    <script>
        // CSRF & Routes
        window.__CSRF_TOKEN = "{{ csrf_token() }}";
        window.__NOTIF_API = {
            latest: "{{ route('notifications.api.latest') }}",
            clear : "{{ route('notifications.api.clear') }}",
            markRead: "{{ route('notifications.api.markRead') }}"
        };

        // -------------------------------------------------
        // 1. Loader – Updated for new UI
        // -------------------------------------------------
        class Loader {
            constructor() {
                this.loader = document.getElementById('global-loader');
                this.messageEl = document.getElementById('global-loader-message');
                this.subEl = document.getElementById('global-loader-sub');
                this.progressWrap = document.getElementById('global-loader-progress-wrap');
                this.progressEl = document.getElementById('global-loader-progress');
                this.closeBtn = document.getElementById('global-loader-close');
                this.timeoutId = null;
                this.progress = 0;
                this._animating = false;

                // Attach close event
                if(this.closeBtn) {
                    this.closeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.hide();
                    });
                }
            }

            show(message = '', timeout = 15000, sub = '') {
                if (!this.loader) return;
                const hasMessage = !!message;

                // Prepare UI
                this.loader.classList.remove('hidden');
                // Small delay to allow display:flex to apply before opacity transition
                requestAnimationFrame(() => {
                    this.loader.classList.add('flex');
                    this.loader.style.opacity = '1';
                });
                
                this.loader.setAttribute('aria-hidden', 'false');

                // Text Updates
                if (this.messageEl) this.messageEl.textContent = message || 'Loading...';
                // if (this.subEl) this.subEl.textContent = sub || 'Please wait a moment';
                
                // Visibility Toggles
                if (this.subEl) this.subEl.style.display = sub || !message ? 'block' : 'none'; 
                if (this.closeBtn) this.closeBtn.style.display = 'none'; // Reset close btn

                // Progress Bar Logic
                if (this.progressWrap) {
                    this.progressWrap.classList.toggle('hidden', !hasMessage);
                    this.progressEl.style.width = '0%';
                    this.progress = 0;
                    
                    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && hasMessage) {
                        this.animateProgress();
                    }
                }

                // Timeout Logic (Safety valve)
                if (timeout) {
                    clearTimeout(this.timeoutId);
                    this.timeoutId = setTimeout(() => {
                        // Instead of hiding immediately, show the "Cancel" button after timeout
                        if(this.closeBtn) this.closeBtn.style.display = 'block';
                    }, timeout);
                }
            }

            animateProgress() {
                if (this._animating) return;
                this._animating = true;
                const step = () => {
                    // Stop if hidden
                    if (this.loader.style.opacity === '0') {
                         this._animating = false; 
                         return;
                    }

                    if (this.progress < 90) {
                        // Slow down as it gets higher
                        const increment = Math.random() * (this.progress > 70 ? 2 : 8);
                        this.progress = Math.min(90, this.progress + increment);
                        this.progressEl.style.width = `${Math.floor(this.progress)}%`;
                        
                        // Randomize speed
                        setTimeout(step, 200 + Math.random() * 300);
                    } else {
                        this._animating = false;
                    }
                };
                step();
            }

            hide() {
                if (!this.loader) return;
                
                // Finish progress bar fast for visual satisfaction
                if (this.progressEl) this.progressEl.style.width = '100%';
                
                clearTimeout(this.timeoutId);
                
                // Fade out
                this.loader.style.opacity = '0';
                
                // Wait for transition to finish before display:none
                setTimeout(() => {
                    this.loader.classList.add('hidden');
                    this.loader.classList.remove('flex');
                    this.loader.setAttribute('aria-hidden', 'true');
                    
                    // Reset state
                    this.progress = 0;
                    if (this.progressEl) this.progressEl.style.width = '0%';
                    if (this.messageEl) this.messageEl.textContent = 'Loading...';
                    if (this.closeBtn) this.closeBtn.style.display = 'none';
                }, 300); // Matches duration-300 class
            }
        }

        // -------------------------------------------------
        // 2. Theme Manager
        // -------------------------------------------------
        class ThemeManager {
            constructor() {
                this.storageKey = 'anc_theme_pref_v2';
                this.button = document.getElementById('theme-toggle-main');
                this.icon   = document.getElementById('theme-icon');
                this.html   = document.documentElement;
                this.mq     = window.matchMedia?.('(prefers-color-scheme: dark)');
                this.init();
            }

            getCurrentTheme() { return localStorage.getItem(this.storageKey) || 'system'; }

            applyTheme(pref) {
                const isDark = pref === 'dark' ? true
                             : pref === 'light' ? false
                             : !!(this.mq && this.mq.matches);

                this.html.classList.toggle('dark', isDark);
                this.button?.setAttribute('aria-pressed', isDark);
                this.renderIcon(isDark);
                try { localStorage.setItem(this.storageKey, pref); } catch (_) {}
            }

            renderIcon(isDark) {
                if (!this.icon) return;
                this.icon.innerHTML = isDark
                    ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>`
                    : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-11.66l-.7.7M4.04 4.04l-.7.7M21 12h-1M4 12H3m15.66 5.66l-.7-.7M4.04 19.96l-.7-.7"/>`;
            }

            cycleTheme() {
                const cur = this.getCurrentTheme();
                const next = cur === 'dark' ? 'light' : cur === 'light' ? 'system' : 'dark';
                this.applyTheme(next);
            }

            init() {
                this.applyTheme(this.getCurrentTheme());
                this.button?.addEventListener('click', () => this.cycleTheme());
                this.mq?.addEventListener?.('change', () => {
                    if (this.getCurrentTheme() === 'system') this.applyTheme('system');
                });
            }
        }

        // -------------------------------------------------
        // 3. Accessible Toast
        // -------------------------------------------------
        window.toast = function(message, type = 'success', duration = 5000) {
            const container = document.createElement('div');
            container.className = `fixed top-4 right-4 z-[9999] p-4 rounded-xl shadow-lg max-w-sm text-white flex items-center gap-3 transform translate-x-full transition-transform duration-300`;
            container.setAttribute('role', 'status');
            container.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

            const colors = {
                success: 'bg-emerald-600',
                error:   'bg-danger',
                warn:    'bg-accent',
                info:    'bg-brand'
            };
            container.classList.add(colors[type] || colors.success);

            container.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="${type === 'error' ? 'M12 9v2m0 4h.01' : type === 'warn' ? 'M12 8v4m0 4h.01' : 'M5 13l4 4L19 7'}"></path>
                </svg>
                <span class="font-medium text-sm">${message}</span>
                <button class="ml-auto text-white/70 hover:text-white" aria-label="Dismiss">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
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

        // -------------------------------------------------
        // 4. Global init
        // -------------------------------------------------
        document.addEventListener('DOMContentLoaded', () => {
            window.App = window.App || {};
            window.App.loader = new Loader();
            window.App.theme  = new ThemeManager();

            // Hide loader when page is fully loaded
            if (document.readyState === 'complete') {
                window.App.loader.hide();
            } else {
                window.addEventListener('load', () => window.App.loader.hide());
            }

            // Show spinner on form submit (except data-no-loader)
            document.addEventListener('submit', e => {
                if (e.target.tagName === 'FORM' && !e.target.hasAttribute('data-no-loader')) {
                    
                    // --- NEW: Stop if form has validation errors (required fields, etc.) ---
                    if (!e.target.checkValidity()) {
                        return; 
                    }
                    // ----------------------------------------------------------------------

                    window.App.loader.show();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>