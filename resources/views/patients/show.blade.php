@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-6">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $patient->first_name }} {{ $patient->last_name ?? '' }}
            </h1>
            <div class="flex gap-4">
                <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" aria-label="Back to all patients">
                    Back to Patients
                </a>
                <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all shadow hover:shadow-lg" aria-label="Schedule new appointment for {{ $patient->first_name }}">
                    Schedule Appointment
                </a>
            </div>
        </div>

        <!-- Patient Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                    <div class="mt-2 space-y-2">
                        <p><strong>Full Name:</strong> {{ $patient->first_name }} {{ $patient->last_name ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $patient->phone ?? 'N/A' }}</p>
                        <p><strong>Address:</strong> {{ $patient->address ?? 'N/A' }}</p>
                        <p><strong>Next of Kin:</strong> {{ $patient->next_of_kin_name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Hospital Information</h2>
                    <div class="mt-2 space-y-2">
                        <p><strong>Hospital Number:</strong> {{ $patient->hospital_number ?? 'N/A' }}</p>
                        <p><strong>ID Number:</strong> {{ $patient->id_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="h-24 w-24 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-semibold text-2xl">
                    {{ substr($patient->first_name, 0, 1) }}{{ $patient->last_name ? substr($patient->last_name, 0, 1) : '' }}
                </div>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Appointment History
            </h2>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($patient->appointments as $appointment)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    <strong>Date:</strong> {{ $appointment->date }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <strong>Time:</strong> {{ $appointment->time ? $appointment->time->format('H:i') : 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <strong>Status:</strong>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ in_array($appointment->status, ['queued', 'in_room', 'seen']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openCallModal({{ $appointment->id }}, '{{ addslashes($patient->first_name . ' ' . ($patient->last_name ?? '')) }}', '{{ $patient->phone ?? '' }}')" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-all" aria-label="Log call for appointment {{ $appointment->id }}">
                                    Call / Log
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <svg class="h-12 w-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p>No appointments found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Call Modal -->
        <div id="callModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="callModalTitle">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" role="document">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center justify-between">
                        <h3 id="callModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Log Call — <span id="callPatientName"></span></h3>
                        <button onclick="closeModal('callModal')" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all" aria-label="Close modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <form id="callLogForm" class="p-6 space-y-6" method="POST" action="{{ route('call-logs.store') }}">
                    @csrf
                    <input type="hidden" name="appointment_id" id="callAppointmentId">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Call Result <span class="text-red-500">*</span></label>
                        <select name="result" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm">
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
                        <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm" placeholder="Additional details about the call..."></textarea>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a id="telLink" href="#" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition-colors">📞 Call Again</a>
                        <div class="flex gap-3">
                            <button type="button" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" onclick="closeModal('callModal')">Cancel</button>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium shadow hover:shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all focus:ring-2 focus:ring-blue-500">Save Log</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast Container -->
        <div id="toast" class="fixed right-6 bottom-6 z-50 space-y-2"></div>
    </div>
</div>

@push('scripts')
<script>
// Toast Function
function toast(message, type = 'success') {
    const el = document.getElementById('toast');
    const id = 't' + Date.now();
    const color = type === 'error' ? 'bg-red-600 border-red-400' : type === 'warn' ? 'bg-yellow-500 border-yellow-400' : 'bg-emerald-500 border-emerald-400';
    const html = `
        <div id="${id}" class="border-l-4 ${color} bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg transform translate-y-10 opacity-0 transition-all duration-500 max-w-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'error' ? 'M6 18L18 6M6 6l12 12' : type === 'warn' ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 dark:text-white">${message}</p>
            </div>
        </div>
    `;
    el.innerHTML = html + el.innerHTML;
    el.classList.remove('hidden');
    setTimeout(() => {
        const node = document.getElementById(id);
        if (node) {
            node.classList.remove('translate-y-10', 'opacity-0');
            node.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                node.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => {
                    node.remove();
                    if (el.children.length === 0) el.classList.add('hidden');
                }, 500);
            }, 4000);
        }
    }, 100);
}

// Modal Helpers
function openModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('[role="document"]');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
        content.focus();
    }, 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('[role="document"]');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
    }, 300);
}

// Call Modal
const openCallModal = (id, name, phone = '') => {
    document.getElementById('callAppointmentId').value = id;
    document.getElementById('callPatientName').textContent = name + (phone ? ' • ' + phone : '');
    const tel = phone ? `tel:${phone}` : '#';
    const telLink = document.getElementById('telLink');
    telLink.href = tel;
    telLink.style.display = phone ? 'block' : 'none';
    openModal('callModal');
};

// Call Log Form Submission
document.getElementById('callLogForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('[type="submit"]');
    const original = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    const formData = new FormData(form);
    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        if (!res.ok) throw new Error('Failed to log call');
        toast('Call logged successfully');
        closeModal('callModal');
        form.reset();
        setTimeout(() => location.reload(), 500); // Refresh to update appointment status
    } catch (err) {
        toast('Failed to log call', 'error');
    } finally {
        submitBtn.textContent = original;
    }
});

// Close Modal on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.getElementById('callModal').classList.contains('hidden')) {
        closeModal('callModal');
    }
});
</script>
@endpush
@endsection