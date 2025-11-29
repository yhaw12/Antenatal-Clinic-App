@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="min-h-screen font-sans py-8 px-4 sm:px-6 lg:px-8" 
     style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    <div class="max-w-4xl mx-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text)">Notifications</h1>
                <p class="text-sm font-medium mt-1" style="color: var(--muted)">Stay updated with patient and system alerts.</p>
            </div>

            <div class="flex items-center gap-3">
                @if ($alerts->isNotEmpty())
                    {{-- Dismiss All Form (Handled by JS for spinner, but works without it too) --}}
                    <form id="dismiss-all-form" action="{{ route('alerts.dismiss-all') }}" method="POST">
                        @csrf
                        <button id="dismiss-all-button" type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95"
                                style="background: var(--surface); color: var(--danger); border: 1px solid color-mix(in srgb, var(--danger) 20%, transparent);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Clear All
                        </button>
                    </form>
                @endif

                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95"
                   style="background: var(--brand); color: white;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Dashboard
                </a>
            </div>
        </div>

        @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl border flex items-start gap-3 shadow-sm animate-fade-in"
             style="background: color-mix(in srgb, var(--success) 10%, transparent); border-color: color-mix(in srgb, var(--success) 20%, transparent); color: var(--success);">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if (isset($error) || session('error'))
        <div class="mb-6 p-4 rounded-2xl border flex items-start gap-3 shadow-sm animate-fade-in"
             style="background: color-mix(in srgb, var(--danger) 10%, transparent); border-color: color-mix(in srgb, var(--danger) 20%, transparent); color: var(--danger);">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="font-medium">{{ $error ?? session('error') }}</span>
        </div>
        @endif

        @if ($alerts->isNotEmpty())
        <div class="glass-card rounded-3xl overflow-hidden border shadow-sm" style="background: var(--surface); border-color: var(--border);">
            <ul role="list" class="divide-y" style="border-color: var(--border);">
                @foreach ($alerts as $alert)
                <li class="group p-5 transition-colors hover:bg-gray-50 dark:hover:bg-white/5 relative overflow-hidden {{ $alert->is_read ? 'opacity-70' : '' }}">
                    @if($alert->type === 'critical')
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                    @endif

                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-12 w-12 rounded-2xl shadow-sm ring-1 ring-inset"
                                  style="@if($alert->type === 'critical') background: color-mix(in srgb, var(--danger) 10%, transparent); color: var(--danger); ring-color: color-mix(in srgb, var(--danger) 20%, transparent); @else background: color-mix(in srgb, var(--brand) 10%, transparent); color: var(--brand); ring-color: color-mix(in srgb, var(--brand) 20%, transparent); @endif">
                                @if ($alert->type === 'critical')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 003 11v3.159c0 .538-.214 1.055-.595 1.436L1 17h5m5 0v1a3 3 0 11-6 0v-1m6 0H5"/></svg>
                                @endif
                            </span>
                        </div>

                        <div class="flex-1 min-w-0 pt-1">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div>
                                    <a href="{{ $alert->url ?? '#' }}" 
                                       class="block text-base font-bold leading-snug hover:underline focus:outline-none"
                                       style="color: var(--text);"
                                       @if($alert->url) target="_blank" rel="noopener noreferrer" @endif>
                                        {{ $alert->message }}
                                    </a>
                                    <div class="mt-1 flex items-center gap-2 text-xs font-medium" style="color: var(--muted);">
                                        <span class="capitalize">{{ $alert->type }}</span>
                                        <span>•</span>
                                        <span>{{ optional($alert->created_at)->diffForHumans() ?? 'Just now' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 mt-2 sm:mt-0 self-start">
                                    @if($alert->url)
                                    <a href="{{ $alert->url }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors border"
                                       style="background: var(--surface); color: var(--brand); border-color: var(--border);">
                                        Open
                                    </a>
                                    @endif

                                    @if(!$alert->is_read)
                                    <form action="{{ route('alerts.read', $alert) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                                                style="color: var(--muted);"
                                                aria-label="Mark as read">
                                            Dismiss
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>

            <div class="px-6 py-4 border-t flex items-center justify-between" style="border-color: var(--border); background: color-mix(in srgb, var(--bg) 50%, transparent);">
                <div class="text-sm font-medium" style="color: var(--muted)">
                    Showing <strong>{{ $alerts->firstItem() }}</strong> - <strong>{{ $alerts->lastItem() }}</strong> of <strong>{{ $alerts->total() }}</strong>
                </div>
                <div>
                    {{ $alerts->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="glass-card rounded-3xl p-12 text-center border border-dashed shadow-sm" style="background: var(--surface); border-color: var(--border);">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full flex items-center justify-center" style="background: color-mix(in srgb, var(--surface) 90%, transparent);">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h3 class="text-lg font-bold" style="color: var(--text)">All Caught Up</h3>
            <p class="mt-2 text-sm" style="color: var(--muted)">You have no unread notifications at the moment.</p>
            <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all active:scale-95" style="background: var(--brand);">
                Return to Dashboard
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dismissForm = document.getElementById('dismiss-all-form');
    const dismissButton = document.getElementById('dismiss-all-button');

    if (!dismissForm || !dismissButton) return;

    dismissForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Optimistic UI: Disable and fade button
        dismissButton.disabled = true;
        dismissButton.style.opacity = '0.7';
        dismissButton.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Clearing...`;

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(dismissForm.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json' // Crucial: tells Controller to return JSON
                },
                body: JSON.stringify({})
            });

            if (res.ok) {
                window.location.reload();
            } else {
                dismissForm.submit(); // Fallback if AJAX fails logic but not network
            }
        } catch (err) {
            // If AJAX fails completely (e.g. CSRF token mismatch), fall back to standard submit
            dismissForm.submit(); 
        }
    });
});
</script>
@endpush