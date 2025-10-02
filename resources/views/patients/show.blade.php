@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-6">
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <!-- Avatar with optional image fallback -->
                <div class="h-20 w-20 rounded-full overflow-hidden bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-semibold text-2xl" aria-hidden="true">
                    @if(!empty($patient->avatar_url))
                        <img src="{{ $patient->avatar_url }}" alt="{{ $patient->first_name }} avatar" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($patient->first_name,0,1).($patient->last_name ? substr($patient->last_name,0,1) : '')) }}
                    @endif
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $patient->first_name }} {{ $patient->last_name ?? '' }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Hospital No: <span class="font-medium text-gray-700 dark:text-gray-200">{{ $patient->hospital_number ?? 'N/A' }}</span>
                        • ID: <span class="font-medium text-gray-700 dark:text-gray-200">{{ $patient->id_number ?? 'N/A' }}</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m7 4l2 2-2 2M14 10v6"/></svg>
                        <span id="patientPhoneLabel">{{ $patient->phone ?? 'N/A' }}</span>
                        @if($patient->phone)
                            <button id="copyPhoneBtn" class="ml-2 text-xs px-2 py-1 border rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" title="Copy phone">Copy</button>
                            <a id="quickCallBtn" href="tel:{{ $patient->phone }}" class="ml-2 text-xs px-2 py-1 border rounded text-blue-600 dark:text-blue-300 hover:underline">Call</a>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" aria-label="Back to all patients">
                    Back
                </a>

                <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg font-medium hover:from-emerald-600 hover:to-emerald-700 shadow transition-all" aria-label="Schedule new appointment for {{ $patient->first_name }}">
                    Schedule Appointment
                </a>

                <button id="printBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all" title="Print appointments">
                    Print
                </button>
            </div>
        </div>

        <!-- Controls: search + filter + sort + counts -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <label for="searchAppointments" class="sr-only">Search appointments</label>
                <input id="searchAppointments" type="search" placeholder="Search by notes, date or status..." class="w-full md:w-72 px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500" />

                <select id="statusFilter" class="px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-700">
                    <option value="all">All statuses</option>
                    <option value="queued">Queued</option>
                    <option value="in_room">In Room</option>
                    <option value="seen">Seen</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No Show</option>
                </select>

                <select id="sortBy" class="px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-700">
                    <option value="date_desc">Newest first</option>
                    <option value="date_asc">Oldest first</option>
                </select>
            </div>

            <div class="text-sm text-gray-600 dark:text-gray-300">
                <span id="appointmentsCount">0</span> appointments
                <span class="ml-3 text-gray-500">•</span>
                <span class="ml-3">Last updated: <span id="lastUpdated">—</span></span>
            </div>
        </div>

        <!-- Appointment History -->
        <section aria-labelledby="appointmentsHeading" class="mb-8">
            <h2 id="appointmentsHeading" class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Appointment History
            </h2>

            <div id="appointmentsList" class="space-y-4 max-h-[34rem] overflow-y-auto pr-2">
                {{-- Appointment cards will be rendered server-side initially and enhanced client-side --}}
                @forelse($patient->appointments as $appointment)
                    <article class="appointment-card p-4 rounded-lg border shadow-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-700 flex justify-between items-start" data-id="{{ $appointment->id }}" data-status="{{ $appointment->status }}" data-date="{{ $appointment->date }}" data-time="{{ $appointment->time ? $appointment->time->format('H:i') : '' }}" aria-labelledby="appt-title-{{ $appointment->id }}">
                        <div class="flex gap-4 items-start">
                            <div class="flex-shrink-0 pt-1">
                                <!-- small icon -->
                                <div class="h-10 w-10 rounded-lg bg-white dark:bg-gray-800 border flex items-center justify-center text-gray-600 dark:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14"/>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <h3 id="appt-title-{{ $appointment->id }}" class="text-sm font-semibold text-gray-900 dark:text-white">
                                    <span class="inline-block mr-2">{{ \Carbon\Carbon::parse($appointment->date)->format('d M, Y') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->time ? $appointment->time->format('H:i') : 'Time N/A' }}</span>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-prose">
                                    {{ Str::limit($appointment->notes ?? 'No notes', 120) }}
                                </p>

                                <div class="mt-2 flex items-center gap-2">
                                    <span class="status-badge inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold {{ in_array($appointment->status, ['queued','in_room','seen']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ ucfirst(str_replace('_',' ',$appointment->status)) }}
                                    </span>

                                    @if($appointment->rescheduled_from)
                                        <span class="text-xs text-yellow-700 dark:text-yellow-300">(rescheduled)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <div class="flex gap-2">
                                <button onclick="openCallModal({{ $appointment->id }}, '{{ addslashes($patient->first_name . ' ' . ($patient->last_name ?? '')) }}', '{{ $patient->phone ?? '' }}')" class="px-3 py-2 border rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-600" aria-label="Log call for appointment {{ $appointment->id }}">
                                    Call / Log
                                </button>

                                <button onclick="markSeen({{ $appointment->id }})" class="px-3 py-2 border rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-600" title="Mark as seen">
                                    Mark seen
                                </button>
                            </div>

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $appointment->date }}T{{ $appointment->time ? $appointment->time->format('H:i') : '00:00' }}" class="relative-date">—</time>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400 rounded-lg border border-dashed">
                        <svg class="h-12 w-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mb-2">No appointments found.</p>
                        <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg">Create first appointment</a>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Call Modal -->
        <div id="callModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="callModalTitle">
            <div id="callModalPanel" class="w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-2xl transform transition-all scale-95 opacity-0 overflow-hidden" role="document">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center justify-between">
                        <h3 id="callModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Log Call — <span id="callPatientName"></span></h3>
                        <button id="closeCallModalBtn" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all" aria-label="Close modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <form id="callLogForm" class="p-6 space-y-6" method="POST" action="{{ route('call-logs.store') }}">
                    @csrf
                    <input type="hidden" name="appointment_id" id="callAppointmentId">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Call Result <span class="text-red-500">*</span></label>
                        <select name="result" id="callResult" required class="w-full px-4 py-3 border rounded-xl bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all shadow-sm">
                            <option value="">Select outcome</option>
                            <option value="no_answer">No Answer</option>
                            <option value="rescheduled">Rescheduled</option>
                            <option value="will_attend">Will Attend</option>
                            <option value="refused">Refused</option>
                            <option value="incorrect_number">Incorrect Number</option>
                        </select>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-3 border rounded-xl bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all shadow-sm" placeholder="Additional details about the call..."></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a id="telLink" href="#" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition-colors" target="_blank" rel="noopener noreferrer">📞 Call Again</a>
                        <div class="flex gap-3">
                            <button type="button" id="cancelCallBtn" class="px-6 py-3 border rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button id="saveCallBtn" type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium shadow hover:shadow-lg transition-all">
                                <span class="btn-text">Save Log</span>
                                <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 ml-2 inline-block" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast container (aria-live) -->
        <div id="toast" class="fixed right-6 bottom-6 z-50 space-y-2" aria-live="polite" aria-atomic="true"></div>
    </div>
