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

<div class="space-y-1 p-2" style="border:1px solid var(--border); border-radius: 16px;">

  <nav id="mobile-sidebar" class="space-y-1" aria-label="Main navigation">
    @foreach($menuItems as $item)
      @php $isActive = request()->routeIs($item['active']); @endphp

      <a href="{{ $item['route'] }}"
        class="group flex items-center px-3 py-3 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
        style="
          color: {{ $isActive ? 'var(--brand)' : 'var(--text)' }};
          background: {{ $isActive ? 'color-mix(in srgb, var(--brand) 8%, transparent)' : 'transparent' }};
          border: 1px solid transparent;
        "
        data-tooltip="{{ $item['tooltip'] ?? $item['label'] }}"
        aria-label="{{ $item['label'] }}"
        {{ $isActive ? 'aria-current=page' : '' }}
        tabindex="0">
        <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3"
             style="{{ $isActive ? 'color:var(--brand)' : 'color:var(--muted)'}}">
          <svg class="{{ $item['iconClass'] ?? '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false" role="img">
            <title>{{ $item['label'] }} icon</title>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] ?? '' }}" />
          </svg>
        </div>

        <span class="text-sm font-medium" style="color:inherit;">{{ ucfirst($item['label']) }}</span>

        @if($isActive)
          <div class="ml-auto flex items-center gap-2">
            <div class="w-2 h-2 rounded-full animate-pulse" aria-hidden="true" style="background:var(--brand)"></div>
            <span class="sr-only">Current page</span>
          </div>
        @endif
      </a>
    @endforeach
  </nav>

  <div id="mobile-sidebar-overlay" aria-hidden="true" style="position:fixed; inset:0; background:rgba(0,0,0,0.35); display:none;"></div>

  <div class="px-2 my-2">
    <div style="border-top:1px solid var(--border);"></div>
  </div>

  <div class="space-y-1" style="background:var(--surface); color:var(--text); border-radius: 12px;">
    <a href="{{ route('settings.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-black/5 dark:hover:bg-white/5"
       style="color:var(--text);">
      <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3" style="color:var(--muted);">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" role="img">
          <title>Settings icon</title>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543.826 3.31 2.37 2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </div>
      <span class="text-sm font-medium">Settings</span>
    </a>
  </div>

</div>

<div class="card p-4 mt-4" style="background:var(--surface); color:var(--text); border:1px solid var(--border); box-shadow:var(--shadow); border-radius: 16px;">
    <h3 class="text-lg font-semibold mb-2">Login Status</h3>
    @auth
      <div class="text-sm space-y-2" style="color:var(--text);">
        <p class="truncate" title="{{ auth()->user()->name }}">
            <strong>User:</strong> {{ auth()->user()->name }}
        </p>
        
        <p class="flex items-center gap-2">
            <strong>Role:</strong> 
            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold uppercase tracking-wide"
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
        <button type="submit" class="btn-danger w-full flex items-center justify-center gap-2 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all hover:opacity-90">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            Logout
        </button>
      </form>
    @else
      <div class="text-center space-y-3">
          <p class="text-sm text-muted">You are currently browsing as a guest.</p>
          <a href="{{ route('login') }}" class="w-full block btn-primary text-center py-2 rounded-xl shadow-lg shadow-brand/20">
              Log In
          </a>
      </div>
    @endauth
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('mobile-sidebar');
  if (!nav) return;

  // Tooltip element (reused)
  const tooltip = document.createElement('div');
  tooltip.className = 'sidebar-tooltip';
  tooltip.setAttribute('role', 'tooltip');

  // basic styling using CSS variables so it follows theme tokens
  Object.assign(tooltip.style, {
    position: 'fixed',
    padding: '6px 8px',
    background: 'var(--text)',
    color: 'var(--bg)',
    fontSize: '12px',
    borderRadius: '8px', // Updated tooltip radius
    opacity: '0',
    pointerEvents: 'none',
    zIndex: '9999',
    transform: 'translate(-50%, -6px)',
    transition: 'opacity 150ms ease, transform 150ms ease'
  });

  document.body.appendChild(tooltip);

  let hideTimeout = null;

  function showTooltip(el) {
    const text = el.dataset.tooltip || el.getAttribute('aria-label') || '';
    if (!text) return;
    tooltip.textContent = text;

    const rect = el.getBoundingClientRect();
    const x = rect.left + rect.width / 2;
    const y = rect.top;

    tooltip.style.left = Math.min(Math.max(8, x), window.innerWidth - 8) + 'px';
    tooltip.style.top = (Math.max(8, y) - 8) + 'px';
    tooltip.style.opacity = '1';
    tooltip.style.transform = 'translate(-50%, -8px)';

    // attach aria
    const id = 'sidebar-tooltip';
    tooltip.id = id;
    el.setAttribute('aria-describedby', id);

    if (hideTimeout) { clearTimeout(hideTimeout); hideTimeout = null; }
  }

  function hideTooltip(el) {
    tooltip.style.opacity = '0';
    tooltip.style.transform = 'translate(-50%, -4px)';
    if (el) el.removeAttribute('aria-describedby');
    hideTimeout = setTimeout(() => { tooltip.textContent = ''; }, 160);
  }

  // mouse + focus events
  nav.addEventListener('pointerenter', (e) => {
    const a = e.target.closest('a');
    if (!a || !nav.contains(a)) return;
    if (a.dataset.tooltip) showTooltip(a);
  }, true);

  nav.addEventListener('pointerleave', (e) => {
    const a = e.target.closest('a');
    if (!a || !nav.contains(a)) return;
    hideTooltip(a);
  }, true);

  nav.addEventListener('focusin', (e) => {
    const a = e.target.closest('a');
    if (!a || !nav.contains(a)) return;
    if (a.dataset.tooltip) showTooltip(a);
  });

  nav.addEventListener('focusout', (e) => {
    const a = e.target.closest('a');
    if (!a || !nav.contains(a)) return;
    hideTooltip(a);
  });

  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape') {
      tooltip.style.opacity = '0';
    }
  });

  // subtle initial animation for active item
  requestAnimationFrame(() => {
    document.querySelectorAll('#mobile-sidebar a[aria-current="page"]').forEach(el => {
      el.style.transform = 'scale(.98)';
      setTimeout(() => el.style.transform = '', 150);
    });
  });
});

