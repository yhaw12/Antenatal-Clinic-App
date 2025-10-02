@php
$menuItems = $menuItems ?? [
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
        'route'     => route('call-logs'),
        'active'    => 'call-logs*',
        'label'     => 'Call Logs',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M3 5a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z M14 7h7v10a2 2 0 01-2 2h-5',
        'tooltip'   => 'Call history'
    ],

    [
    'route'     => route('daily-queue'),
    'active'    => 'daily-queue*',
    'label'     => 'daily-queue',
    'iconClass' => 'w-5 h-5',
    'icon'      => 'M3 5a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z M14 7h7v10a2 2 0 01-2 2h-5',
    'tooltip'   => 'Attendance history'
    ],

    [
        'route'     => route('reports.index'),
        'active'    => 'reports.*',
        'label'     => 'Reports',
        'iconClass' => 'w-5 h-5',
        'icon'      => 'M3 3h18v18H3V3z M7 12h3v6H7v-6z M11 8h3v10h-3V8z',
        'tooltip'   => 'View reports'
    ],
];
@endphp


<div class="space-y-1">

  <!-- Navigation Links -->
  <nav id="mobile-sidebar" class="space-y-1 px-2" aria-label="Main navigation">
    @foreach($menuItems as $item)
      @php $isActive = request()->routeIs($item['active']); @endphp

      <a href="{{ $item['route'] }}"
         class="group flex items-center px-3 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400
                {{ $isActive ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400' }}"
         data-tooltip="{{ $item['tooltip'] ?? $item['label'] }}"
         aria-label="{{ $item['label'] }}"
         {{ $isActive ? 'aria-current=page' : '' }}
         tabindex="0">

        <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors' }}">
          <svg class="{{ $item['iconClass'] ?? '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false" role="img">
            <title>{{ $item['label'] }} icon</title>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] ?? 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' }}" />
          </svg>
        </div>

        <span class="text-sm font-medium">{{ $item['label'] }}</span>

        @if($isActive)
          <div class="ml-auto flex items-center gap-2">
            <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse" aria-hidden="true"></div>
            <span class="sr-only">Current page</span>
          </div>
        @endif
      </a>
    @endforeach
  </nav>

  <!-- Divider -->
  <div class="px-2 my-4">
    <div class="border-t border-gray-200 dark:border-gray-700"></div>
  </div>

  <!-- Settings & Help -->
  <div class="space-y-1 px-2">
    <a href="{{ route('settings.index') }}" class="group flex items-center px-3 py-3 rounded-lg transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400" aria-label="Settings">
      <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3 text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" role="img">
          <title>Settings icon</title>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </div>
      <span class="text-sm font-medium">Settings</span>
    </a>

    <a href="#" class="group flex items-center px-3 py-3 rounded-lg transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400" aria-label="Help">
      <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center mr-3 text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" role="img">
          <title>Help icon</title>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <span class="text-sm font-medium">Help</span>
    </a>
  </div>

  <div class="card p-4">
    <h3 class="text-lg font-semibold mb-2">Login Status</h3>
    @auth
      <div class="text-sm space-y-1 text-body">
        <p><strong>User:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Role:</strong> {{ auth()->user()->role ?? 'Nurse' }}</p>
        <p><strong>Since:</strong> {{ optional(auth()->user()->created_at) ? auth()->user()->created_at->diffForHumans() : now()->format('H:i') }}</p>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full btn-danger">Logout</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="w-full block btn-primary text-center">Login</a>
    @endauth
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('mobile-sidebar');
  if (!nav) return;

  // Create one tooltip element we reuse for performance and accessibility.
  const tooltip = document.createElement('div');
  tooltip.className = 'absolute px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 pointer-events-none transition-opacity z-50 whitespace-nowrap';
  tooltip.setAttribute('role', 'tooltip');
  tooltip.style.top = '0';
  tooltip.style.left = '0';
  tooltip.style.transform = 'translate(-50%, -8px)';
  tooltip.style.transition = 'opacity 150ms ease, transform 150ms ease';
  document.body.appendChild(tooltip);

  let tooltipVisible = false;
  let hideTimeout = null;

  function showTooltip(el) {
    const text = el.dataset.tooltip || el.getAttribute('aria-label') || '';
    if (!text) return;
    tooltip.textContent = text;

    const rect = el.getBoundingClientRect();
    const x = rect.left + rect.width / 2;
    const y = rect.top;

    // Position above the element, but keep inside viewport horizontally
    tooltip.style.left = Math.min(Math.max(8, x), window.innerWidth - 8) + 'px';
    tooltip.style.top = (Math.max(8, y) - 8) + 'px';
    tooltip.style.opacity = '1';
    tooltip.style.transform = 'translate(-50%, -6px)';
    tooltipVisible = true;

    // Associate for screen readers
    const id = 'sidebar-tooltip';
    tooltip.id = id;
    el.setAttribute('aria-describedby', id);

    if (hideTimeout) {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    }
  }

  function hideTooltip(el) {
    tooltip.style.opacity = '0';
    tooltip.style.transform = 'translate(-50%, -4px)';
    tooltipVisible = false;
    if (el) el.removeAttribute('aria-describedby');

    // remove from DOM after animation to prevent focus issues
    hideTimeout = setTimeout(() => {
      tooltip.textContent = '';
      hideTimeout = null;
    }, 160);
  }

  // events: mouseenter/mouseleave and focus/blur, also handle touch
  nav.addEventListener('mouseover', (e) => {
    const a = e.target.closest('a[ data-tooltip], a');
    if (!a || !nav.contains(a)) return;
    if (a.dataset.tooltip) showTooltip(a);
  });

  nav.addEventListener('mouseout', (e) => {
    const a = e.target.closest('a[ data-tooltip], a');
    if (!a || !nav.contains(a)) return;
    hideTooltip(a);
  });

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

  // keyboard: close tooltip on escape
  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape' && tooltipVisible) {
      tooltip.style.opacity = '0';
      tooltipVisible = false;
    }
  });

  // small improvement: keep indicator animation subtle for first load
  requestAnimationFrame(() => {
    document.querySelectorAll('#mobile-sidebar a[aria-current="page"]').forEach(el => {
      el.style.transform = 'scale(.98)';
      setTimeout(() => el.style.transform = '', 150);
    });
  });
});
</script>
@endpush