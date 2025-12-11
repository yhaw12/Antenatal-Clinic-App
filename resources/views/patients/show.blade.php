@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="min-h-screen font-sans" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    {{-- Header --}}
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
        
        {{-- Patient Card --}}
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

        {{-- Tabs --}}
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
                
                {{-- TAB: Scheduled --}}
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

                            <div class="flex gap-2 w-full sm:w-auto flex-wrap items-center">
    
                                {{-- LOGIC 1: CHECK CALL LOGS --}}
                                @php
                                    $lastCall = $appointment->callLogs->sortByDesc('created_at')->first();
                                @endphp

                                @if($lastCall)
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border" style="background: var(--bg); border-color: var(--border);">
                                        <div class="text-right leading-tight">
                                            <div class="text-[9px] uppercase font-bold tracking-wider" style="color: var(--muted)">Call Result</div>
                                            <div class="text-xs font-bold" style="color: {{ $lastCall->result == 'will_attend' ? 'var(--success)' : 'var(--brand)' }}">
                                                {{ str_replace('_', ' ', ucfirst($lastCall->result)) }}
                                            </div>
                                        </div>
                                        <button onclick="openCallModal({{ $appointment->id }}, '{{ addslashes($patient->first_name) }}', '{{ $patient->phone }}')" 
                                                class="p-1.5 rounded-lg transition-colors hover:bg-gray-200 dark:hover:bg-white/10" 
                                                title="Log another call">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <button onclick="openCallModal({{ $appointment->id }}, '{{ addslashes($patient->first_name) }}', '{{ $patient->phone }}')" 
                                            class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-bold transition-all hover:scale-105 active:scale-95" 
                                            style="background: var(--bg); color: var(--text); border: 1px solid var(--border);">
                                        📞 Log Call
                                    </button>
                                @endif


                                {{-- LOGIC 2: CHECK REFERRAL STATUS --}}
                                @if($appointment->status === 'referred')
                                    <div class="px-4 py-2 rounded-xl text-sm font-bold border opacity-70 cursor-not-allowed flex items-center gap-2" 
                                         style="background: color-mix(in srgb, var(--accent) 5%, transparent); color: var(--accent); border-color: var(--accent);">
                                        <span>↗ Referred</span>
                                    </div>
                                @else
                                    <button onclick="openReferralModal({{ $patient->id }}, {{ $appointment->id }})" 
                                            class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-bold transition-all hover:scale-105 active:scale-95" 
                                            style="background: color-mix(in srgb, var(--accent) 10%, transparent); color: var(--accent); border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);">
                                        ↗ Refer
                                    </button>
                                @endif


                                {{-- LOGIC 3: MARK SEEN --}}
                                @if($appointment->status === 'seen')
                                    <div class="px-4 py-2 rounded-xl text-sm font-bold border flex items-center gap-2" 
                                         style="background: color-mix(in srgb, var(--success) 5%, transparent); color: var(--success); border-color: var(--success);">
                                        <span>✓ Patient Seen</span>
                                    </div>
                                @elseif($appointment->status !== 'referred')
                                    <button onclick="openMarkSeenModal({{ $appointment->id }})" 
                                            class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-bold transition-all hover:scale-105 active:scale-95" 
                                            style="background: color-mix(in srgb, var(--success) 10%, transparent); color: var(--success); border: 1px solid color-mix(in srgb, var(--success) 20%, transparent);">
                                        ✓ Mark Seen
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 rounded-3xl border-2 border-dashed" style="border-color: var(--border); color: var(--muted);">
                            <p class="font-medium">No upcoming visits scheduled.</p>
                        </div>
                    @endforelse
                </div>

                {{-- TAB: History --}}
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

                {{-- TAB: Call Logs --}}
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

    {{-- 1. Call Modal --}}
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

    {{-- 2. Referral Modal --}}
    <div id="referralModal" class="fixed inset-0 z-50 hidden" aria-labelledby="referral-modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="referralBackdrop"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="referralModalPanel" class="relative transform overflow-hidden rounded-3xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     style="background: var(--surface); color: var(--text);">
                    <div class="px-6 py-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold" id="referral-modal-title">Refer Patient</h3>
                            <button type="button" onclick="closeReferralModal()" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="mb-6 p-4 rounded-2xl flex items-center gap-3" style="background: color-mix(in srgb, var(--accent) 10%, transparent); color: var(--accent);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span class="text-sm font-medium">Referring: <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong></span>
                        </div>
                        <form id="referralForm" method="POST" action="{{ route('referrals.store') }}">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            <input type="hidden" name="appointment_id" id="referralAppointmentId">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold mb-2" style="color: var(--muted)">Referral Notes / Reason <span class="text-red-500">*</span></label>
                                    <textarea name="reason" required rows="5" class="w-full rounded-xl border-0 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-accent text-gray-900 dark:text-white bg-transparent" placeholder="Enter clinical reason for referral..."></textarea>
                                </div>
                            </div>
                            <div class="mt-8 sm:flex sm:flex-row-reverse gap-3">
                                <button type="submit" id="submitReferralBtn" class="inline-flex w-full justify-center rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all active:scale-95 sm:w-auto" style="background: var(--accent);">
                                    <span class="btn-text">Submit Referral</span>
                                </button>
                                <button type="button" onclick="closeReferralModal()" class="mt-3 inline-flex w-full justify-center rounded-xl px-6 py-3 text-sm font-bold shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-white/5 sm:mt-0 sm:w-auto" style="color: var(--text); background: var(--surface);">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Mark Seen Modal (Restored) --}}
    <div id="markSeenModal" class="fixed inset-0 z-50 hidden" aria-labelledby="seen-modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="markSeenBackdrop"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="markSeenPanel" class="relative transform overflow-hidden rounded-3xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     style="background: var(--surface); color: var(--text);">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-6" style="background: color-mix(in srgb, var(--success) 10%, transparent);">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: var(--success);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2" style="color: var(--text)">Mark as Seen?</h3>
                        <p class="text-sm leading-relaxed mb-6" style="color: var(--muted)">
                            This will confirm the patient has been attended to and remove them from the active queue.
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button type="button" onclick="closeMarkSeenModal()" 
                                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
                                    style="color: var(--text); background: var(--bg); border: 1px solid var(--border);">
                                Cancel
                            </button>
                            <button type="button" id="confirmSeenBtn" onclick="processMarkSeen()" 
                                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:scale-105 active:scale-95"
                                    style="background: var(--success);">
                                Yes, Mark Seen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>