</div>

@push('scripts')
<script>
/* ==========================
   Utility & micro UX helpers
   ========================== */

// Format relative date simple (no deps)
function relativeDateFrom(dateStr, timeStr = '') {
    try {
        const base = timeStr ? dateStr + ' ' + timeStr : dateStr;
        const d = new Date(base);
        const now = new Date();
        const diff = Math.round((now - d) / 1000); // seconds
        if (isNaN(diff)) return new Date(dateStr).toLocaleDateString();
        if (Math.abs(diff) < 60) return Math.abs(diff) + 's ago';
        if (Math.abs(diff) < 3600) return Math.round(Math.abs(diff) / 60) + 'm ago';
        if (Math.abs(diff) < 86400) return Math.round(Math.abs(diff) / 3600) + 'h ago';
        return d.toLocaleString();
    } catch (e) {
        return dateStr;
    }
}

// Toast
function toast(message, type = 'success') {
    const el = document.getElementById('toast');
    const id = 't' + Date.now();
    const color = type === 'error' ? 'bg-red-600 border-red-400 text-white' : type === 'warn' ? 'bg-yellow-500 border-yellow-400 text-black' : 'bg-emerald-500 border-emerald-400 text-white';
    const node = document.createElement('div');
    node.id = id;
    node.setAttribute('role','status');
    node.className = `border-l-4 ${color} px-4 py-3 rounded-lg shadow-lg max-w-sm transform translate-y-6 opacity-0 transition-all duration-300`;
    node.innerHTML = `<div class="flex items-center gap-3"><div class="flex-1 text-sm font-medium">${message}</div><button aria-label="Dismiss" class="ml-2">✕</button></div>`;
    el.prepend(node);
    // entrance
    requestAnimationFrame(()=> {
        node.classList.remove('translate-y-6','opacity-0');
        node.classList.add('translate-y-0','opacity-100');
    });
    // dismiss
    const dismiss = node.querySelector('button');
    const remove = () => node.remove();
    dismiss.addEventListener('click', remove);
    setTimeout(() => {
        node.classList.add('translate-y-6','opacity-0');
        setTimeout(remove, 500);
    }, 3500);
}

