{{-- Topbar (improved UX + accessibility + keyboard + a11y) --}}
<div id="topbar" class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between gap-6 sticky top-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 shadow-lg transition-all duration-300" role="banner">

  <!-- Left: Logo & Branding -->
  <div class="flex items-center gap-4 flex-shrink-0">
    <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 p-2 rounded-2xl hover:bg-gray-100/80 dark:hover:bg-gray-800/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 transition-all duration-300"
       aria-label="ANC Clinic Dashboard - Home">
      <div class="relative overflow-hidden rounded-2xl">
        <img src="{{ asset('images/anc-logo.svg') }}" alt="ANC Clinic Logo" class="h-12 w-12 transition-transform group-hover:scale-110 group-focus:scale-110">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-emerald-500/20 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        <span class="sr-only">Status: online</span>
        <div class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-900 shadow-md animate-pulse" aria-hidden="true"></div>
      </div>

      <div class="hidden lg:block">
        <div class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">{{ config('app.name', 'ANC Clinic') }}</div>
        <div class="text-xs font-medium text-blue-600 dark:text-blue-400 tracking-wide uppercase">Antenatal Care Excellence</div>
      </div>
    </a>

    <!-- Mobile Menu Button -->
    <button id="mobile-menu-button" type="button"
            class="lg:hidden relative p-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 transition-all duration-300 group"
            aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="mobile-sidebar" data-toggle="mobile">
      <svg class="h-6 w-6 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
        <path id="tb-line1" class="hamburger-line origin-center transition-all duration-300" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16"></path>
        <path id="tb-line2" class="hamburger-line origin-center transition-all duration-300" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12h16"></path>
        <path id="tb-line3" class="hamburger-line origin-center transition-all duration-300" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 18h16"></path>
      </svg>
      <div class="absolute inset-0 rounded-2xl bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" aria-hidden="true"></div>
    </button>
  </div>

  <!-- Right: Actions & User Profile -->
  <div class="flex items-center gap-4 flex-shrink-0">

    @auth
    <!-- Notifications -->
    <div class="relative">
      <button id="notification-bell" type="button"
              class="relative p-3 rounded-2xl hover:bg-gray-100/80 dark:hover:bg-gray-800/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 transition-all duration-300 group"
              aria-label="View notifications" aria-expanded="false" aria-controls="notification-dropdown" data-toggle="dropdown" data-dropdown-id="notification-dropdown">
        <svg class="h-6 w-6 text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7"/>
        </svg>

        <span id="notification-count" class="absolute -top-1 -right-1 bg-gradient-to-br from-red-500 to-rose-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg ring-2 ring-white dark:ring-gray-900 hidden transform scale-95 transition-all duration-200"
              aria-hidden="true" aria-live="polite" aria-atomic="true">0</span>
      </button>

      <div id="notification-dropdown" class="hidden absolute right-0 mt-3 w-96 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl rounded-lg border border-gray-200/50 dark:border-gray-700/50 z-50 overflow-hidden transition-all duration-150 transform scale-95 opacity-0"
           role="menu" aria-hidden="true" aria-labelledby="notification-bell" tabindex="-1" data-dropdown>
        <div class="p-5 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-gray-50/80 to-white/80 dark:from-gray-800/80 dark:to-gray-900/80 backdrop-blur-sm flex items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Notifications</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">You have <span id="unread-count" class="font-semibold text-blue-600 dark:text-blue-400">0</span> unread alerts</p>
          </div>

          <div class="flex items-center gap-2">
            <button id="notification-refresh" type="button" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40" aria-label="Refresh notifications">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6"/></svg>
            </button>

            <button id="dismiss-all-notifications" type="button" class="flex items-center gap-1 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 text-xs font-medium rounded-full hover:bg-red-200 dark:hover:bg-red-800/50 transition-all"
                    aria-label="Dismiss all notifications">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              Clear
            </button>
          </div>
        </div>

        <div id="notification-list" class="max-h-96 overflow-y-auto" tabindex="0" aria-label="Notification list"></div>

        <div class="p-5 border-t border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-white/80 to-gray-50/80 dark:from-gray-900/80 dark:to-gray-800/80 backdrop-blur-sm">
          <a href="{{ route('notifications.index') }}" class="block w-full text-center py-2 px-4 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus-visible:ring-2 focus-visible:ring-blue-500/50 transition-all shadow-lg transform hover:-translate-y-0.5" role="menuitem" tabindex="0">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            View All Notifications
          </a>
        </div>
      </div>
    </div>
    @endauth

    <!-- Theme Toggle -->
    <button id="theme-toggle-main" type="button"
            class="relative p-3 rounded-2xl hover:bg-gray-100/80 dark:hover:bg-gray-800/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 transition-all duration-300 group"
            aria-label="Toggle theme mode" aria-pressed="false" title="Cycle themes: Light, Dark, System" data-toggle="theme">
      <svg id="theme-icon" class="w-6 h-6 text-gray-700 dark:text-gray-300 transition-all duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
        <path id="sun-path" class="opacity-100" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        <path id="moon-path" class="opacity-0" fill="currentColor" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
      </svg>
      <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/20 to-emerald-500/20 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" aria-hidden="true"></div>
    </button>

    <!-- User Menu -->