/* -------------------------
   Mobile sidebar toggle
   ------------------------- */
document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const btn = document.querySelector('[data-toggle="mobile"]#mobile-menu-button') || document.querySelector('[data-toggle="mobile"]');
  const sidebar = document.getElementById('mobile-sidebar');
  const overlay = document.getElementById('mobile-sidebar-overlay');

  function openSidebar(triggerBtn) {
    if (!sidebar) return;
    overlay.style.display = 'block';
    overlay.style.opacity = '1';
    sidebar.style.display = 'block';
    sidebar.style.transform = 'translateX(0)';
    document.body.classList.add('overflow-hidden');

    if (triggerBtn) {
      triggerBtn.setAttribute('aria-expanded', 'true');
      triggerBtn.classList.add('hamburger-open');
    }

    const first = sidebar.querySelector('a, button, input, [tabindex]:not([tabindex="-1"])');
    (first || sidebar).focus({preventScroll:true});
  }

  function closeSidebar(returnFocusTo) {
    if (!sidebar) return;
    overlay.style.opacity = '0';
    overlay.style.display = 'none';
    sidebar.style.transform = '';
    sidebar.style.display = 'none';
    document.body.classList.remove('overflow-hidden');

    if (btn) {
      btn.setAttribute('aria-expanded', 'false');
      btn.classList.remove('hamburger-open');
    }
    const target = returnFocusTo || btn;
    if (target && typeof target.focus === 'function') target.focus({preventScroll:true});
  }

  function toggleSidebar(triggerBtn) {
    const isOpen = document.body.classList.contains('mobile-sidebar-open');
    if (isOpen) {
      document.body.classList.remove('mobile-sidebar-open');
      closeSidebar(triggerBtn);
    } else {
      document.body.classList.add('mobile-sidebar-open');
      openSidebar(triggerBtn);
    }
  }

  document.addEventListener('click', (e) => {
    const clicked = e.target.closest('[data-toggle="mobile"]');
    if (clicked) {
      e.preventDefault();
      toggleSidebar(clicked);
    }
  });

  if (overlay) {
    overlay.addEventListener('click', () => closeSidebar(btn));
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.body.classList.contains('mobile-sidebar-open')) {
      closeSidebar(btn);
    }
  });

  const mql = window.matchMedia('(min-width: 768px)');
  if (mql.addEventListener) {
    mql.addEventListener('change', (ev) => {
      if (ev.matches && document.body.classList.contains('mobile-sidebar-open')) {
        closeSidebar(btn);
      }
    });
  } else if (mql.addListener) {
    mql.addListener((ev) => {
      if (ev.matches && document.body.classList.contains('mobile-sidebar-open')) {
        closeSidebar(btn);
      }
    });
  }

  if (btn && !btn.hasAttribute('aria-expanded')) btn.setAttribute('aria-expanded', 'false');
});
</script>
@endpush