<header id="topbar"
        class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between gap-6 sticky top-0 z-50 transition-all duration-300 motion-reduce:transition-none"
        role="banner"
        style="background: color-mix(in srgb, var(--bg) 95%, transparent);
               color:var(--text);
               border-bottom:1px solid var(--border);
               backdrop-filter: blur(10px);
               box-shadow: var(--shadow);">

    {{-- LEFT – LOGO --}}
    <div class="flex items-center gap-4 flex-shrink-0">
        <a href="{{ route('dashboard') }}"
           class="group relative flex items-center gap-3 p-2 rounded-2xl focus:outline-none transition-all duration-300"
           aria-label="ANC Clinic Dashboard – Home">
            <div class="relative overflow-hidden rounded-2xl" aria-hidden="true">
                <img src="{{ asset('images/anc-logo.svg') }}"
                     alt="ANC Clinic logo — antenatal care"
                     class="h-12 w-12 transition-transform group-hover:scale-110 group-focus:scale-110 motion-reduce:transform-none">
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
                     style="background: linear-gradient(135deg,
                                color-mix(in srgb, var(--brand) 22%, transparent),
                                color-mix(in srgb, var(--success) 18%, transparent));"></div>
                <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 shadow-md animate-pulse"
                     style="background:var(--success); border-color:var(--bg)"></div>
            </div>

            <div class="hidden lg:block">
                <div class="font-bold text-xl leading-tight tracking-tight" style="color:var(--text)">
                    {{ config('app.name', 'ANC Clinic') }}
                </div>
                <div class="text-xs font-medium tracking-wide uppercase" style="color:var(--brand)">
                    {{ __('Antenatal Care Excellence') }}
                </div>
            </div>
        </a>

        {{-- MOBILE MENU BUTTON --}}
        <button id="mobile-menu-button" type="button"
                aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobile-sidebar"
                class="lg:hidden relative p-3 rounded-2xl focus:outline-none transition-all duration-300 group"
                style="color:var(--muted);">
            <span class="sr-only">Toggle navigation</span>
            <svg class="h-6 w-6 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path class="hamburger-line origin-center transition-all duration-300"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16"></path>
                <path class="hamburger-line origin-center transition-all duration-300"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M4 12h16"></path>
                <path class="hamburger-line origin-center transition-all duration-300"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M4 18h16"></path>
            </svg>
        </button>
    </div>

    {{-- RIGHT – ACTIONS --}}
    <div class="flex items-center gap-4 flex-shrink-0">

        @auth
        {{-- NOTIFICATIONS --}}
        <div class="relative" data-dropdown-container>
            <button id="notification-bell" data-dropdown type="button"
                    aria-label="View notifications" aria-expanded="false" aria-controls="notification-dropdown"
                    class="relative p-3 rounded-2xl focus:outline-none transition-all duration-300 group"
                    style="color:var(--muted);">
                <svg class="h-6 w-6 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v. persist..."/>
                </svg>

                <span id="notification-count"
                      class="absolute -top-1 -right-1 text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg transform scale-95 transition-all duration-200"
                      style="background: linear-gradient(135deg, var(--danger), color-mix(in srgb,var(--danger) 70%, var(--accent)));
                             color:#fff; border:2px solid var(--bg)">
                    0
                </span>
            </button>

            {{-- DROPDOWN PANEL --}}
            <div id="notification-dropdown"
                 class="hidden absolute right-0 mt-3 w-96 rounded-lg z-50 overflow-hidden transition-all duration-200 transform scale-95 opacity-0"
                 role="region" aria-labelledby="notification-bell" tabindex="-1"
                 style="background: color-mix(in srgb, var(--surface) 98%, transparent);
                        border:1px solid var(--border);
                        box-shadow:var(--shadow);">

                <div class="p-5 border-b backdrop-blur-sm flex items-center justify-between gap-3"
                     style="border-bottom:1px solid var(--border);
                            background: linear-gradient(90deg,
                                color-mix(in srgb,var(--surface) 80%,transparent),
                                color-mix(in srgb,var(--bg) 80%,transparent));">
                    <div>
                        <h3 class="text-base font-bold" style="color:var(--text)">Notifications</h3>
                        <p class="text-sm" style="color:var(--muted); margin-top:4px">
                            You have <span id="unread-count" class="font-semibold" style="color:var(--brand)">0</span> unread alerts
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button id="notification-refresh" type="button"
                                class="p-2 rounded-md focus:outline-none transition-colors"
                                aria-label="Refresh notifications" style="color:var(--muted);">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6"/>
                            </svg>
                        </button>

                        <button id="mark-all-read" type="button"
                                class="p-2 rounded-md focus:outline-none transition-colors text-sm"
                                aria-label="Mark all as read" style="color:var(--muted);">
                            Mark all read
                        </button>

                        <button id="dismiss-all-notifications" type="button"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium transition-all"
                                aria-label="Clear all notifications"
                                style="background: color-mix(in srgb, var(--danger) 10%, transparent);
                                       color: color-mix(in srgb, var(--danger) 85%, black);">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>Clear</span>
                        </button>
                    </div>
                </div>

                <div id="notification-list" class="max-h-96 overflow-y-auto" tabindex="0"
                     role="list" aria-label="Notification list" style="color:var(--text)"></div>

                <div class="p-5 border-t"
                     style="border-top:1px solid var(--border);
                            background: linear-gradient(90deg,
                                color-mix(in srgb,var(--bg) 85%,transparent),
                                color-mix(in srgb,var(--surface) 85%,transparent));">
                    <a href="{{ route('notifications.index') }}"
                       class="block w-full text-center py-2 px-4 rounded-lg font-medium transition-all shadow-lg transform hover:-translate-y-0.5"
                       style="background:var(--brand); color:#fff;">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        View All Notifications
                    </a>
                </div>
            </div>
        </div>
        @endauth

        {{-- THEME TOGGLE --}}
        <button id="theme-toggle-main" type="button"
                aria-label="Cycle theme (Light / Dark / System)" aria-pressed="false"
                title="Cycle themes: Light, Dark, System"
                class="relative p-3 rounded-2xl focus:outline-none transition-all duration-300 group"
                style="color:var(--muted);">
            <svg id="theme-icon" class="w-6 h-6 transition-all duration-300 group-hover:scale-110"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <!-- Sun (default) -->
                <path class="opacity-100" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                <!-- Moon (filled when dark) -->
                <path class="opacity-0" fill="currentColor"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <span class="sr-only">Toggle theme</span>
        </button>

        @auth
        {{-- USER MENU --}}
        <div class="relative" data-dropdown-container>
            <button id="user-menu-button" data-dropdown type="button"
                    aria-label="User menu" aria-controls="user-menu" aria-expanded="false"
                    class="flex items-center gap-3 p-3 rounded-2xl focus:outline-none transition-all duration-300 group"
                    style="color:var(--text);">
                <div class="relative">
                    <img src="{{ auth()->user()->avatar ? asset('storage/avatars/'.auth()->user()->avatar) : asset('images/default-avatar.png') }}"
                         alt="{{ auth()->user()->name ?? 'User' }} avatar"
                         class="w-10 h-10 rounded-2xl object-cover ring-2 shadow-md transition-transform group-hover:scale-105"
                         style="border-color:var(--border)">
                    <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full border-2"
                         style="background:var(--success); border-color:var(--bg)"></div>
                </div>

                <div class="hidden lg:block min-w-0 flex-1">
                    <div class="text-sm font-semibold truncate" style="color:var(--text)">
                        {{ auth()->user()->name ?? 'User' }}
                    </div>
                    <div class="text-xs truncate" style="color:var(--muted)">
                        {{ auth()->user()->role ?? 'Nurse' }}
                    </div>
                </div>

                <svg class="h-4 w-4 transition-transform duration-300 group-hover:rotate-180"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div id="user-menu"
                 class="hidden absolute right-0 mt-2 w-64 rounded-2xl z-50 overflow-hidden transition-all duration-200 origin-top-right transform scale-95 opacity-0"
                 role="menu" aria-labelledby="user-menu-button" tabindex="-1"
                 style="background: color-mix(in srgb, var(--surface) 98%, transparent);
                        border:1px solid var(--border);
                        box-shadow:var(--shadow);">
                {{-- user info --}}
                <div class="p-4 border-b backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/'.auth()->user()->avatar) : asset('images/default-avatar.png') }}"
                             alt="User avatar" class="w-12 h-12 rounded-xl object-cover"
                             style="border:2px solid var(--border)">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-semibold text-sm truncate" style="color:var(--text)">
                                {{ auth()->user()->name ?? 'User' }}
                            </h4>
                            <p class="text-xs truncate" style="color:var(--muted)">
                                {{ auth()->user()->email ?? 'user@example.com' }}
                            </p>
                            <p class="text-xs font-medium" style="color:var(--success)">Active</p>
                        </div>
                    </div>
                </div>

                <div class="py-1 divide-y" style="border-color:var(--border);">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm transition-colors"
                       role="menuitem" tabindex="0" style="color:var(--text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>

                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm transition-colors"
                       role="menuitem" tabindex="0" style="color:var(--text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543.826 3.31 2.37 2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm transition-colors"
                                role="menuitem" tabindex="0" style="color:var(--text)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>