/* ==========================
   Appointment list client behavior
   ========================== */

document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('appointmentsList');
    const countEl = document.getElementById('appointmentsCount');
    const lastUpdatedEl = document.getElementById('lastUpdated');
    const searchInput = document.getElementById('searchAppointments');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');

    // init relative times and counts
    const refreshUi = () => {
        const cards = Array.from(list.querySelectorAll('.appointment-card'));
        // apply relative dates
        cards.forEach(card => {
            const timeEl = card.querySelector('.relative-date');
            const date = card.dataset.date;
            const time = card.dataset.time || '';
            timeEl.textContent = relativeDateFrom(date, time);
        });
        countEl.textContent = cards.length;
        lastUpdatedEl.textContent = new Date().toLocaleTimeString();
    };
    refreshUi();

    // filtering & search
    const applyFilters = () => {
        const q = (searchInput.value || '').toLowerCase().trim();
        const st = statusFilter.value;
        let cards = Array.from(list.querySelectorAll('.appointment-card'));
        // filter
        cards.forEach(card => {
            const text = (card.textContent || '').toLowerCase();
            const status = card.dataset.status || '';
            const date = card.dataset.date || '';
            let visible = true;
            if (st !== 'all' && status !== st) visible = false;
            if (q && !(text.includes(q) || date.includes(q))) visible = false;
            card.style.display = visible ? 'flex' : 'none';
        });
        // sort visible cards
        const visibleCards = Array.from(list.querySelectorAll('.appointment-card')).filter(c => c.style.display !== 'none');
        visibleCards.sort((a,b) => {
            const ad = new Date(a.dataset.date + ' ' + (a.dataset.time || '00:00'));
            const bd = new Date(b.dataset.date + ' ' + (b.dataset.time || '00:00'));
            return sortBy.value === 'date_asc' ? ad - bd : bd - ad;
        });
        visibleCards.forEach(c => list.appendChild(c));
        document.getElementById('appointmentsCount').textContent = visibleCards.length;
    };

    searchInput.addEventListener('input', () => applyFilters());
    statusFilter.addEventListener('change', () => applyFilters());
    sortBy.addEventListener('change', () => applyFilters());

    /* Copy phone */
    const copyBtn = document.getElementById('copyPhoneBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            const phoneText = document.getElementById('patientPhoneLabel').textContent.trim();
            try {
                await navigator.clipboard.writeText(phoneText);
                toast('Phone copied to clipboard');
            } catch(e) {
                toast('Unable to copy', 'warn');
            }
        });
    }

    /* Print */
    document.getElementById('printBtn').addEventListener('click', () => {
        window.print();
    });
});

/* ==========================
   Modal (call log) interactions
   ========================== */

const openCallModal = (id, name, phone = '') => {
    const modal = document.getElementById('callModal');
    const panel = document.getElementById('callModalPanel');
    document.getElementById('callAppointmentId').value = id;
    document.getElementById('callPatientName').textContent = name + (phone ? ' • ' + phone : '');
    const telLink = document.getElementById('telLink');
    telLink.href = phone ? `tel:${phone}` : '#';
    telLink.style.display = phone ? 'inline' : 'none';
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('scale-95','opacity-0');
        panel.classList.add('scale-100','opacity-100');
        // focus first field
        document.getElementById('callResult').focus();
        trapFocus(panel);
    }, 10);
};

