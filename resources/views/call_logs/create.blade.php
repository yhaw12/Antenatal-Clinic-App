@extends('layouts.app')

@section('title', 'Log New Call')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 bg-app text-body flex items-center justify-center">
    <div class="w-full max-w-2xl">
        
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-body">Log Call</h1>
                <p class="text-sm text-muted mt-1">Record details of a patient interaction.</p>
            </div>
            <a href="{{ route('call_logs') }}" class="text-sm text-muted hover:text-body underline">Cancel</a>
        </div>

        <div class="card bg-surface border border-border rounded-xl shadow-sm overflow-hidden">
            
            @if($patient)
            <div class="bg-gray-50 dark:bg-white/5 px-6 py-4 border-b border-border flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-bold text-body">{{ $patient->first_name }} {{ $patient->last_name }}</h3>
                    <p class="text-xs text-muted flex gap-3">
                        <span>📞 {{ $patient->phone ?? 'No Phone' }}</span>
                        <span>ID: {{ $patient->hospital_number ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
            @endif

            <form action="{{ route('call_logs.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <input type="hidden" name="patient_id" value="{{ $patient ? $patient->id : '' }}">
                @if($appointment)
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                @endif

                @if(!$patient)
                <div>
                    <label class="block text-sm font-medium text-muted mb-1">Find Patient</label>
                    <div class="relative">
                        <input type="text" id="patientSearch" class="w-full rounded-lg bg-transparent border border-border px-4 py-2 text-body focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none" placeholder="Search name or phone..." autocomplete="off">
                        <input type="hidden" name="patient_id" id="selectedPatientId" required>
                        <div id="patientSearchResults" class="absolute top-full left-0 right-0 mt-1 bg-surface border border-border rounded-lg shadow-lg z-10 hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    @error('patient_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-muted mb-2">Call Outcome <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $outcomes = [
                                'will_attend' => ['label' => 'Will Attend', 'color' => 'peer-checked:bg-emerald-500 peer-checked:text-white border-emerald-200 text-emerald-700'],
                                'rescheduled' => ['label' => 'Rescheduled', 'color' => 'peer-checked:bg-amber-500 peer-checked:text-white border-amber-200 text-amber-700'],
                                'no_answer' => ['label' => 'No Answer', 'color' => 'peer-checked:bg-red-500 peer-checked:text-white border-red-200 text-red-700'],
                                'refused' => ['label' => 'Refused', 'color' => 'peer-checked:bg-gray-600 peer-checked:text-white border-gray-200 text-gray-700'],
                                'incorrect_number' => ['label' => 'Wrong Number', 'color' => 'peer-checked:bg-gray-600 peer-checked:text-white border-gray-200 text-gray-700'],
                            ];
                        @endphp

                        @foreach($outcomes as $value => $config)
                        <label class="cursor-pointer relative">
                            <input type="radio" name="result" value="{{ $value }}" class="peer sr-only" required {{ old('result') == $value ? 'checked' : '' }}>
                            <div class="p-3 rounded-lg border bg-surface text-center text-sm font-medium transition-all {{ $config['color'] }} hover:bg-gray-50 dark:hover:bg-white/5">
                                {{ $config['label'] }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('result') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-muted mb-1">Notes</label>
                    <textarea name="notes" rows="4" class="w-full rounded-lg bg-transparent border border-border px-4 py-2 text-body focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none" placeholder="Add details regarding the conversation...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-border">
                    <a href="{{ route('call_logs') }}" class="px-4 py-2 text-sm font-medium text-muted hover:text-body transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-brand hover:bg-brand-dark text-white text-sm font-bold rounded-lg shadow-md transition-all active:scale-95">
                        Save Log
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Simple search logic if patient not pre-selected
    const searchInput = document.getElementById('patientSearch');
    const resultsBox = document.getElementById('patientSearchResults');
    const hiddenId = document.getElementById('selectedPatientId');

    if(searchInput) {
        let timeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            const term = e.target.value.trim();
            if(term.length < 2) { resultsBox.classList.add('hidden'); return; }

            timeout = setTimeout(async () => {
                try {
                    const res = await fetch(`/dashboard/search?term=${term}`);
                    const data = await res.json();
                    
                    if(data.length === 0) {
                        resultsBox.innerHTML = '<div class="p-3 text-sm text-muted text-center">No patient found</div>';
                    } else {
                        resultsBox.innerHTML = data.map(p => `
                            <div class="p-3 hover:bg-gray-50 dark:hover:bg-white/10 cursor-pointer flex justify-between items-center group" onclick="selectPatient(${p.id}, '${p.first_name} ${p.last_name}')">
                                <div>
                                    <div class="font-bold text-sm text-body">${p.first_name} ${p.last_name}</div>
                                    <div class="text-xs text-muted">${p.phone || 'No Phone'}</div>
                                </div>
                                <div class="text-xs text-brand opacity-0 group-hover:opacity-100 font-medium">Select</div>
                            </div>
                        `).join('');
                    }
                    resultsBox.classList.remove('hidden');
                } catch(e) { console.error(e); }
            }, 300);
        });

        window.selectPatient = (id, name) => {
            hiddenId.value = id;
            searchInput.value = name;
            resultsBox.classList.add('hidden');
            searchInput.classList.add('border-brand', 'bg-brand/5');
        };

        // Close search on outside click
        document.addEventListener('click', (e) => {
            if(!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection