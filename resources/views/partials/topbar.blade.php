@php
$items = [
    [
        'route'     => route('dashboard'),
        'active'    => 'dashboard*',
        'label'     => 'Dashboard',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'tooltip'   => 'Overview'
    ],
    [
        'route'     => route('appointments.create'),
        'active'    => 'appointments.*',
        'label'     => 'Appointments',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'tooltip'   => 'Manage appointments'
    ],
    [
        'route'     => route('patients.index'),
        'active'    => 'patients.*',
        'label'     => 'Patients',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z M6 20v-1c0-2.21 3.58-4 6-4s6 1.79 6 4v1',
        'tooltip'   => 'Patient list'
    ],
    [
        'route'     => route('call_logs'),
        'active'    => 'call_logs*',
        'label'     => 'Call Logs',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M3 5a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z M14 7h7v10a2 2 0 01-2 2h-5',
        'tooltip'   => 'Call history'
    ],
    [
        'route'     => route('daily-queue'),
        'active'    => 'daily-queue*',
        'label'     => 'Daily Queue',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        'tooltip'   => 'Attendance history'
    ],
];

$user = auth()->user();
if ($user && (
    (method_exists($user, 'hasRole') && $user->hasRole('admin')) || 
    ($user->role === 'admin')
)) {
    $items[] = [
        'route'     => route('reports.index'),
        'active'    => 'reports.*',
        'label'     => 'Reports',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M3 3h18v18H3V3z M7 12h3v6H7v-6z M11 8h3v10h-3V8z',
        'tooltip'   => 'View reports'
    ];
}

$menuItems = $menuItems ?? $items;
@endphp


