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
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-3 py-2 rounded-md text-sm font-medium ring-2"
   style="background:var(--bg); color:var(--text); ring-color:var(--brand);">
    Skip to content
</a>

{{-- Global loader – add IDs for JS --}}
<div id="global-loader" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="loader-backdrop absolute inset-0"></div>
    <div class="loader-card relative z-10 w-full max-w-sm mx-4 rounded-xl p-5">
        <div class="flex items-center gap-3">
            <svg class="h-10 w-10 animate-spin" viewBox="0 0 48 48">
                <circle cx="24" cy="24" r="18" stroke="rgba(0,0,0,0.08)" stroke-width="4" fill="none"></circle>
                <circle cx="24" cy="24" r="18" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                        stroke-dasharray="85 113" stroke-dashoffset="0" fill="none"
                        transform="rotate(-90 24 24)"></circle>
            </svg>
            <div>
                <h3 id="global-loader-message" class="text-sm font-semibold body-text">Loading…</h3>
                <p id="global-loader-sub" class="text-xs muted-text">Please wait.</p>
            </div>
        </div>
        <div id="global-loader-progress-wrap" class="mt-3 w-full h-1 bg-gray-200 rounded-full overflow-hidden hidden">
            <div id="global-loader-progress" class="h-full bg-gradient-to-r from-brand to-success transition-all duration-300" style="width:0%"></div>
        </div>
        <button id="global-loader-close" class="absolute top-2 right-2 text-muted hover:text-text hidden" aria-label="Close loader">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
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
    // 1. Loader – now uses the IDs above
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
        }

        show(message = '', timeout = 15000, sub = '') {
            if (!this.loader) return;
            const hasMessage = !!message;

            this.loader.classList.remove('hidden');
            this.loader.style.opacity = '1';
            this.loader.setAttribute('aria-hidden', 'false');

            // Text
            if (this.messageEl) this.messageEl.textContent = message || 'Loading…';
            if (this.subEl) this.subEl.textContent = sub || 'Please wait.';
            this.messageEl.style.display = hasMessage ? '' : 'none';
            this.subEl.style.display = hasMessage ? '' : 'none';
            this.closeBtn.style.display = hasMessage ? '' : 'none';

            // Progress
            this.progressWrap.style.display = hasMessage ? '' : 'none';
            this.progressEl.style.width = '0%';
            this.progress = 0;
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && hasMessage) {
                this.animateProgress();
            }

            // Timeout
            if (timeout && hasMessage) {
                clearTimeout(this.timeoutId);
                this.timeoutId = setTimeout(() => this.hide(), timeout);
            }
        }

        animateProgress() {
            if (this._animating) return;
            this._animating = true;
            const step = () => {
                if (this.progress < 95) {
                    this.progress = Math.min(95, this.progress + Math.random() * 8);
                    this.progressEl.style.width = `${Math.floor(this.progress)}%`;
                    setTimeout(step, 200 + Math.random() * 200);
                } else {
                    this._animating = false;
                }
            };
            step();
        }

        hide() {
            if (!this.loader) return;
            this.progressEl.style.width = '100%';
            clearTimeout(this.timeoutId);
            this.loader.style.opacity = '0';
            setTimeout(() => {
                this.loader.classList.add('hidden');
                this.loader.setAttribute('aria-hidden', 'true');
                this.progress = 0;
                this.progressEl.style.width = '0%';
                this.messageEl.textContent = '';
                this.subEl.textContent = '';
            }, 300);
        }
    }

    // -------------------------------------------------
    // 2. Theme Manager – one instance, no duplicate class
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
        container.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg max-w-sm text-white flex items-center gap-3 transform translate-x-full transition-transform duration-300`;
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
            <span class="font-medium">${message}</span>
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
                window.App.loader.show();
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>