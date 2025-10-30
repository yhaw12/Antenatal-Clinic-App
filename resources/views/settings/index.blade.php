{{-- resources/views/settings.blade.php --}}
@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" style="color:var(--text); background:transparent;">

    <div class="rounded-xl p-6 shadow"
        style="background:var(--surface); color:var(--text); border:1px solid var(--border);">

        <h2 class="text-xl font-semibold mb-6" style="color:var(--text);">
            Theme & Appearance
        </h2>

        {{-- Theme Selector --}}
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm" style="color:var(--muted);">Theme</span>

            <select id="themeSelect"
                class="px-3 py-2 rounded-lg text-sm"
                style="background:var(--bg); color:var(--text); border:1px solid var(--border);">
                <option value="light" style="color:black;">Light</option>
                <option value="dark" style="color:black;">Dark</option>
                <option value="auto" style="color:black;">System Default</option>
            </select>
        </div>

        <p class="text-xs mt-2" style="color:var(--muted);">
            “System Default” automatically adjusts based on your device's theme settings.
        </p>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const themeSelect = document.getElementById('themeSelect');

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'auto';
    themeSelect.value = savedTheme;
    applyTheme(savedTheme);

    themeSelect.addEventListener('change', function () {
        const value = this.value;
        localStorage.setItem('theme', value);
        applyTheme(value);
    });

    function applyTheme(theme) {
        const html = document.documentElement;

        if (theme === 'light') {
            html.classList.remove('dark');
        } else if (theme === 'dark') {
            html.classList.add('dark');
        } else {
            // auto/system default
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
    }

    // In case system theme changes while in auto mode
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener('change', e => {
        const current = localStorage.getItem('theme') || 'auto';
        if (current === 'auto') applyTheme('auto');
    });
});
</script>
@endpush
@endsection
