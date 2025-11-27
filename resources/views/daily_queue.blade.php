@extends('layouts.app')

@section('title', 'Daily Queue')

@section('content')
<div class="min-h-screen font-sans" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    <header class="sticky top-0 z-40 transition-all duration-200" style="backdrop-filter: blur(16px); background: color-mix(in srgb, var(--bg) 90%, transparent); border-bottom: 1px solid var(--border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-md" style="background: linear-gradient(135deg, var(--brand), var(--accent));">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold" style="color: var(--text)">Daily Queue</h1>
                    <p class="text-xs font-medium" style="color: var(--muted)">{{ \Carbon\Carbon::parse(request('date', now()))->format('l, M d, Y') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('daily-queue') }}" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ request('date', now()->toDateString()) }}" 
                           class="px-3 py-2 rounded-xl text-sm font-medium border bg-transparent focus:ring-2 focus:ring-brand/20 outline-none transition-all"
                           style="border-color: var(--border); color: var(--text);"
                           onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="glass-card rounded-3xl shadow-sm border overflow-hidden min-h-[500px]" style="background: var(--surface); border-color: var(--border);">
            
            <div class="px-6 py-4 border-b flex items-center justify-between bg-gray-50/50 dark:bg-white/5" style="border-color: var(--border);">
                <h2 class="font-bold text-sm uppercase tracking-wide" style="color: var(--muted)">Appointments List</h2>
                <span class="text-xs font-bold px-2 py-1 rounded-lg bg-brand/10 text-brand">{{ $daily->count() }} Total</span>
            </div>

            <div class="divide-y" style="border-color: var(--border);">
                @forelse($daily as $appt)
                    @php
                        $patient = $appt->patient;
                        $status = $appt->status;
                        $time = $appt->time ? \Carbon\Carbon::parse($appt->time)->format('h:i A') : 'TBD';
                    @endphp

                    <div class="group p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0" 
                                 style="background: linear-gradient(135deg, var(--brand), var(--accent));">
                                {{ strtoupper(substr($patient->first_name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-base" style="color: var(--text)">
                                    {{ $patient->first_name ?? 'Unknown' }} {{ $patient->last_name ?? '' }}
                                </h3>
                                <div class="flex items-center gap-2 text-xs mt-1" style="color: var(--muted)">
                                    <span>{{ $time }}</span>
                                    <span>•</span>
                                    @role('admin')
                                        <span>{{ $patient->phone ?? 'No Phone' }}</span>
                                    @else
                                        <span>{{ $patient->phone ? Str::mask($patient->phone, '*', 3, 4) : 'No Phone' }}</span>
                                    @endrole
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 self-end sm:self-auto">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border"
                                  style="
                                  @if($status === 'present') background: color-mix(in srgb, var(--success) 10%, transparent); color: var(--success); border-color: color-mix(in srgb, var(--success) 20%, transparent);
                                  @elseif($status === 'missed') background: color-mix(in srgb, var(--danger) 10%, transparent); color: var(--danger); border-color: color-mix(in srgb, var(--danger) 20%, transparent);
                                  @else background: color-mix(in srgb, var(--brand) 10%, transparent); color: var(--brand); border-color: color-mix(in srgb, var(--brand) 20%, transparent);
                                  @endif">
                                {{ ucfirst($status) }}
                            </span>

                            <div class="flex items-center gap-2">
                                @if($status !== 'present' && $status !== 'missed')
                                    <button onclick="markStatus({{ $appt->id }}, 'present')" class="p-2 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors" title="Mark Present">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button onclick="markStatus({{ $appt->id }}, 'missed')" class="p-2 rounded-lg hover:bg-rose-50 text-rose-600 transition-colors" title="Mark Missed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif
                                
                                <a href="{{ route('patients.show', $patient->id) }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="View Profile">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center flex flex-col items-center justify-center" style="color: var(--muted)">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="font-medium">No appointments scheduled for this date.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>

@endsection

@push('scripts')
<script>
    // Toast Helper
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const el = document.createElement('div');
        const bg = type === 'success' ? 'bg-emerald-600' : 'bg-rose-600';
        el.className = `${bg} text-white px-6 py-3 rounded-xl shadow-lg transform transition-all translate-y-10 opacity-0 pointer-events-auto font-medium`;
        el.innerText = message;
        container.appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-y-10', 'opacity-0'));
        setTimeout(() => {
            el.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => el.remove(), 300);
        }, 3000);
    }

    // Quick Status Actions
    async function markStatus(id, status) {
        if(!confirm(`Mark this appointment as ${status.toUpperCase()}?`)) return;

        const endpoint = status === 'present' ? '/daily-queue/mark-present' : '/daily-queue/mark-absent';
        
        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ appointment_ids: [id] })
            });

            if(res.ok) {
                showToast(`Marked as ${status}`);
                setTimeout(() => window.location.reload(), 500);
            } else {
                showToast('Action failed', 'error');
            }
        } catch(e) {
            console.error(e);
            showToast('Network error', 'error');
        }
    }
</script>
@endpush