<header id="topbar"
        class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between gap-6 sticky top-0 z-40 transition-all duration-300 motion-reduce:transition-none"
        role ="banner"
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
                     alt="ANC Clinic logo"
                     class="h-12 w-12 transition-transform group-hover:scale-110 group-focus:scale-110 motion-reduce:transform-none">
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
                     style="background: linear-gradient(135deg, color-mix(in srgb, var(--brand) 22%, transparent), color-mix(in srgb, var(--success) 18%, transparent));"></div>
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
                <path class="hamburger-line origin-center transition-all duration-300" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16"></path>
                <path class="hamburger-line origin-center transition-all duration-300" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12h16"></path>
                <path class="hamburger-line origin-center transition-all duration-300" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 18h16"></path>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 003 11v3.159c0 .538-.214 1.055-.595 1.436L1 17h5m5 0v1a3 3 0 11-6 0v-1m6 0H5"/>
                    </svg>
                    <span id="notification-count"
                          class="absolute -top-1 -right-1 text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg transform scale-95 transition-all duration-200"
                          style="background: linear-gradient(135deg, var(--danger), color-mix(in srgb,var(--danger) 70%, var(--accent))); color:#fff; border:2px solid var(--bg); display:none;">
                      0
                    </span>
                </button>

                {{-- DROPDOWN PANEL --}}
                <div id="notification-dropdown"
                     class="hidden absolute right-0 mt-3 w-96 rounded-lg z-50 overflow-hidden transition-all duration-200 transform scale-95 opacity-0"
                     role="region" aria-labelledby="notification-bell" tabindex="-1"
                     style="background: color-mix(in srgb, var(--surface) 98%, transparent); border:1px solid var(--border); box-shadow:var(--shadow);">
                    
                    <div class="p-5 border-b backdrop-blur-sm flex items-center justify-between gap-3"
                         style="border-bottom:1px solid var(--border); background: linear-gradient(90deg, color-mix(in srgb,var(--surface) 80%,transparent), color-mix(in srgb,var(--bg) 80%,transparent));">
                        <div>
                            <h3 class="text-base font-bold" style="color:var(--text)">Notifications</h3>
                            <p class="text-sm" style="color:var(--muted); margin-top:4px">
                                You have <span id="unread-count" class="font-semibold" style="color:var(--brand)">0</span> unread alerts
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="notification-refresh" type="button" class="p-2 rounded-md focus:outline-none transition-colors" aria-label="Refresh notifications" style="color:var(--muted);">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 20v-6h-6"/></svg>
                            </button>
                            <button id="mark-all-read" type="button" class="p-2 rounded-md focus:outline-none transition-colors text-sm" style="color:var(--muted);">
                                Mark all read
                            </button>
                            <button id="dismiss-all-notifications" type="button" class="flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium transition-all"
                                    style="background: color-mix(in srgb, var(--danger) 10%, transparent); color: color-mix(in srgb, var(--danger) 85%, black);">
                                <span>Clear</span>
                            </button>
                        </div>
                    </div>

                    <div id="notification-list" class="max-h-96 overflow-y-auto" tabindex="0" role="list" style="color:var(--text)"></div>

                    <div class="p-5 border-t" style="border-top:1px solid var(--border); background: linear-gradient(90deg, color-mix(in srgb,var(--bg) 85%,transparent), color-mix(in srgb,var(--surface) 85%,transparent));">
                        <a href="{{ route('notifications.index') }}" class="block w-full text-center py-2 px-4 rounded-lg font-medium transition-all shadow-lg transform hover:-translate-y-0.5" style="background:var(--brand); color:#fff;">
                            View All Notifications
                        </a>
                    </div>
                </div>
            </div>
        @endauth

        {{-- THEME TOGGLE --}}
        <button id="theme-toggle-main" type="button" aria-label="Cycle theme" class="relative p-3 rounded-2xl focus:outline-none transition-all duration-300 group" style="color:var(--muted);">
            <svg id="theme-icon" class="w-6 h-6 transition-all duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path class="opacity-100" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>

        @auth
            {{-- USER MENU --}}
            <div class="relative" data-dropdown-container>
                <button id="user-menu-button" data-dropdown type="button" aria-label="User menu" aria-controls="user-menu" class="flex items-center gap-3 p-3 rounded-2xl focus:outline-none transition-all duration-300 group" style="color:var(--text);">
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/'.auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" class="w-10 h-10 rounded-2xl object-cover ring-2 shadow-md transition-transform group-hover:scale-105" style="border-color:var(--border)">
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full border-2" style="background:var(--success); border-color:var(--bg)"></div>
                    </div>
                    <div class="hidden lg:block min-w-0 flex-1">
                        <div class="text-sm font-semibold truncate" style="color:var(--text)">{{ auth()->user()->name ?? 'User' }}</div>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div id="user-menu" class="hidden absolute right-0 mt-2 w-64 rounded-2xl z-50 overflow-hidden transition-all duration-200 origin-top-right transform scale-95 opacity-0" role="menu" style="background: color-mix(in srgb, var(--surface) 98%, transparent); border:1px solid var(--border); box-shadow:var(--shadow);">
                    <div class="p-4 border-b backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <img src="{{ auth()->user()->avatar ? asset('storage/avatars/'.auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" class="w-12 h-12 rounded-xl object-cover" style="border:2px solid var(--border)">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-sm truncate" style="color:var(--text)">{{ auth()->user()->name ?? 'User' }}</h4>
                                <p class="text-xs truncate" style="color:var(--muted)">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="py-1 divide-y" style="border-color:var(--border);">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm transition-colors" style="color:var(--text)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Profile</a>
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm transition-colors" style="color:var(--text)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543.826 3.31 2.37 2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Settings</a>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm transition-colors" style="color:var(--text)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Sign Out</button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</header>

{{-- 
    MOBILE SIDEBAR
--}}
<aside id="mobile-sidebar" class="fixed inset-0 z-[100] lg:hidden hidden" aria-hidden="true" role="dialog" aria-modal="true">
    {{-- Overlay --}}
    <div id="mobile-overlay"
         class="absolute inset-0 transition-opacity duration-300 opacity-0 pointer-events-none"
         style="background:rgba(0,0,0,.4); backdrop-filter:blur(6px);"></div>

    {{-- Sliding Panel --}}
    <nav id="mobile-panel"
         class="absolute left-0 top-0 h-[100dvh] w-80 max-w-[85vw] transform -translate-x-full opacity-0 transition-all duration-300 motion-reduce:transition-none flex flex-col"
         aria-label="Mobile navigation" tabindex="-1"
         style="background:var(--surface); border-right:1px solid var(--border); box-shadow:var(--shadow);">
        
        {{-- Header (Logo + Close) --}}
        <div class="p-4 border-b flex items-center justify-between flex-shrink-0" style="border-bottom:1px solid var(--border);">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/anc-logo.svg') }}" alt="ANC logo" class="w-10 h-10 rounded-md">
                <div>
                    <div class="font-semibold text-sm" style="color:var(--text)">{{ config('app.name', 'ANC Clinic') }}</div>
                    <div class="text-xs" style="color:var(--muted)">Antenatal Care</div>
                </div>
            </div>
            <button id="mobile-close" type="button" class="p-2 rounded-md focus:outline-none transition-all duration-200 hover:bg-black/5" aria-label="Close menu" style="color:var(--muted);">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Scrollable Content Area --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-6" id="mobile-nav-items">
            
            {{-- 1. Dynamic Navigation Links --}}
            <div class="space-y-1">
                @foreach($menuItems as $item)
                    @php $isActive = request()->routeIs($item['active']); @endphp
                    <a href="{{ $item['route'] }}"
                       class="group flex items-center px-3 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
                       style="
                         color: {{ $isActive ? 'var(--brand)' : 'var(--text)' }};
                         background: {{ $isActive ? 'color-mix(in srgb, var(--brand) 8%, transparent)' : 'transparent' }};
                       ">
                        <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3"
                             style="{{ $isActive ? 'color:var(--brand)' : 'color:var(--muted)'}}">
                            <svg class="{{ $item['iconClass'] ?? '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] ?? '' }}" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium" style="color:inherit;">{{ ucfirst($item['label']) }}</span>
                        @if($isActive)
                            <div class="ml-auto flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full animate-pulse" aria-hidden="true" style="background:var(--brand)"></div>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- 2. Divider --}}
            <div style="border-top:1px solid var(--border);"></div>

            {{-- 3. Settings & Help --}}
            <div class="space-y-1">
                <a href="{{ route('settings.index') }}" class="group flex items-center px-3 py-3 rounded-lg transition-all duration-200 hover:bg-black/5" style="color:var(--text);">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3" style="color:var(--muted);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543.826 3.31 2.37 2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-sm font-medium">Settings</span>
                </a>
               
            </div>

            {{-- 4. Login Status Card --}}
            <div class="card p-4" style="background:var(--surface); color:var(--text); border:1px solid var(--border); box-shadow:var(--shadow);">
            <h3 class="text-lg font-semibold mb-2">Login Status</h3>
            @auth
            <div class="text-sm space-y-2" style="color:var(--text);">
                <p class="truncate" title="{{ auth()->user()->name }}">
                    <strong>User:</strong> {{ auth()->user()->name }}
                </p>
                
                <p class="flex items-center gap-2">
                    <strong>Role:</strong> 
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide"
                        style="background: color-mix(in srgb, var(--brand) 10%, transparent); color: var(--brand); border: 1px solid color-mix(in srgb, var(--brand) 20%, transparent);">
                        {{ auth()->user()->getRoleNames()->first() ? str_replace('_', ' ', ucfirst(auth()->user()->getRoleNames()->first())) : (auth()->user()->role ?? 'Staff') }}
                    </span>
                </p>

                <p class="text-xs" style="color:var(--muted)">
                    <strong>Joined:</strong> {{ optional(auth()->user()->created_at) ? auth()->user()->created_at->format('M d, Y') : 'N/A' }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn-danger w-full flex items-center justify-center gap-2 py-2 text-xs font-bold uppercase tracking-widest">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Logout
                </button>
            </form>
            @else
            <div class="text-center space-y-3">
                <p class="text-sm text-muted">You are currently browsing as a guest.</p>
                <a href="{{ route('login') }}" class="w-full block btn-primary text-center py-2 shadow-lg shadow-brand/20">
                    Log In
                </a>
            </div>
            @endauth
        </div>

        </div>
    </nav>
</aside>

<script>
    window.__ALERT_API = {
        list: "{{ route('alerts.index') }}",
        read: (id) => `/alerts/${id}/read`,
        dismissAll: "{{ route('alerts.dismiss-all') }}"
    };
</script>

<script>
/* ----------------------------------------------------------------------
   1. GLOBAL HELPERS
----------------------------------------------------------------------- */
const $  = (s, el = document) => el.querySelector(s);
const $$ = (s, el = document) => Array.from(el.querySelectorAll(s));
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const enterCls = ['opacity-100', 'scale-100', 'translate-x-0'];
const leaveCls = ['opacity-0', 'scale-95', '-translate-x-full'];

function addCls(el, cls) { cls.forEach(c => el.classList.add(c)); }
function rmCls(el, cls)  { cls.forEach(c => el.classList.remove(c)); }

function show(el, btn) {
    btn?.setAttribute('aria-expanded', 'true');
    el?.setAttribute('aria-hidden', 'false');
    el?.classList.remove('hidden');
    requestAnimationFrame(() => {
        rmCls(el, leaveCls); addCls(el, enterCls);
    });
}
function hide(el, btn) {
    btn?.setAttribute('aria-expanded', 'false');
    el?.setAttribute('aria-hidden', 'true');
    addCls(el, leaveCls); rmCls(el, enterCls);
    if (prefersReduced) el.classList.add('hidden');
    else setTimeout(() => el.classList.add('hidden'), 300);
}

/* ----------------------------------------------------------------------
   2. FOCUS TRAP
----------------------------------------------------------------------- */
function trapFocus(container) {
    if (!container) return null;
    const focusables = () => Array.from(container.querySelectorAll(
        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"]), input, textarea, select'
    )).filter(el => {
        return el.offsetWidth || el.offsetHeight || el.getClientRects().length;
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
   3. DROPDOWN WIRING
----------------------------------------------------------------------- */
$$('[data-dropdown]').forEach(btn => {
    const panelId = btn.getAttribute('aria-controls');
    const panel   = panelId ? $(`#${panelId}`) : btn.closest('[data-dropdown-container]')?.querySelector('.absolute');
    if (!panel) return;
    if (!panel.hasAttribute('tabindex')) panel.setAttribute('tabindex', '-1');

    const toggle = (e) => {
        e.stopPropagation();
        const open = btn.getAttribute('aria-expanded') === 'true';
        if (open) { hide(panel, btn); if (panel._untrap) { panel._untrap(); panel._untrap = null; } }
        else {
            $$('[data-dropdown][aria-expanded="true"]').forEach(other => other.click());
            show(panel, btn);
            panel._untrap = trapFocus(panel);
            const first = panel.querySelector('a, button');
            (first || panel).focus();
        }
    };
    btn.addEventListener('click', toggle);
    panel.addEventListener('trap-escape', () => { hide(panel, btn); if (panel._untrap) { panel._untrap(); panel._untrap = null; } btn.focus(); });
    document.addEventListener('click', e => {
        if (btn.getAttribute('aria-expanded') !== 'true') return;
        if (panel.contains(e.target) || btn.contains(e.target)) return;
        hide(panel, btn);
        if (panel._untrap) { panel._untrap(); panel._untrap = null; }
    });
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
        mobPanel.classList.remove('-translate-x-full', 'opacity-0');
        mobPanel.classList.add('translate-x-0', 'opacity-100');
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
    mobPanel.classList.add('-translate-x-full', 'opacity-0');
    mobPanel.classList.remove('translate-x-0', 'opacity-100');
    document.body.classList.remove('overflow-hidden');
    if (mobUntrap) { mobUntrap(); mobUntrap = null; }
    setTimeout(() => mobSidebar.classList.add('hidden'), 300);
    mobBtn?.focus();
}

mobBtn?.addEventListener('click', () => (mobBtn.getAttribute('aria-expanded') === 'true' ? closeMobile() : openMobile()));
mobClose?.addEventListener('click', closeMobile);
mobOverlay?.addEventListener('click', closeMobile);

/* ----------------------------------------------------------------------
   5. NOTIFICATIONS
----------------------------------------------------------------------- */
(() => {
    const bell = $('#notification-bell');
    const drop = $('#notification-dropdown');
    const list = $('#notification-list');
    const unread = $('#unread-count');
    const badge  = $('#notification-count');
    const refresh= $('#notification-refresh');
    const markAll= $('#mark-all-read');
    const clear  = $('#dismiss-all-notifications');

    if (!bell || !drop || !list) return;

    const API = window.__ALERT_API;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    const esc = s => (s ?? '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

    async function safeJson(url, opts = {}) {
        try {
            const r = await fetch(url, opts);
            return r.ok ? await r.json() : null;
        } catch (e) { return null; }
    }

    function render(json) {
        const {data = [], count = 0} = json || {};
        if (!Array.isArray(data) || data.length === 0) {
            list.innerHTML = `<div class="p-4 text-sm" style="color:var(--muted)">No notifications</div>`;
            unread.textContent = '0';
            badge.style.display = 'none';
            return;
        }
        list.innerHTML = data.map(it => {
            const title = esc(it.message ?? 'Notification');
            const time  = esc(it.human_time ?? new Date(it.created_at).toLocaleString());
            const href  = esc(it.url ?? '#');
            return `
              <a href="${href}" role="listitem" data-alert-id="${esc(it.id)}"
                class="block p-3 border-b hover:bg-gray-10 focus:outline-none ${it.is_read ? '' : 'notif-unread'}"
                style="color:var(--text);text-decoration:none;">
                <div class="flex items-start justify-between">
                  <div class="min-w-0 pr-3">
                    <div class="text-sm truncate notif-title">${title}</div>
                    <div class="text-xs" style="color:var(--muted)" mt-1">${time}</div>
                  </div>
                </div>
               </a>`;
        }).join('');
        unread.textContent = count;
        badge.textContent  = count > 99 ? '99+' : count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }

    async function load() {
        const json = await safeJson(`${API.list}?limit=50&critical_only=0`, {credentials: 'same-origin'});
        render(json || {data: [], count: 0});
    }

    async function markRead(id) {
        await fetch(API.read(id), { method: 'POST', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'}, body: JSON.stringify({}) });
    }

    async function dismissAll() {
        list.innerHTML = `<div class="p-4 text-sm">Clearing...</div>`;
        const res = await fetch(API.dismissAll, { method: 'POST', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'}, body: JSON.stringify({}) });
        if(res.ok) render({data:[], count:0});
    }

    list.addEventListener('click', async e => {
        const a = e.target.closest('a[role="listitem"]');
        if (!a) return;
        const id = a.dataset.alertId;
        const href = a.getAttribute('href');
        if (e.ctrlKey || e.metaKey || e.button === 1) { if(id) await markRead(id); return; }
        e.preventDefault();
        if(id) { await markRead(id); a.classList.remove('notif-unread'); }
        window.location.href = href;
    });

    refresh?.addEventListener('click', () => load());
    markAll?.addEventListener('click', () => {
        $$('#notification-list .notif-unread').forEach(el => el.classList.remove('notif-unread'));
        unread.textContent = '0'; badge.style.display = 'none';
    });
    clear?.addEventListener('click', dismissAll);
    bell.addEventListener('click', () => setTimeout(() => { if (drop.getAttribute('aria-hidden') === 'false') load(); }, 40));
    load();
})();

/* ----------------------------------------------------------------------
   6. THEME TOGGLE (Fixed Logic)
----------------------------------------------------------------------- */
(() => {
    const btn = document.getElementById('theme-toggle-main');
    const icon = btn?.querySelector('#theme-icon path');
    const html = document.documentElement;

    const sunPath  = "M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z";
    const moonPath = "M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z";

    if (!btn || !icon) return;

    function setTheme(dark) {
        if (dark) {
            html.classList.add('dark');
            icon.setAttribute('d', sunPath);
            localStorage.setItem('theme', 'dark');
        } else {
            html.classList.remove('dark');
            icon.setAttribute('d', moonPath);
            localStorage.setItem('theme', 'light');
        }
    }

    // Init based on storage or system preference
    const saved = localStorage.getItem('theme');
    const system = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = saved === 'dark' || (!saved && system);
    setTheme(isDark);

    // Toggle on click
    btn.addEventListener('click', () => {
        // Toggle based on current DOM class existence
        setTheme(!html.classList.contains('dark'));
    });
})();
</script>