@auth
<div class="relative">
  <button id="user-menu-button" type="button" 
          class="flex items-center gap-3 p-3 rounded-2xl hover:bg-gray-100/80 dark:hover:bg-gray-800/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 transition-all duration-300 group"
          aria-label="User menu" aria-controls="user-menu" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" data-dropdown-id="user-menu">
    <div class="relative">
      <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" 
           alt="{{ auth()->user()->name ?? 'User' }} Avatar" 
           class="w-10 h-10 rounded-2xl object-cover ring-2 ring-gray-200 dark:ring-gray-700 shadow-md transition-transform group-hover:scale-105">
      <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-900" aria-hidden="true"></div>
    </div>

    <div class="hidden lg:block min-w-0 flex-1">
      <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name ?? 'User' }}</div>
      <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->role ?? 'Nurse' }}</div>
    </div>

    <svg class="h-4 w-4 text-gray-500 dark:text-gray-400 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg>
  </button>

  <div id="user-menu" 
       class="hidden absolute right-0 mt-2 w-64 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 z-50 overflow-hidden transition-all duration-200 origin-top-right transform scale-95 opacity-0"
       role="menu" aria-hidden="true" aria-labelledby="user-menu-button" tabindex="-1" data-dropdown>
    <div class="p-4 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-gray-50/80 to-white/80 dark:from-gray-800/80 dark:to-gray-900/80 backdrop-blur-sm">
      <div class="flex items-center gap-3">
        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" 
             alt="" 
             class="w-12 h-12 rounded-xl object-cover ring-2 ring-gray-200 dark:ring-gray-700">
        <div class="min-w-0 flex-1">
          <h4 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ auth()->user()->name ?? 'User' }}</h4>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email ?? 'user@example.com' }}</p>
          <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Active</p>
        </div>
      </div>
    </div>

    <div class="py-1 divide-y divide-gray-200/50 dark:divide-gray-700/50">
      <a href="{{ route('profile.edit') }}" 
         class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50"
         role="menuitem" tabindex="0" aria-label="Edit Profile">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Profile
      </a>

      <a href="{{ route('settings.index') }}" 
         class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50"
         role="menuitem" tabindex="0" aria-label="Open Settings">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543.826 3.31 2.37 2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Settings
      </a>

      <form action="{{ route('logout') }}" method="POST" class="relative">
        @csrf
        <button type="submit" 
                class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50"
                role="menuitem" tabindex="0" aria-label="Sign Out">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Sign Out
        </button>
      </form>
    </div>
  </div>
</div>
@endauth

  </div>
</div>