{{-- MOBILE SIDEBAR (slide-in) --}}
<aside id="mobile-sidebar" class="fixed inset-0 z-40 hidden lg:hidden" aria-hidden="true" style="color:var(--text)">
    <div id="mobile-overlay"
         class="absolute inset-0 transition-opacity duration-300 opacity-0 pointer-events-none"
         style="background:rgba(0,0,0,.4); backdrop-filter:blur(6px);"></div>

    <nav id="mobile-panel"
         class="absolute left-0 top-0 h-full w-80 max-w-full transform -translate-x-6 opacity-0 scale-98 transition-all duration-300 motion-reduce:transition-none"
         aria-label="Mobile navigation" role="dialog" aria-modal="true" tabindex="-1"
         style="background:var(--surface); border-right:1px solid var(--border); box-shadow:var(--shadow);">
        <div class="p-4 border-b flex items-center justify-between"
             style="border-bottom:1px solid var(--border);">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/anc-logo.svg') }}" alt="ANC logo" class="w-10 h-10 rounded-md">
                <div>
                    <div class="font-semibold text-sm" style="color:var(--text)">
                        {{ config('app.name', 'ANC Clinic') }}
                    </div>
                    <div class="text-xs" style="color:var(--muted)">Antenatal Care</div>
                </div>
            </div>
            <button id="mobile-close" type="button"
                    class="p-2 rounded-md focus:outline-none transition-all duration-200"
                    aria-label="Close menu" style="color:var(--muted);">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-4 overflow-y-auto h-[calc(100vh-6rem)]" id="mobile-nav-items">
            <ul class="space-y-1">
                <li><a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md transition-colors" style="color:var(--text)">Dashboard</a></li>
                <li><a href="{{ route('patients.index') }}" class="block px-3 py-2 rounded-md transition-colors" style="color:var(--text)">Patients</a></li>
                <li><a href="{{ route('appointments.index') }}" class="block px-3 py-2 rounded-md transition-colors" style="color:var(--text)">Appointments</a></li>
                <li><a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-md transition-colors" style="color:var(--text)">Reports</a></li>
                <li><a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-md transition-colors" style="color:var(--text)">Settings</a></li>
            </ul>

            <div class="mt-6 pt-4 border-t" style="border-top:1px solid var(--border)">
                @auth
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors" style="color:var(--text)">
                    <img src="{{ auth()->user()->avatar ? asset('storage/avatars/'.auth()->user()->avatar) : asset('images/default-avatar.png') }}"
                         alt="" class="w-8 h-8 rounded-md object-cover">
                    <div class="text-sm" style="color:var(--text)">{{ auth()->user()->name ?? 'User' }}</div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md transition-colors" style="color:var(--text)">Sign out</button>
                </form>
                @endauth
            </div>
        </div>
    </nav>
