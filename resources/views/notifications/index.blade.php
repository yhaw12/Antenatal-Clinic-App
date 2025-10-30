@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">All alerts for your account — newest first.</p>
        </div>

        <div class="flex items-center gap-3">
            <form id="dismiss-all-form" action="{{ route('alerts.dismiss-all') }}" method="POST" class="inline">
                @csrf
                <button id="dismiss-all-button" type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-200 rounded-lg text-sm font-medium hover:bg-red-200 dark:hover:bg-red-800/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Dismiss All
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 transition">
                Back to Dashboard
            </a>
        </div>
    </div>

    @if (isset($error))
    <div role="status" aria-live="polite" class="mb-6">
        <div class="bg-red-50 dark:bg-red-900 p-4 rounded-md border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200">
            {{ $error }}
        </div>
    </div>
    @endif

    @if ($alerts->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <ul role="list" aria-label="Notifications list" class="divide-y divide-gray-200 dark:divide-gray-700/50">
            @foreach ($alerts as $alert)
                <li class="p-4 flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-xl ring-1 ring-gray-100 dark:ring-gray-700
                            {{ $alert->type === 'critical' ? 'text-red-700 bg-red-100 dark:bg-red-900/30' : 'text-blue-600 bg-blue-50 dark:bg-blue-900/10' }}">
                            @if ($alert->type === 'critical')
                                &#9888;
                            @else
                                &#128276;
                            @endif
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <a href="{{ $alert->url ?? '#' }}"
                                   class="block text-sm font-medium text-gray-900 dark:text-gray-100 truncate hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40"
                                   @if($alert->url) target="_blank" rel="noopener noreferrer" @endif>
                                    {{ $alert->message }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ ucfirst($alert->type) }} • {{ optional($alert->created_at)->diffForHumans() ?? '' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Mark as read form (POST) --}}
                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 transition"
                                            aria-label="Mark this notification as read">
                                        Mark as Read
                                    </button>
                                </form>

                                {{-- If the alert has a custom URL, show "Open" --}}
                                @if($alert->url)
                                    <a href="{{ $alert->url }}" target="_blank" rel="noopener noreferrer"
                                       class="text-xs px-3 py-1 rounded-full bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/10 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 transition"
                                       aria-label="Open notification link">
                                        Open
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/80 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <strong>{{ $alerts->firstItem() }}</strong> - <strong>{{ $alerts->lastItem() }}</strong> of <strong>{{ $alerts->total() }}</strong>
            </div>

            <div>
                {{ $alerts->links() }}
            </div>
        </div>
    </div>

    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-10 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a3 3 0 116 0v6M5 21h14" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No notifications</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You're all caught up — no unread alerts at the moment.</p>
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 transition">
                    Back to dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
/**
 * Enhance UX: prevent double submits, progressively enhance Dismiss All to use AJAX
 */
document.addEventListener('DOMContentLoaded', function () {
    const dismissForm = document.getElementById('dismiss-all-form');
    const dismissButton = document.getElementById('dismiss-all-button');

    if (!dismissForm || !dismissButton) return;

    dismissForm.addEventListener('submit', async function (e) {
        // progressive enhancement: try AJAX, fallback to regular submit
        e.preventDefault();

        // disable UI
        dismissButton.disabled = true;
        dismissButton.classList.add('opacity-60', 'pointer-events-none');

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(dismissForm.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            });

            if (res.ok) {
                // reload page (server changed DB), preserving UX
                window.location.reload();
            } else {
                // fallback: submit normally to allow server to return an error
                dismissForm.submit();
            }
        } catch (err) {
            // network error -> fallback to full form submit
            dismissForm.submit();
        } finally {
            dismissButton.disabled = false;
            dismissButton.classList.remove('opacity-60', 'pointer-events-none');
        }
    });
});
</script>
@endpush
