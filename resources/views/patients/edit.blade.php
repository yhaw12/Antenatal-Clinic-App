@extends('layouts.app')
@section('title','Edit Patient')

@section('content')
  <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-body">Edit Patient</h1>
        <p class="text-sm text-muted">Update details for {{ $patient->first_name }} {{ $patient->last_name }}</p>
      </div>
      <a href="{{ route('patients.show', $patient->id) }}" class="btn-ghost flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Patient
      </a>
    </div>

    <div class="card p-6 sm:p-8 bg-surface border-surface shadow-sm rounded-xl">
      
      {{-- Alerts --}}
      @if(session('success'))
        <div class="p-4 mb-6 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="p-4 mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
      @endif

      <form method="POST" action="{{ route('patients.update', $patient->id) }}" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Section 1: Personal Info --}}
        <div>
            <h3 class="text-lg font-medium text-body mb-4 pb-2 border-b border-border">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-muted mb-1.5">First Name <span class="text-danger">*</span></label>
                    <input id="first_name" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('first_name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-muted mb-1.5">Last Name</label>
                    <input id="last_name" name="last_name" value="{{ old('last_name', $patient->last_name) }}"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('last_name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="id_number" class="block text-sm font-medium text-muted mb-1.5">ID Number / National ID</label>
                    <input id="id_number" name="id_number" value="{{ old('id_number', $patient->id_number) }}"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('id_number') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Contact Details --}}
        <div>
            <h3 class="text-lg font-medium text-body mb-4 pb-2 border-b border-border">Contact Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-sm font-medium text-muted mb-1.5">Phone Number</label>
                    <input id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" type="tel"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    <p class="mt-1 text-xs text-muted">Format: <code>0xxxxxxxxx</code> (digits only)</p>
                    @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-muted mb-1.5">WhatsApp Number</label>
                    <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $patient->whatsapp) }}" type="tel"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('whatsapp') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-muted mb-1.5">Residential Address</label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all">{{ old('address', $patient->address) }}</textarea>
                    @error('address') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Hospital Records --}}
        <div>
            <h3 class="text-lg font-medium text-body mb-4 pb-2 border-b border-border">Hospital Records</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="hospital_number" class="block text-sm font-medium text-muted mb-1.5">Hospital / File Number</label>
                    <input id="hospital_number" name="hospital_number" value="{{ old('hospital_number', $patient->hospital_number) }}"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('hospital_number') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="folder_no" class="block text-sm font-medium text-muted mb-1.5">Legacy Folder No</label>
                    <input id="folder_no" name="folder_no" value="{{ old('folder_no', $patient->folder_no) }}"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('folder_no') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="room" class="block text-sm font-medium text-muted mb-1.5">Assigned Room</label>
                    <input id="room" name="room" value="{{ old('room', $patient->room) }}"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('room') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Next of Kin --}}
        <div>
            <h3 class="text-lg font-medium text-body mb-4 pb-2 border-b border-border">Next of Kin</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="next_of_kin_name" class="block text-sm font-medium text-muted mb-1.5">Full Name</label>
                    <input id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name', $patient->next_of_kin_name) }}"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('next_of_kin_name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="next_of_kin_phone" class="block text-sm font-medium text-muted mb-1.5">Phone Number</label>
                    <input id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $patient->next_of_kin_phone) }}" type="tel"
                           class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all" />
                    @error('next_of_kin_phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 5: Medical / Complaints --}}
        <div>
            <label for="complaints" class="block text-sm font-medium text-muted mb-1.5">Presenting Complaints / Notes</label>
            <textarea id="complaints" name="complaints" rows="3"
                      class="w-full px-4 py-2.5 border border-border rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all">{{ old('complaints', $patient->complaints) }}</textarea>
            @error('complaints') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
          <a href="{{ route('patients.show', $patient->id) }}" class="btn-ghost">Cancel</a>
          <button type="submit" class="btn-primary shadow-lg shadow-brand/20">
            Update Patient Details
          </button>
        </div>

      </form>
    </div>
  </div>
@endsection