</aside>

<script>
/* ----------------------------------------------------------------------
   1. GLOBAL HELPERS (focus trap, reduced-motion, animation classes)
----------------------------------------------------------------------- */
const $  = (s, el = document) => el.querySelector(s);
const $$ = (s, el = document) => Array.from(el.querySelectorAll(s));
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const enterCls = ['opacity-100', 'scale-100', 'translate-x-0'];
const leaveCls = ['opacity-0', 'scale-95', '-translate-x-6'];

function addCls(el, cls) { cls.forEach(c => el.classList.add(c)); }
function rmCls(el, cls)  { cls.forEach(c => el.classList.remove(c)); }

function show(el, btn) {
    btn?.setAttribute('aria-expanded', 'true');
    el?.setAttribute('aria-hidden', 'false');
    el?.classList.remove('hidden');
    rmCls(el, leaveCls); addCls(el, enterCls);
}
function hide(el, btn) {
    btn?.setAttribute('aria-expanded', 'false');
    el?.setAttribute('aria-hidden', 'true');
    addCls(el, leaveCls); rmCls(el, enterCls);
    if (prefersReduced) el.classList.add('hidden');
    else setTimeout(() => el.classList.add('hidden'), 220);
}

/* ----------------------------------------------------------------------
   2. FOCUS TRAP (used by dropdowns & mobile sidebar)
----------------------------------------------------------------------- */
function trapFocus(container) {
    if (!container) return null;
    const focusables = () => Array.from(container.querySelectorAll(
        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"]), input, textarea, select'
    )).filter(el => {
        const style = getComputedStyle(el);
        return el.offsetWidth || el.offsetHeight || el.getClientRects().length
            && style.visibility !== 'hidden'
            && el.closest('[aria-hidden="true"]') === null;
    });

    const handler = e => {
        if (e.key === 'Tab') {
            const items = focusables();
            if (!items.length) return;
            const first = items[0], last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        } else if (e.key === 'Escape') {
            container.dispatchEvent(new CustomEvent('trap-escape', {bubbles:true}));
        }
    };
    container.addEventListener('keydown', handler);
    return () => container.removeEventListener('keydown', handler);
}

