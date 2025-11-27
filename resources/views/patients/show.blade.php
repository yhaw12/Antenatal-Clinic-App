@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="min-h-screen font-sans" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    <header class="sticky top-0 z-40 transition-all duration-200" style="backdrop-filter: blur(16px); background: color-mix(in srgb, var(--bg) 90%, transparent); border-bottom: 1px solid var(--border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-4">
                <a href="{{ route('patients.index') }}" class="p-2 rounded-xl border transition-all hover:scale-105" style="background: var(--surface); border-color: var(--border); color: var(--muted);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-lg font-bold" style="color: var(--text)">Patient Profile</h1>
                    <div class="flex items-center gap-2 text-xs font-medium" style="color: var(--muted)">
                        <span>{{ $patient->hospital_number ?? 'No Folder No' }}</span>
                        <span>•</span>
                        <span>{{ $patient->id_number ?? 'No ID' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('patients.edit', $patient->id) }}" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-xl border transition-all hover:bg-gray-50 dark:hover:bg-white/5" style="background: var(--surface); border-color: var(--border); color: var(--text);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit
                </a>
                <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all active:scale-95" style="background: var(--brand);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">New Appointment</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <div class="glass-card rounded-3xl p-8 relative overflow-hidden shadow-sm border group" style="background:var(--surface); border-color: var(--border);">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
            
            <div class="relative flex flex-col md:flex-row gap-8 items-start md:items-center">
                <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-3xl font-black text-white shadow-xl shadow-brand/20 shrink-0" 
                     style="background: linear-gradient(135deg, var(--brand), var(--accent));">
                    @if(!empty($patient->avatar_url))
                        <img src="{{ $patient->avatar_url }}" alt="Avatar" class="w-full h-full object-cover rounded-2xl">
                    @else
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name ?? '', 0, 1)) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0 space-y-2">
                    <h2 class="text-3xl font-black tracking-tight" style="color: var(--text)">
                        {{ $patient->first_name }} {{ $patient->last_name }}
                    </h2>
                    
                    <div class="flex flex-wrap gap-3">
                        @if($patient->phone)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg border text-sm font-medium transition-colors cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5" 
                             style="background: color-mix(in srgb, var(--surface) 50%, transparent); border-color: var(--border); color: var(--text);"
                             onclick="navigator.clipboard.writeText('{{ $patient->phone }}'); showToast('Phone copied!');">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $patient->phone }}
                        </div>
                        @endif
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg border text-sm font-medium" 
                             style="background: color-mix(in srgb, var(--surface) 50%, transparent); border-color: var(--border); color: var(--muted);">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $patient->address ?? 'No Address' }}
                        </div>
                    </div>
                </div>

                <div class="flex gap-6 md:border-l md:pl-8" style="border-color: var(--border);">
                    <div class="text-center">
                        <div class="text-3xl font-black" style="color: var(--brand)">{{ $visitHistory->count() }}</div>
                        <div class="text-xs font-bold uppercase tracking-wider" style="color: var(--muted)">Past Visits</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black" style="color: var(--success)">{{ $scheduledVisits->count() }}</div>
                        <div class="text-xs font-bold uppercase tracking-wider" style="color: var(--muted)">Upcoming</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            
            <div class="flex p-1 rounded-xl overflow-x-auto no-scrollbar w-full sm:w-auto gap-2" style="background: color-mix(in srgb, var(--bg) 50%, transparent);">
                <button class="tab-btn flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap active" 
                        data-target="#scheduled" onclick="switchTab('scheduled')">
                    Scheduled Visits
                </button>
                <button class="tab-btn flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap" 
                        data-target="#history" onclick="switchTab('history')" style="color: var(--muted)">
                    Visit History
                </button>
                <button class="tab-btn flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap" 
                        data-target="#calls" onclick="switchTab('calls')" style="color: var(--muted)">
                    Call Logs
                </button>
            </div>

            <div id="tab-contents">
                
                <div id="scheduled" class="tab-panel space-y-4">
                    @forelse($scheduledVisits as $appointment)
                        <div class="group bg-white dark:bg-gray-800 p-5 rounded-2xl border hover:border-blue-300 dark:hover:border-blue-700 shadow-sm flex flex-col sm:flex-row justify-between gap-4 items-start sm:items-center transition-all animate-fade-in" 
                             style="background: var(--surface); border-color: var(--border);">
                            
                            <div class="flex gap-5 items-center">
                                <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex flex-col items-center justify-center border shadow-sm" 
                                     style="background: var(--bg); border-color: var(--border); color: var(--brand);">
                                    <span class="text-[10px] font-bold uppercase">{{ \Carbon\Carbon::parse($appointment->date)->format('M') }}</span>
                                    <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($appointment->date)->format('d') }}</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg" style="color: var(--text)">
                                        {{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : 'Time TBD' }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide" 
                                          style="background: color-mix(in srgb, var(--brand) 10%, transparent); color: var(--brand);">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2 w-full sm:w-auto ">
                                <button onclick="openCallModal({{ $appointment->id }}, '{{ addslashes($patient->first_name) }}', '{{ $patient->phone }}')"
                                        class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-bold transition-all hover:scale-105 active:scale-95"
                                        style="background: var(--bg); color: var(--text); border: 1px solid var(--border);">
                                    📞 Log Call
                                </button>
                                @if($appointment->status !== 'seen')
                                    <button onclick="markSeen({{ $appointment->id }}, this)"
                                            class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-bold transition-all hover:scale-105 active:scale-95"
                                            style="background: color-mix(in srgb, var(--success) 10%, transparent); color: var(--success); border: 1px solid color-mix(in srgb, var(--success) 20%, transparent);">
                                        ✓ Mark Seen
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 rounded-3xl border-2 border-dashed" style="border-color: var(--border); color: var(--muted);">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="font-medium">No upcoming visits scheduled.</p>
                        </div>
                    @endforelse
                </div>

                <div id="history" class="tab-panel hidden space-y-4">
                    @forelse($visitHistory as $appointment)
                        <div class="p-5 rounded-2xl border shadow-sm flex gap-5 animate-fade-in" style="background: var(--surface); border-color: var(--border);">
                            <div class="w-1.5 self-stretch rounded-full" 
                                 style="background: {{ $appointment->status === 'missed' ? 'var(--danger)' : ($appointment->status === 'seen' ? 'var(--success)' : 'var(--muted)') }}"></div>
                            
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-lg" style="color: var(--text)">{{ \Carbon\Carbon::parse($appointment->date)->format('F d, Y') }}</h4>
                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-md"
                                          style="background: {{ $appointment->status === 'missed' ? 'color-mix(in srgb, var(--danger) 10%, transparent)' : 'color-mix(in srgb, var(--success) 10%, transparent)' }}; 
                                                 color: {{ $appointment->status === 'missed' ? 'var(--danger)' : 'var(--success)' }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                <p class="text-sm mt-2 leading-relaxed" style="color: var(--muted)">
                                    {{ $appointment->notes ?? 'No clinical notes recorded for this visit.' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 rounded-3xl border-2 border-dashed" style="border-color: var(--border); color: var(--muted);">
                            <p class="font-medium">No past visit history found.</p>
                        </div>
                    @endforelse
                </div>

                <div id="calls" class="tab-panel hidden space-y-4">
                    @forelse($callHistory as $call)
                        <div class="p-5 rounded-2xl border shadow-sm transition-all hover:border-brand/30 animate-fade-in" style="background: var(--surface); border-color: var(--border);">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide" 
                                          style="background: {{ $call->result === 'no_answer' ? 'color-mix(in srgb, var(--danger) 10%, transparent)' : 'color-mix(in srgb, var(--brand) 10%, transparent)' }}; 
                                                 color: {{ $call->result === 'no_answer' ? 'var(--danger)' : 'var(--brand)' }}">
                                        {{ str_replace('_', ' ', $call->result) }}
                                    </span>
                                    <span class="text-xs font-mono" style="color: var(--muted)">{{ $call->created_at->format('h:i A') }}</span>
                                </div>
                                <span class="text-xs font-bold" style="color: var(--muted)">{{ $call->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="text-sm leading-relaxed" style="color: var(--text)">
                                {{ $call->notes ?? 'No notes provided.' }}
                            </p>
                            <div class="mt-4 pt-3 border-t flex items-center gap-2 text-xs" style="border-color: var(--border); color: var(--muted);">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white" style="background: var(--muted);">
                                    {{ substr($call->caller->name ?? 'S', 0, 1) }}
                                </div>
                                Logged by <span class="font-medium">{{ $call->caller->name ?? 'System' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 rounded-3xl border-2 border-dashed" style="border-color: var(--border); color: var(--muted);">
                            <p class="font-medium">No calls logged yet.</p>
                            <button onclick="document.querySelector('[onclick*=\'openCallModal\']').click()" class="text-sm font-bold hover:underline mt-2 inline-block" style="color: var(--brand)">
                                Log a call now
                            </button>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </main>

    <div id="callModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="callModalPanel" class="relative transform overflow-hidden rounded-3xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" style="background: var(--surface); color: var(--text);">
                    
                    <div class="px-6 py-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold" id="modal-title">Log Call Result</h3>
                            <button type="button" onclick="closeCallModal()" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="mb-6 p-4 rounded-2xl flex items-center justify-between" style="background: color-mix(in srgb, var(--brand) 10%, transparent); color: var(--brand);">
                            <span>Calling: <span id="callPatientName" class="font-bold"></span></span>
                            <a id="telLink" href="#" class="font-bold hover:underline">Dial Now ↗</a>
                        </div>

                        <form id="callLogForm" method="POST" action="{{ route('call_logs.store') }}">
                            @csrf
                            <input type="hidden" name="appointment_id" id="callAppointmentId">
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            
                            <div class="space-y-5">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium" style="color:var(--muted)">Call Result <span class="text-red-500">*</span></label>
                                    <select name="result" id="callResult" required class="w-full px-4 py-3 border rounded-xl"
                                            style="background:var(--bg); border:1px solid var(--border); color:var(--text);">
                                        <option value="">Select outcome</option>
                                        <option value="no_answer">No Answer</option>
                                        <option value="rescheduled">Rescheduled</option>
                                        <option value="will_attend">Will Attend</option>
                                        <option value="refused">Refused</option>
                                        <option value="incorrect_number">Incorrect Number</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold mb-2" style="color: var(--muted)">Notes</label>
                                    <textarea name="notes" rows="3" class="w-full rounded-xl border-0 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-brand text-gray-900 dark:text-white bg-transparent" placeholder="Add details..."></textarea>
                                </div>
                            </div>

                            <div class="mt-8 sm:flex sm:flex-row-reverse gap-3">
                                <button type="submit" id="saveCallBtn" class="inline-flex w-full justify-center rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all active:scale-95 sm:w-auto" style="background: var(--brand);">
                                    <span class="btn-text">Save Log</span>
                                </button>
                                <button type="button" onclick="closeCallModal()" class="mt-3 inline-flex w-full justify-center rounded-xl px-6 py-3 text-sm font-bold shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/5 sm:mt-0 sm:w-auto" style="color: var(--text); background: var(--surface);">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>
</div>

<style>
    /* Tab Styling using Brand Color */
    .tab-btn.active {
        background-color: var(--brand);
        color: white;
        box-shadow: 0 4px 6px -1px color-mix(in srgb, var(--brand) 30%, transparent);
    }
    .tab-btn.active:hover { color: white; }
    
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Tab Logic ---
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    window.switchTab = (targetId) => {
        panels.forEach(p => p.classList.add('hidden'));
        document.getElementById(targetId).classList.remove('hidden');
        tabs.forEach(btn => {
            if(btn.dataset.target === '#' + targetId) {
                btn.classList.add('active');
                // Reset inline color styles that might override class
                btn.style.color = '';
            } else {
                btn.classList.remove('active');
                // Apply muted color to inactive
                btn.style.color = 'var(--muted)';
            }
        });
    };

    // Initialize Tabs
    switchTab('scheduled');

    // --- Modal Logic ---
    window.openCallModal = (id, name, phone) => {
        const modal = document.getElementById('callModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('callModalPanel');
        
        document.getElementById('callAppointmentId').value = id;
        document.getElementById('callPatientName').textContent = name;
        
        const telLink = document.getElementById('telLink');
        if(phone) {
            telLink.href = `tel:${phone}`;
            telLink.style.display = 'inline';
        } else {
            telLink.style.display = 'none';
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    };

    window.closeCallModal = () => {
        const modal = document.getElementById('callModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('callModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('callLogForm').reset();
        }, 300);
    };

    // --- AJAX Form ---
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const bgStyle = type === 'success' ? 'background: var(--success);' : 'background: var(--danger);';
        
        toast.style.cssText = `${bgStyle} color: white;`;
        toast.className = 'px-6 py-3 rounded-xl shadow-2xl transform transition-all duration-300 translate-y-10 opacity-0 pointer-events-auto flex items-center gap-3 font-bold';
        toast.innerHTML = `<span>${message}</span>`;
        
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-y-10', 'opacity-0'));
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    document.getElementById('callLogForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('saveCallBtn');
        
        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            if(!response.ok) throw new Error('Failed to save log');

            showToast('Call logged successfully');
            closeCallModal();
            window.location.reload(); 
        } catch (error) {
            showToast('Error saving log.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<span class="btn-text">Save Log</span>';
        }
    });

    // Mark Seen AJAX
    window.markSeen = async (id, btnElement) => {
        if(!confirm('Mark this appointment as attended/seen?')) return;

        const originalText = btnElement.innerHTML;
        btnElement.innerText = 'Processing...';
        btnElement.disabled = true;

        try {
            const response = await fetch(`/appointments/${id}/mark-seen`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if(!response.ok) throw new Error('Failed');

            showToast('Marked as seen!');
            setTimeout(() => window.location.reload(), 500);

        } catch (error) {
            console.error(error);
            showToast('Failed to update status', 'error');
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
        }
    };
});
</script>
@endpush