@push('styles')
<style>
  /* Hamburger animation */
  .hamburger-open #tb-line1 { transform: translateY(6px) rotate(45deg); }
  .hamburger-open #tb-line2 { opacity: 0; transform: scaleX(0); }
  .hamburger-open #tb-line3 { transform: translateY(-6px) rotate(-45deg); }

  /* Dropdown transitions */
  #notification-dropdown, #user-menu {
    transition: transform 200ms ease-in-out, opacity 200ms ease-in-out;
  }
  #notification-dropdown.hidden, #user-menu.hidden {
    transform: scale(.95);
    opacity: 0;
  }
  #notification-dropdown:not(.hidden), #user-menu:not(.hidden) {
    transform: scale(1);
    opacity: 1;
  }

  /* Sidebar (off-canvas) default + opened state via body class */
  #mobile-sidebar {
    transform: translateX(-100%);
    transition: transform 260ms cubic-bezier(.2,.9,.2,1);
    will-change: transform;
  }
  body.mobile-sidebar-open #mobile-sidebar {
    transform: translateX(0);
  }
  /* On medium+ screens keep sidebar visible */
  @media (min-width: 768px) {
    #mobile-sidebar { transform: none !important; position: static !important; width: auto; box-shadow: none; }
    body.mobile-sidebar-open #mobile-sidebar { transform: none !important; }
    /* hide overlay in md+ if any */
    #mobile-sidebar-overlay { display: none !important; }
  }

  /* Overlay that dims page when sidebar open */
  #mobile-sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5); /* tailwind slate-900/50-ish */
    z-index: 40;
    opacity: 0;
    pointer-events: none;
    transition: opacity 200ms ease-in-out;
  }
  body.mobile-sidebar-open #mobile-sidebar-overlay {
    opacity: 1;
    pointer-events: auto;
  }
  body.mobile-sidebar-open { 
    overflow: hidden;
  }

  /* Reduce motion */
  @media (prefers-reduced-motion: reduce) {
    * { transition-duration: 0ms !important; animation-duration: 0ms !important; }
  }
</style>
@endpush