/* ----------------------------------------------------------------------
   3. DROPDOWN WIRING (notifications + user menu)
----------------------------------------------------------------------- */
$$('[data-dropdown]').forEach(btn => {
    const panelId = btn.getAttribute('aria-controls');
    const panel   = panelId ? $(`#${panelId}`) : btn.closest('[data-dropdown-container]')?.querySelector('.absolute');

    if (!panel) return;

    // make panel focusable
    if (!panel.hasAttribute('tabindex')) panel.setAttribute('tabindex', '-1');

    const toggle = () => {
        const open = btn.getAttribute('aria-expanded') === 'true';
        if (open) { hide(panel, btn); if (panel._untrap) { panel._untrap(); panel._untrap = null; } }
        else {
            show(panel, btn);
            panel._untrap = trapFocus(panel);
            const first = panel.querySelector('a, button, [tabindex]:not([tabindex="-1"])');
            (first || panel).focus();
        }
    };
    btn.addEventListener('click', toggle);

    // Escape → close
    panel.addEventListener('trap-escape', () => { hide(panel, btn); if (panel._untrap) { panel._untrap(); panel._untrap = null; } btn.focus(); });

    // Click-outside (capture phase so it fires before other handlers)
    document.addEventListener('click', e => {
        if (btn.getAttribute('aria-expanded') !== 'true') return;
        if (panel.contains(e.target) || btn.contains(e.target)) return;
        hide(panel, btn);
        if (panel._untrap) { panel._untrap(); panel._untrap = null; }
    }, true);
});

/* ----------------------------------------------------------------------
   4. MOBILE SIDEBAR
----------------------------------------------------------------------- */
const mobBtn     = $('#mobile-menu-button');
const mobSidebar = $('#mobile-sidebar');
const mobPanel   = $('#mobile-panel');
const mobOverlay = $('#mobile-overlay');
const mobClose   = $('#mobile-close');
let mobUntrap    = null;

function openMobile() {
    mobSidebar.classList.remove('hidden');
    mobSidebar.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');

    requestAnimationFrame(() => {
        mobOverlay.classList.remove('opacity-0', 'pointer-events-none');
        mobOverlay.classList.add('opacity-100');
        rmCls(mobPanel, ['-translate-x-6','opacity-0','scale-98','scale-95']);
        addCls(mobPanel, ['translate-x-0','opacity-100','scale-100']);
    });

    mobUntrap = trapFocus(mobPanel);
    const first = mobPanel.querySelector('a, button');
    (first || mobPanel).focus();
    mobBtn?.setAttribute('aria-expanded', 'true');
}
function closeMobile() {
    mobBtn?.setAttribute('aria-expanded', 'false');
    mobSidebar.setAttribute('aria-hidden', 'true');

    mobOverlay.classList.add('opacity-0');
    mobOverlay.classList.remove('opacity-100');
    addCls(mobPanel, ['-translate-x-6','opacity-0','scale-95']);
    rmCls(mobPanel, ['translate-x-0','opacity-100','scale-100']);

    document.body.classList.remove('overflow-hidden');
    if (mobUntrap) { mobUntrap(); mobUntrap = null; }

    if (prefersReduced) mobSidebar.classList.add('hidden');
    else setTimeout(() => mobSidebar.classList.add('hidden'), 250);

    mobBtn?.focus();
}
mobBtn?.addEventListener('click', () => (mobBtn.getAttribute('aria-expanded') === 'true' ? closeMobile() : openMobile()));
mobClose?.addEventListener('click', closeMobile);
mobOverlay?.addEventListener('click', closeMobile);