const closeCallModal = () => {
    const modal = document.getElementById('callModal');
    const panel = document.getElementById('callModalPanel');
    panel.classList.add('scale-95','opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        releaseFocusTrap();
        document.getElementById('callLogForm').reset();
    }, 200);
};

document.getElementById('closeCallModalBtn').addEventListener('click', closeCallModal);
document.getElementById('cancelCallBtn').addEventListener('click', closeCallModal);

// Close on Escape
document.addEventListener('keydown', (e) => {
    const modal = document.getElementById('callModal');
    if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeCallModal();
});

/* Focus trap simple */
let _trap = null;
function trapFocus(container) {
    const focusable = Array.from(container.querySelectorAll('a,button,input,select,textarea,[tabindex]:not([tabindex="-1"])')).filter(n => !n.hasAttribute('disabled'));
    if (!focusable.length) return;
    let i = 0;
    _trap = (e) => {
        if (e.key !== 'Tab') return;
        i = focusable.indexOf(document.activeElement);
        if (e.shiftKey) {
            if (i === 0) {
                e.preventDefault();
                focusable[focusable.length - 1].focus();
            }
        } else {
            if (i === focusable.length - 1) {
                e.preventDefault();
                focusable[0].focus();
            }
        }
    };
    document.addEventListener('keydown', _trap);
}
function releaseFocusTrap() {
    if (_trap) {
        document.removeEventListener('keydown', _trap);
        _trap = null;
    }
}

/* ==========================
   Submit call log (AJAX optimistic)
   ========================== */

document.getElementById('callLogForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const submitBtn = document.getElementById('saveCallBtn');
    const spinner = document.getElementById('btnSpinner');
    const btnText = submitBtn.querySelector('.btn-text');
    const originalText = btnText.textContent;
    // show loading
    spinner.classList.remove('hidden');
    btnText.textContent = 'Saving...';
    const formData = new FormData(form);
    const url = form.action;
    try {
        const res = await fetch(url, { method: 'POST', body: formData, headers: {'X-Requested-With':'XMLHttpRequest'} });
        if (!res.ok) {
            const text = await res.text();
            throw new Error(text || 'Failed to log call');
        }
        const data = await res.json().catch(()=>({}));
        toast('Call logged successfully');
        // optimistic update: update appointment status badge if returned or if result is will_attend/rescheduled
        const apptId = formData.get('appointment_id');
        const result = formData.get('result');
        const card = document.querySelector(`.appointment-card[data-id="${apptId}"]`);
        if (card) {
            // update badge & dataset
            if (result === 'will_attend') {
                card.dataset.status = 'queued';
                const badge = card.querySelector('.status-badge');
                badge.textContent = 'Queued';
                badge.className = 'status-badge inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300';
            } else if (result === 'rescheduled') {
                card.dataset.status = 'queued';
                const badge = card.querySelector('.status-badge');
                badge.textContent = 'Rescheduled';
            }
        }
        closeCallModal();
    } catch (err) {
        console.error(err);
        toast('Failed to log call', 'error');
    } finally {
        spinner.classList.add('hidden');
        btnText.textContent = originalText;
    }
});

/* ==========================
   Mark seen action (AJAX)
   ========================== */
async function markSeen(apptId) {
    if (!confirm('Mark this appointment as seen?')) return;
    try {
        // endpoint — assumes you have a route to update appointment status: /appointments/{id}/mark-seen
        const res = await fetch(`/appointments/${apptId}/mark-seen`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With':'XMLHttpRequest'}
        });
        if (!res.ok) throw new Error('Failed');
        // update UI
        const card = document.querySelector(`.appointment-card[data-id="${apptId}"]`);
        if (card) {
            card.dataset.status = 'seen';
            const badge = card.querySelector('.status-badge');
            badge.textContent = 'Seen';
            badge.className = 'status-badge inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300';
        }
        toast('Appointment marked as seen');
    } catch (err) {
        console.error(err);
        toast('Failed to update appointment', 'error');
    }
}

/* Expose openCallModal for inline onclick usage */
window.openCallModal = openCallModal;
window.markSeen = markSeen;
</script>
@endpush

@endsection