<script>
(function () {
  const dropdownSelector = '[data-dropdown]';
  const dropdownBtnSelector = '[data-toggle="dropdown"]';
  const themeBtnSelector = '[data-toggle="theme"]';

  const dropdowns = Array.from(document.querySelectorAll(dropdownSelector));
  const dropdownButtons = Array.from(document.querySelectorAll(dropdownBtnSelector));
  const themeToggle = document.querySelector(themeBtnSelector);
  const trapMap = new Map(); // map container -> handler (so we can remove)

  /* ---------- Focus trap helpers ---------- */
  function focusableElements(container) {
    const selector = 'a[href], area[href], input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';
    return Array.from(container.querySelectorAll(selector))
      .filter(el => !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length));
  }

  function trapFocus(container, returnFocusTo) {
    if (!container) return;
    releaseFocus(container); // ensure single handler
    const elements = focusableElements(container);
    const first = elements[0] || container;
    const last = elements[elements.length - 1] || container;

    // remember previously focused element so we can return to it
    const prev = document.activeElement;

    const handler = function (e) {
      if (e.key === 'Tab') {
        if (elements.length === 0) { e.preventDefault(); return; }
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      } else if (e.key === 'Escape') {
        // close the menu that contains this container
        if (container.dataset.trapParentId) {
          const menu = document.getElementById(container.dataset.trapParentId);
          closeDropdown(menu, returnFocusTo || prev);
        } else {
          // fallback: just blur and return focus
          (returnFocusTo || prev || document.body).focus({preventScroll: true});
        }
      }
    };

    container.addEventListener('keydown', handler);
    trapMap.set(container, { handler, returnFocusTo: returnFocusTo || prev });
    // ensure container is focusable if it has no focusable children
    if (elements.length === 0) container.setAttribute('tabindex', '-1');

    // focus the first meaningful item
    (elements[0] || container).focus({preventScroll: true});
  }

  function releaseFocus(container) {
    const entry = trapMap.get(container);
    if (!entry) return;
    container.removeEventListener('keydown', entry.handler);
    trapMap.delete(container);
    if (container.hasAttribute('tabindex')) container.removeAttribute('tabindex');
  }

  /* ---------- Dropdown open/close ---------- */
  function getMenuById(id) { return id ? document.getElementById(id) : null; }

  function openDropdown(menu, triggerBtn) {
    if (!menu || !triggerBtn) return;
    // close others
    dropdowns.forEach(d => { if (d !== menu) closeDropdown(d); });

    menu.classList.remove('hidden');
    menu.style.opacity = '1';
    menu.style.transform = 'scale(1)';
    menu.setAttribute('aria-hidden', 'false');
    triggerBtn.setAttribute('aria-expanded', 'true');

    // set a marker so focus-trap knows which menu produced Escape closing
    menu.dataset.trapParentId = menu.id || '';
    // trap focus, returning to triggerBtn on close
    trapFocus(menu, triggerBtn);
  }

  function closeDropdown(menu, returnFocusTo = null) {
    if (!menu || menu.classList.contains('hidden')) return;
    menu.classList.add('hidden');
    menu.style.opacity = '';
    menu.style.transform = '';
    menu.setAttribute('aria-hidden', 'true');
    const id = menu.id;
    const btn = document.querySelector(`${dropdownBtnSelector}[data-dropdown-id="${id}"]`);
    if (btn) btn.setAttribute('aria-expanded', 'false');
    // release focus trap and return focus
    releaseFocus(menu);
    const toFocus = returnFocusTo || btn;
    if (toFocus && typeof toFocus.focus === 'function') toFocus.focus({preventScroll: true});
  }

  function closeAllDropdowns(returnFocusTo = null) {
    dropdowns.forEach(d => closeDropdown(d, returnFocusTo));
  }

  /* ---------- Init/restore states ---------- */
  (function restoreTheme() {
    try {
      const saved = localStorage.getItem('theme');
      if (saved === 'dark') document.documentElement.classList.add('dark');
      if (saved === 'light') document.documentElement.classList.remove('dark');
      if (themeToggle) themeToggle.setAttribute('aria-pressed', document.documentElement.classList.contains('dark') ? 'true' : 'false');
    } catch (e) {}
  })();

  // Ensure dropdown buttons have a sane default
  dropdownButtons.forEach(b => {
    if (!b.hasAttribute('aria-expanded')) b.setAttribute('aria-expanded', 'false');
  });

  /* ---------- Central event handlers ---------- */
  document.addEventListener('click', function (e) {
    // dropdown toggle
    const btn = e.target.closest(dropdownBtnSelector);
    if (btn) {
      e.preventDefault();
      const id = btn.getAttribute('data-dropdown-id');
      const menu = getMenuById(id);
      const open = btn.getAttribute('aria-expanded') === 'true';
      if (open) closeDropdown(menu, btn);
      else openDropdown(menu, btn);
      return;
    }

    // theme toggle
    const th = e.target.closest(themeBtnSelector);
    if (th) {
      const isDark = document.documentElement.classList.toggle('dark');
      th.setAttribute('aria-pressed', isDark ? 'true' : 'false');
      try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (err) {}
      return;
    }

    // click inside dropdowns — ignore (allow natural interactions)
    if (e.target.closest(dropdownSelector)) return;

    // click outside everything: close dropdowns
    closeAllDropdowns();
  });

  // touch: close when touching outside
  document.addEventListener('touchstart', function (e) {
    if (!e.target.closest(dropdownSelector) && !e.target.closest(dropdownBtnSelector)) {
      closeAllDropdowns();
    }
  }, { passive: true });

  // keyboard: Escape closes an open menu
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      const openMenu = dropdowns.find(d => !d.classList.contains('hidden'));
      if (openMenu) {
        // find its trigger and close
        const id = openMenu.id;
        const btn = document.querySelector(`${dropdownBtnSelector}[data-dropdown-id="${id}"]`);
        closeDropdown(openMenu, btn || null);
        return;
      }
    }
  });

  // helper exposed for your API calls
  window.updateNotificationCount = function (count) {
    const el = document.getElementById('notification-count');
    const unread = document.getElementById('unread-count');
    if (!el || !unread) return;
    const n = Number(count || 0);
    if (n <= 0) {
      el.classList.add('hidden');
      el.textContent = '0';
    } else {
      el.classList.remove('hidden');
      el.textContent = String(n);
    }
    unread.textContent = String(n);
  };

  // On blur (losing focus), close menus for safety
  window.addEventListener('blur', () => closeAllDropdowns());
})();
</script>