/* ----------------------------------------------------------------------
   5. NOTIFICATIONS (fetch, mark-read, clear, refresh)
----------------------------------------------------------------------- */
(() => {
    const bell   = $('#notification-bell');
    const drop   = $('#notification-dropdown');
    const list   = $('#notification-list');
    const unread = $('#unread-count');
    const badge  = $('#notification-count');
    const refresh= $('#notification-refresh');
    const markAll= $('#mark-all-read');
    const clear  = $('#dismiss-all-notifications');

    if (!bell || !drop || !list) return;

    const API = window.__NOTIF_API || {};
    const CSRF = window.__CSRF_TOKEN || $('meta[name=csrf-token]')?.content;

    const live = document.createElement('div');
    live.id = 'notif-live-announcer';
    live.setAttribute('aria-live', 'polite');
    live.className = 'sr-only';
    document.body.appendChild(live);

    async function safeJson(url, opts = {}) {
        try { const r = await fetch(url, opts); return r.ok ? await r.json() : null; }
        catch { return null; }
    }
    const esc = s => (s ?? '').toString()
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#039;');

    function render(items) {
        if (!Array.isArray(items) || items.length === 0) {
            list.innerHTML = `<div class="p-4 text-sm muted-text">No notifications</div>`;
            unread.textContent = badge.textContent = '0';
            live.textContent = 'No notifications';
            return;
        }
        list.innerHTML = items.map(it => {
            const title = esc(it.title ?? it.message ?? 'Notification');
            const time  = esc(it.human_time ?? '');
            const href  = esc(it.url ?? '#');
            const unreadCls = it.read ? '' : 'notif-unread';
            return `
<a href="${href}" role="listitem" data-notif-id="${esc(it.id)}"
   class="block p-3 border-b hover:bg-gray-50 focus:outline-none ${unreadCls}"
   tabindex="0" style="color:var(--text);text-decoration:none;">
  <div class="flex items-start justify-between">
    <div class="min-w-0 pr-3">
      <div class="text-sm truncate notif-title">${title}</div>
      <div class="text-xs muted-text mt-1">${time}</div>
    </div>
    <div class="ml-2 flex-shrink-0" aria-hidden="true"></div>
  </div>
</a>`;
        }).join('');

        const uc = items.filter(i => !i.read).length;
        unread.textContent = uc;
        badge.textContent  = uc > 99 ? '99+' : uc;
        live.textContent   = `${uc} unread notifications`;
    }

    async function load() {
        if (!API.latest) return;
        const data = await safeJson(API.latest, {credentials:'same-origin'});
        render(Array.isArray(data) ? data : []);
    }

    async function markRead(id) {
        if (!API.markRead) return;
        await fetch(API.markRead, {
            method:'POST', credentials:'same-origin',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body:JSON.stringify({id})
        });
    }

    async function clearAll() {
        if (!API.clear) return;
        list.innerHTML = `<div class="p-4 text-sm muted-text">Clearing…</div>`;
        const res = await fetch(API.clear, {
            method:'POST', credentials:'same-origin',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body:JSON.stringify({})
        });
        if (res.ok) { render([]); live.textContent='Notifications cleared'; }
        else { list.innerHTML = `<div class="p-4 text-sm muted-text">Failed</div>`; }
    }

    // optimistic click / Enter handling
    list.addEventListener('click', async e => {
        const a = e.target.closest('a[role="listitem"]');
        if (!a) return;
        const id = a.dataset.notifId;
        const href = a.getAttribute('href');

        // open in new tab → just fire background mark-read
        if (e.ctrlKey || e.metaKey || e.button === 1) {
            id && markRead(id);
            return;
        }

        e.preventDefault();
        if (id) await markRead(id);
        window.location.href = href;
    });
    list.addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.matches('a[role="listitem"]')) {
            e.preventDefault();
            e.target.click();
        }
    });

    refresh?.addEventListener('click', load);
    markAll?.addEventListener('click', () => {
        $$('#notification-list .notif-unread').forEach(el => el.classList.remove('notif-unread'));
        unread.textContent = badge.textContent = '0';
        live.textContent = 'All notifications marked read';
    });
    clear?.addEventListener('click', clearAll);

    // load when dropdown opens
    bell.addEventListener('click', () => setTimeout(() => {
        if (drop.getAttribute('aria-hidden') === 'false') load();
    }, 40));

    load(); // initial background load
})();
</script>