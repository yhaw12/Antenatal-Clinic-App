@extends('layouts.app')
@section('title','Settings')
@section('page-title','Settings')


@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Settings</h1>

    {{-- Server-side flash or errors --}}
    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 dark:bg-green-900/40 text-green-800 dark:text-green-200 shadow-sm" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200">
            <strong class="block mb-1">Please fix the following:</strong>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Settings Card --}}
    <form id="settings-form" action="{{ route('settings.update') }}" method="POST" autocomplete="off" class="space-y-6 bg-white dark:bg-gray-900 shadow rounded-lg p-6" novalidate>
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between gap-2 mb-2">
            <div class="text-sm text-gray-500 dark:text-gray-400">Manage preferences — collapsible sections below</div>

            <div class="flex items-center gap-2">
                <button id="export-btn" type="button" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <!-- download icon -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M21 21H3"/></svg>
                    Export JSON
                </button>

                <label for="import-file" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                    <!-- upload icon -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M21 21H3"/></svg>
                    Import
                </label>
                <input id="import-file" type="file" accept="application/json" class="hidden" />
            </div>
        </div>

        {{-- Accordion container --}}
        <div id="accordion" class="space-y-3" role="tablist" aria-multiselectable="true">

            {{-- Notification Preferences (accordion) --}}
            <section class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <button type="button" class="w-full flex items-center justify-between p-4 focus:outline-none" aria-expanded="true" data-accordion-toggle>
                    <div class="flex items-center gap-3">
                        <!-- bell icon -->
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1"/></svg>
                        <div class="text-left">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Notification Preferences</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Control how you receive alerts</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">Preview</div>
                        <svg class="accordion-chevron w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </button>

                <div class="accordion-panel p-4 border-t border-gray-100 dark:border-gray-700" role="tabpanel">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email (kept for archive, you requested no active email handling earlier; still present to toggle server preference if present) -->
                        <div class="flex items-center justify-between p-3 rounded border border-gray-100 dark:border-gray-800">
                            <div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Email (critical)</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Critical alerts to your email address</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input id="notif-email" name="notifications[email]" type="checkbox" class="sr-only" {{ data_get($preferences, 'notifications.email', false) ? 'checked' : '' }}>
                                <span class="w-11 h-6 bg-gray-200 dark:bg-gray-700 rounded-full transition-colors duration-200" aria-hidden="true"></span>
                                <span id="notif-email-thumb" class="absolute left-0.5 top-0.5 w-5 h-5 bg-white dark:bg-gray-900 rounded-full shadow transform transition-transform duration-200 {{ data_get($preferences, 'notifications.email', false) ? 'translate-x-5' : '' }}"></span>
                            </label>
                        </div>

                        <!-- In-app -->
                        <div class="flex items-center justify-between p-3 rounded border border-gray-100 dark:border-gray-800">
                            <div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">In-app (bell)</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Show notifications inside the app</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input id="notif-inapp" name="notifications[in_app]" type="checkbox" class="sr-only" {{ data_get($preferences, 'notifications.in_app', true) ? 'checked' : '' }}>
                                <span class="w-11 h-6 bg-gray-200 dark:bg-gray-700 rounded-full transition-colors duration-200" aria-hidden="true"></span>
                                <span id="notif-inapp-thumb" class="absolute left-0.5 top-0.5 w-5 h-5 bg-white dark:bg-gray-900 rounded-full shadow transform transition-transform duration-200 {{ data_get($preferences, 'notifications.in_app', true) ? 'translate-x-5' : '' }}"></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-3">
                        <button id="test-notification" type="button" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Test notification
                        </button>
                        <p class="text-xs text-gray-500 dark:text-gray-400">This triggers a sample in-app notification (client-side).</p>
                    </div>
                </div>
            </section>

            {{-- Theme Preference (accordion) --}}
            <section class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <button type="button" class="w-full flex items-center justify-between p-4 focus:outline-none" aria-expanded="false" data-accordion-toggle>
                    <div class="flex items-center gap-3">
                        <!-- palette icon -->
                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3C7 3 3 7 3 12a7 7 0 0014 0c0-2.21-1.79-4-4-4H9"/></svg>
                        <div class="text-left">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Theme Preference</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Live preview — persists on save</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">Preview</div>
                        <svg class="accordion-chevron w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </button>

                <div class="accordion-panel p-4 border-t border-gray-100 dark:border-gray-700" role="tabpanel">
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="light" class="sr-only" {{ data_get($preferences, 'theme', 'system') === 'light' ? 'checked' : '' }} />
                            <div class="p-3 rounded border border-gray-200 dark:border-gray-700 text-center hover:shadow-sm">
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Light</div>
                                <div class="text-xs text-gray-400">Bright UI</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="dark" class="sr-only" {{ data_get($preferences, 'theme', 'system') === 'dark' ? 'checked' : '' }} />
                            <div class="p-3 rounded border border-gray-200 dark:border-gray-700 text-center hover:shadow-sm">
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Dark</div>
                                <div class="text-xs text-gray-400">Low-light</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="system" class="sr-only" {{ data_get($preferences, 'theme', 'system') === 'system' ? 'checked' : '' }} />
                            <div class="p-3 rounded border border-gray-200 dark:border-gray-700 text-center hover:shadow-sm">
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">System</div>
                                <div class="text-xs text-gray-400">Follow OS</div>
                            </div>
                        </label>
                    </div>

                    {{-- Improved theme preview --}}
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div id="theme-preview" class="p-4 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition-all duration-300 transform hover:scale-[1.01]">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100" id="preview-title">Preview</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">See colors & contrast</div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" id="preview-badge">—</div>
                            </div>

                            <div class="space-y-3">
                                <div class="p-3 rounded shadow-sm" id="preview-card">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-semibold" id="preview-card-title">Poultry Tracker</div>
                                            <div class="text-xs text-gray-500" id="preview-card-sub">Small sample card</div>
                                        </div>
                                        <div class="text-xs font-medium" id="preview-card-cta">₵1200</div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button class="px-3 py-1 text-sm rounded bg-blue-600 text-white">Action</button>
                                    <button class="px-3 py-1 text-sm rounded border">Secondary</button>
                                </div>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <p class="mb-2">Pick a theme and the preview updates instantly — animations make transitions smooth. Theme is persisted locally on change and saved on server when you press Save.</p>
                            <p class="text-xs text-gray-500">Tip: use <strong>System</strong> to follow OS dark mode automatically.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Dashboard Customization (accordion) --}}
            <section class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <button type="button" class="w-full flex items-center justify-between p-4 focus:outline-none" aria-expanded="false" data-accordion-toggle>
                    <div class="flex items-center gap-3">
                        <!-- grid icon -->
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <div class="text-left">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Dashboard Customization</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Choose which sections to show</div>
                        </div>
                    </div>
                    <svg class="accordion-chevron w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </button>

                <div class="accordion-panel p-4 border-t border-gray-100 dark:border-gray-700" role="tabpanel">
                    @php
                        $dashboardSections = [
                            'weather' => 'Weather Widget',
                            'quick_actions' => 'Quick Actions',
                            'kpis' => 'Key Performance Indicators',
                            'pending_approvals' => 'Pending Approvals',
                            'payroll_status' => 'Payroll Status',
                            'charts' => 'Dashboard Charts',
                            'vaccination_overview' => 'Vaccination Overview',
                            'transaction_overview' => 'Transaction Overview',
                            'order_overview' => 'Order Overview',
                            'recent_sales' => 'Recent Sales',
                            'recent_mortalities' => 'Recent Mortalities',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($dashboardSections as $key => $label)
                            <div class="flex items-center justify-between p-3 rounded border border-gray-100 dark:border-gray-800">
                                <div>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Show on dashboard</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input id="dashboard-{{ $key }}" name="dashboard[{{ $key }}]" type="checkbox" class="sr-only" {{ data_get($preferences, "dashboard.$key", true) ? 'checked' : '' }}>
                                    <span class="w-11 h-6 bg-gray-200 dark:bg-gray-700 rounded-full transition-colors duration-200" aria-hidden="true"></span>
                                    <span id="dashboard-{{ $key }}-thumb" class="absolute left-0.5 top-0.5 w-5 h-5 bg-white dark:bg-gray-900 rounded-full shadow transform transition-transform duration-200 {{ data_get($preferences, "dashboard.$key", true) ? 'translate-x-5' : '' }}"></span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

        </div> {{-- accordion end --}}

        {{-- Save/Reset --}}
        <div class="flex items-center justify-between gap-3 mt-4">
            <div class="flex gap-2">
                <button id="save-btn" type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Save Settings
                </button>

                <button id="reset-btn" type="button" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none" aria-haspopup="dialog">
                    Reset to defaults
                </button>
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                Settings saved on the server. Theme preview applied locally.
            </div>
        </div>

        {{-- Hidden inputs for non-JS fallback --}}
        <input type="hidden" name="notifications[in_app]" value="{{ data_get($preferences, 'notifications.in_app', true) ? '1' : '0' }}">
        <input type="hidden" name="notifications[email]" value="{{ data_get($preferences, 'notifications.email', false) ? '1' : '0' }}">
        <input type="hidden" name="theme" value="{{ data_get($preferences, 'theme', 'system') }}">
        @foreach ($dashboardSections as $key => $label)
            <input type="hidden" name="dashboard[{{ $key }}]" value="{{ data_get($preferences, "dashboard.$key", true) ? '1' : '0' }}">
        @endforeach
    </form>
</div>

<!-- Confirm modal (reused for Reset & Import preview confirmation) -->
<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
        <h3 id="confirm-title" class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm action</h3>
        <p id="confirm-message" class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure?</p>
        <div class="mt-4 flex justify-end gap-2">
            <button id="confirm-cancel" class="px-3 py-2 rounded bg-gray-100 dark:bg-gray-800">Cancel</button>
            <button id="confirm-accept" class="px-3 py-2 rounded bg-red-600 text-white">Yes, continue</button>
        </div>
    </div>
</div>

<!-- Import preview modal -->
<div id="import-preview" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-2xl w-full p-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Import preview</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Review preferences from the file. Choose to apply (populate the form) or cancel.</p>

        <pre id="import-json" class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded text-xs max-h-64 overflow-auto"></pre>

        <div class="mt-4 flex justify-end gap-2">
            <button id="import-cancel" class="px-3 py-2 rounded bg-gray-100 dark:bg-gray-800">Cancel</button>
            <button id="import-apply" class="px-3 py-2 rounded bg-green-600 text-white">Apply to form</button>
        </div>
    </div>
</div>

  <div class="bg-white rounded shadow p-4 max-w-2xl">
    <h3 class="font-semibold mb-4">System Settings</h3>
    <div class="text-sm text-gray-600">Configure backup schedule, SMS provider credentials, and roles/permissions here.</div>
  </div>


{{-- small placeholder for toasts --}}
<div id="toast-container" aria-live="polite" class="fixed right-4 bottom-6 z-50"></div>

@push('scripts')
<script>
(function () {
    // Elements
    const form = document.getElementById('settings-form');
    const saveBtn = document.getElementById('save-btn');
    const resetBtn = document.getElementById('reset-btn');
    const exportBtn = document.getElementById('export-btn');
    const importFile = document.getElementById('import-file');
    const importPreview = document.getElementById('import-preview');
    const importJson = document.getElementById('import-json');
    const importCancel = document.getElementById('import-cancel');
    const importApply = document.getElementById('import-apply');
    const confirmModal = document.getElementById('confirm-modal');
    const confirmTitle = document.getElementById('confirm-title');
    const confirmMessage = document.getElementById('confirm-message');
    const confirmCancel = document.getElementById('confirm-cancel');
    const confirmAccept = document.getElementById('confirm-accept');
    const toastContainer = document.getElementById('toast-container');

    // toggles and inputs
    const emailToggle = document.getElementById('notif-email');
    const inAppToggle = document.getElementById('notif-inapp');

    // theme preview elements
    const previewBadge = document.getElementById('preview-badge');
    const previewCard = document.getElementById('preview-card');
    const previewCardTitle = document.getElementById('preview-card-title');
    const previewCardSub = document.getElementById('preview-card-sub');
    const previewCardCta = document.getElementById('preview-card-cta');

    // initial server preferences (blade-provided)
        // initial server-provided preferences (blade variables)
    const serverPrefs = {!! json_encode($preferences ?? [
        'notifications' => ['email' => false, 'in_app' => true],
        'theme' => 'system',
        'dashboard' => new \stdClass(), // empty object in JSON
    ], JSON_THROW_ON_ERROR) !!};


    // --- Small toast for feedback ---
    function toast(message, type='info', timeout=3000) {
        const id = 't-' + Date.now();
        const colors = {
            info: 'bg-indigo-600 text-white',
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white'
        };
        const el = document.createElement('div');
        el.id = id;
        el.className = `mb-3 px-4 py-2 rounded shadow ${colors[type] || colors.info} max-w-sm`;
        el.textContent = message;
        toastContainer.appendChild(el);
        setTimeout(() => {
            el.classList.add('opacity-0', 'transition', 'duration-300');
            setTimeout(() => el.remove(), 350);
        }, timeout);
    }

    // --- Accordion behavior ---
    document.querySelectorAll('[data-accordion-toggle]').forEach(btn => {
        const panel = btn.nextElementSibling;
        // default collapse if attribute aria-expanded false; else open
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        panel.style.display = expanded ? 'block' : 'none';
        if (!expanded) {
            btn.querySelector('.accordion-chevron').classList.remove('rotate-180');
        } else {
            btn.querySelector('.accordion-chevron').classList.add('rotate-180');
        }

        btn.addEventListener('click', () => {
            const isOpen = panel.style.display === 'block';
            if (isOpen) {
                panel.style.display = 'none';
                btn.setAttribute('aria-expanded', 'false');
                btn.querySelector('.accordion-chevron').classList.remove('rotate-180');
            } else {
                panel.style.display = 'block';
                btn.setAttribute('aria-expanded', 'true');
                btn.querySelector('.accordion-chevron').classList.add('rotate-180');
            }
        });

        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                btn.click();
            }
        });
    });

    // --- Toggle visuals for custom switches (move thumb & toggle hidden input) ---
    function wireThumb(input, thumbId) {
        const thumb = document.getElementById(thumbId);
        function update() {
            if (input.checked) {
                thumb.classList.add('translate-x-5');
            } else {
                thumb.classList.remove('translate-x-5');
            }
        }
        input.addEventListener('change', update);
        update();
    }
    wireThumb(emailToggle, 'notif-email-thumb');
    wireThumb(inAppToggle, 'notif-inapp-thumb');
    document.querySelectorAll('input[id^="dashboard-"]').forEach(ch => {
        const key = ch.id;
        const thumb = document.getElementById(`${key}-thumb`);
        function updateDash() {
            if (ch.checked) thumb.classList.add('translate-x-5'); else thumb.classList.remove('translate-x-5');
        }
        ch.addEventListener('change', updateDash);
        updateDash();
    });

    // --- Theme preview & applying theme locally ---
    function applyTheme(theme, showToast=false) {
        const root = document.documentElement;
        // compute resolved theme (system -> detect)
        let resolved = theme;
        if (theme === 'system') {
            resolved = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
        }

        if (resolved === 'dark') {
            root.classList.add('dark');
            previewBadge.textContent = 'Dark';
            previewCard.classList.remove('bg-white', 'text-gray-800');
            previewCard.classList.add('bg-gray-800', 'text-gray-100');
            previewCardTitle.classList.add('text-white');
            previewCardSub.classList.add('text-gray-300');
            previewCardCta.classList.add('text-white');
        } else {
            root.classList.remove('dark');
            previewBadge.textContent = 'Light';
            previewCard.classList.remove('bg-gray-800', 'text-gray-100');
            previewCard.classList.add('bg-white', 'text-gray-800');
            previewCardTitle.classList.remove('text-white');
            previewCardSub.classList.remove('text-gray-300');
            previewCardCta.classList.remove('text-white');
        }

        // subtle animated transition
        previewCard.animate([{ transform: 'translateY(-6px)', opacity: 0.95 }, { transform: 'translateY(0)', opacity: 1 }], { duration: 250 });

        // persist to local
        localStorage.setItem('theme', theme);
        if (showToast) toast('Theme applied locally', 'info', 1200);
    }

    // watch theme radios
    document.querySelectorAll('input[name="theme"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            applyTheme(e.target.value, true);
            // also update hidden fallback input
            document.querySelector('input[name="theme"][type="hidden"]').value = e.target.value;
            markDirty(true);
        });
    });

    // initialize UI state from serverPrefs
    function initUI() {
        // toggles
        emailToggle.checked = !!(serverPrefs.notifications && serverPrefs.notifications.email);
        inAppToggle.checked = !!(serverPrefs.notifications && serverPrefs.notifications.in_app);

        // theme radio
        const theme = serverPrefs.theme || localStorage.getItem('theme') || 'system';
        const themeRadios = document.querySelectorAll('input[name="theme"]');
        themeRadios.forEach(r => r.checked = (r.value === theme));
        applyTheme(theme);

        // dashboard toggles already set by Blade; ensure thumbs reflect
        document.querySelectorAll('input[name^="dashboard"]').forEach(i => {
            // nothing else
        });

        // wire test notification
    }
    initUI();

    // test notification (client side)
    document.getElementById('test-notification').addEventListener('click', () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'fixed left-4 bottom-20 z-50 max-w-sm';
        wrapper.innerHTML = `
            <div class="p-3 bg-white dark:bg-gray-800 shadow rounded-md border">
                <div class="flex items-start gap-3">
                    <div class="text-indigo-600 dark:text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Test Alert</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">This is a sample in-app notification.</div>
                    </div>
                    <button aria-label="Dismiss" class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 close-btn">✕</button>
                </div>
            </div>
        `;
        document.body.appendChild(wrapper);
        const close = wrapper.querySelector('.close-btn');
        close.addEventListener('click', () => wrapper.remove());
        setTimeout(() => wrapper.remove(), 6000);
    });

    // --- Export current form as JSON ---
    function gatherPreferencesFromForm() {
        const prefs = {
            notifications: {
                email: !!document.querySelector('input[name="notifications[email]"]')?.checked,
                in_app: !!document.querySelector('input[name="notifications[in_app]"]')?.checked
            },
            theme: document.querySelector('input[name="theme"]:checked')?.value || 'system',
            dashboard: {}
        };
        document.querySelectorAll('input[name^="dashboard"]').forEach(input => {
            // name like dashboard[key]
            const key = input.name.match(/\[([^\]]+)\]/)?.[1];
            prefs.dashboard[key] = !!input.checked;
        });
        return prefs;
    }

    exportBtn.addEventListener('click', () => {
        const prefs = gatherPreferencesFromForm();
        const blob = new Blob([JSON.stringify(prefs, null, 2)], {type: 'application/json'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `poultry-tracker-preferences-${(new Date()).toISOString().slice(0,10)}.json`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
        toast('Export prepared — saved as JSON', 'success', 1800);
    });

    // --- Import file handling ---
    let importedPrefs = null;
    importFile.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        if (file.type !== 'application/json' && !file.name.match(/\.json$/i)) {
            toast('Please select a valid JSON file', 'error');
            importFile.value = '';
            return;
        }
        try {
            const text = await file.text();
            const parsed = JSON.parse(text);
            // Basic validation: must contain notifications/theme/dashboard
            if (typeof parsed !== 'object' || parsed === null) throw new Error('Invalid JSON');
            importedPrefs = parsed;
            importJson.textContent = JSON.stringify(parsed, null, 2);
            importPreview.classList.remove('hidden');
            importPreview.style.display = 'flex';
        } catch (err) {
            console.error(err);
            toast('Failed to parse JSON file', 'error');
            importFile.value = '';
        }
    });

    importCancel.addEventListener('click', () => {
        importPreview.classList.add('hidden');
        importPreview.style.display = 'none';
        importFile.value = '';
        importedPrefs = null;
    });

    importApply.addEventListener('click', () => {
        if (!importedPrefs) return;
        // Show confirm modal before applying to form
        showConfirm('Apply imported preferences?', 'This will populate the form with values from the imported file. You can review and Save to persist.', () => {
            // populate toggles
            try {
                const np = importedPrefs.notifications || {};
                document.querySelector('input[name="notifications[email]"]').checked = !!np.email;
                document.querySelector('input[name="notifications[in_app]"]').checked = !!np.in_app;
                // theme
                if (importedPrefs.theme) {
                    const themeRadio = document.querySelector(`input[name="theme"][value="${importedPrefs.theme}"]`);
                    if (themeRadio) themeRadio.checked = true;
                    applyTheme(importedPrefs.theme, true);
                    document.querySelector('input[name="theme"][type="hidden"]').value = importedPrefs.theme;
                }
                // dashboard
                if (importedPrefs.dashboard && typeof importedPrefs.dashboard === 'object') {
                    Object.keys(importedPrefs.dashboard).forEach(k => {
                        const el = document.querySelector(`input[name="dashboard[${k}]"]`);
                        if (el) el.checked = !!importedPrefs.dashboard[k];
                    });
                }
                // update thumbs
                document.querySelectorAll('input[name^="dashboard"]').forEach(i => {
                    const thumb = document.getElementById(`${i.id}-thumb`);
                    if (thumb) {
                        if (i.checked) thumb.classList.add('translate-x-5'); else thumb.classList.remove('translate-x-5');
                    }
                });
                document.getElementById('notif-email-thumb').classList.toggle('translate-x-5', document.getElementById('notif-email').checked);
                document.getElementById('notif-inapp-thumb').classList.toggle('translate-x-5', document.getElementById('notif-inapp').checked);

                importPreview.classList.add('hidden');
                importPreview.style.display = 'none';
                importFile.value = '';
                importedPrefs = null;
                markDirty(true);
                toast('Imported preferences applied to form. Click Save to persist.', 'success', 2500);
            } catch (err) {
                console.error(err);
                toast('Failed to apply imported preferences', 'error');
            }
        }, () => {
            // cancelled
        });
    });

    // --- Reset to defaults with confirm ---
    resetBtn.addEventListener('click', () => {
        showConfirm('Reset to defaults?', 'This will reset settings to recommended defaults. You can Save to persist the defaults or Cancel.', () => {
            // defaults
            document.querySelector('input[name="notifications[email]"]').checked = false;
            document.querySelector('input[name="notifications[in_app]"]').checked = true;
            document.querySelectorAll('input[name="theme"]').forEach(r => r.checked = (r.value === 'system'));
            applyTheme('system', true);
            document.querySelectorAll('input[name^="dashboard"]').forEach(i => i.checked = true);
            // update thumbs
            document.querySelectorAll('input[name^="dashboard"]').forEach(i => {
                const thumb = document.getElementById(`${i.id}-thumb`);
                if (thumb) thumb.classList.add('translate-x-5');
            });
            document.getElementById('notif-email-thumb').classList.remove('translate-x-5');
            document.getElementById('notif-inapp-thumb').classList.add('translate-x-5');
            markDirty(true);
            toast('Defaults applied (preview). Save to persist.', 'info', 2000);
        });
    });

    // --- Confirm modal helper ---
    let confirmResolve = null;
    function showConfirm(title, message, onAccept, onCancel) {
        confirmTitle.textContent = title || 'Confirm';
        confirmMessage.textContent = message || '';
        confirmModal.classList.remove('hidden');
        confirmModal.style.display = 'flex';
        confirmAccept.focus();

        function cleanup() {
            confirmModal.classList.add('hidden');
            confirmModal.style.display = 'none';
            confirmAccept.removeEventListener('click', acceptHandler);
            confirmCancel.removeEventListener('click', cancelHandler);
            document.removeEventListener('keydown', escHandler);
        }
        function acceptHandler() {
            cleanup();
            if (typeof onAccept === 'function') onAccept();
        }
        function cancelHandler() {
            cleanup();
            if (typeof onCancel === 'function') onCancel();
        }
        function escHandler(e) {
            if (e.key === 'Escape') {
                cleanup();
                if (typeof onCancel === 'function') onCancel();
            }
        }
        confirmAccept.addEventListener('click', acceptHandler);
        confirmCancel.addEventListener('click', cancelHandler);
        document.addEventListener('keydown', escHandler);
    }

    // --- Unsaved changes warning ---
    let initialSnapshot = null;
    function snapshotForm() {
        // simple serializable snapshot: JSON of prefs
        return JSON.stringify(gatherPreferencesFromForm());
    }
    function markDirty(forceDirty=false) {
        if (forceDirty) {
            window._formDirty = true;
        } else {
            window._formDirty = (snapshotForm() !== initialSnapshot);
        }
    }
    // capture initial
    initialSnapshot = snapshotForm();
    window._formDirty = false;

    // watch changes on form fields to mark dirty
    form.addEventListener('change', () => markDirty(false));
    form.addEventListener('input', () => markDirty(false));

    // beforeunload
    window.addEventListener('beforeunload', (e) => {
        if (window._formDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // intercept internal link clicks (if unsaved)
    document.addEventListener('click', (e) => {
        // only anchor clicks with href
        const a = e.target.closest('a');
        if (!a) return;
        if (!a.href) return;
        const sameOrigin = a.origin === location.origin;
        if (!sameOrigin) return;
        if (window._formDirty) {
            e.preventDefault();
            showConfirm('You have unsaved changes', 'You have unsaved changes. Leaving will discard them. Continue?', () => {
                window._formDirty = false;
                location.href = a.href;
            }, () => {
                // do nothing
            });
        }
    });

    // mark clean on successful save (we detect response ok)
    form.addEventListener('submit', async (e) => {
        // let normal submit happen? We'll perform ajax to keep in-page behavior
        e.preventDefault();
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-70', 'cursor-not-allowed');
        const payload = new FormData(form);
        payload.set('_method', 'PUT');

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (res.ok) {
                toast('Settings saved', 'success', 1600);
                // update hidden fallbacks
                document.querySelector('input[name="notifications[in_app]"][type="hidden"]').value = emailToggle.checked ? '1' : (inAppToggle.checked ? '1' : '0');
                document.querySelector('input[name="notifications[email]"][type="hidden"]').value = emailToggle.checked ? '1' : '0';
                document.querySelector('input[name="theme"][type="hidden"]').value = document.querySelector('input[name="theme"]:checked')?.value || 'system';
                // snapshot
                initialSnapshot = snapshotForm();
                window._formDirty = false;
            } else {
                toast('Failed to save settings', 'error');
            }
        } catch (err) {
            console.error(err);
            toast('Network error. Settings may not be saved.', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    });

})();
</script>
@endpush

@endsection