</div>

<style>
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


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Toast Logic ---
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

    // --- Tab Logic ---
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    window.switchTab = (targetId) => {
        panels.forEach(p => p.classList.add('hidden'));
        document.getElementById(targetId).classList.remove('hidden');
        tabs.forEach(btn => {
            if(btn.dataset.target === '#' + targetId) {
                btn.classList.add('active');
                btn.style.color = '';
            } else {
                btn.classList.remove('active');
                btn.style.color = 'var(--muted)';
            }
        });
    };
    switchTab('scheduled');

    // --- Helper: Modal Toggle ---
    function toggleModal(modalId, show) {
        const modal = document.getElementById(modalId);
        const backdrop = modal.querySelector('div[id$="Backdrop"]');
        const panel = modal.querySelector('div[id$="Panel"]');

        if(show) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        } else {
            backdrop.classList.add('opacity-0');
            panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    }

    // ========================
    // 1. Call Modal Logic
    // ========================
    window.openCallModal = (id, name, phone) => {
        document.getElementById('callAppointmentId').value = id;
        document.getElementById('callPatientName').textContent = name;
        const telLink = document.getElementById('telLink');
        phone ? (telLink.href = `tel:${phone}`, telLink.style.display = 'inline') : telLink.style.display = 'none';
        toggleModal('callModal', true);
    };

    window.closeCallModal = () => toggleModal('callModal', false);

    document.getElementById('callLogForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('saveCallBtn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            if(!response.ok) throw new Error('Failed');

            showToast('Call logged successfully');
            closeCallModal();
            setTimeout(() => window.location.reload(), 300); 
        } catch (error) {
            showToast('Error saving log.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // ========================
    // 2. Referral Modal Logic
    // ========================
    window.openReferralModal = (patientId, appointmentId) => {
        document.getElementById('referralForm').reset();
        document.getElementById('referralAppointmentId').value = appointmentId;
        toggleModal('referralModal', true);
    };

    window.closeReferralModal = () => toggleModal('referralModal', false);

    document.getElementById('referralForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('submitReferralBtn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = 'Sending...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            if(!response.ok) throw new Error('Failed');

            showToast('Referral sent successfully!', 'success');
            closeReferralModal();
            setTimeout(() => window.location.reload(), 300); 
        } catch (error) {
            showToast('Error sending referral.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // ========================
    // 3. Mark Seen Modal Logic
    // ========================
    let appointmentToMark = null;

    window.openMarkSeenModal = (id) => {
        appointmentToMark = id;
        toggleModal('markSeenModal', true);
    };

    window.closeMarkSeenModal = () => {
        toggleModal('markSeenModal', false);
        setTimeout(() => { appointmentToMark = null; }, 300);
    };

    window.processMarkSeen = async () => {
        if (!appointmentToMark) return;

        const btn = document.getElementById('confirmSeenBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Processing...';
        btn.disabled = true;

        try {
            const response = await fetch(`/appointments/${appointmentToMark}/mark-seen`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed');

            showToast('Marked as seen!', 'success');
            closeMarkSeenModal();
            
            // RELOAD THE PAGE to move the item to history
            setTimeout(() => window.location.reload(), 300);

        } catch (error) {
            console.error(error);
            showToast('Failed to update status', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    };
});
</script>
@endpush
@endsection