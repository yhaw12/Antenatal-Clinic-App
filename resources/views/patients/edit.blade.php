@extends('layouts.app')
@section('title','Edit Patient')
@section('page-title','Edit Patient')

@section('content')
  <div class="lhims-container space-y-6 bg-surface border-surface">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-body">Edit Patient</h1>
        <p class="text-sm text-muted">Update the patient details below.</p>
      </div>
      <a href="{{ route('patients.show', $patient->id) }}" class="btn-ghost">Back to patient</a>
    </div>

    <div class="card p-6 bg-surface border-surface max-w-2xl mx-auto">
      @if(session('success'))
        <div class="p-3 mb-4 rounded bg-green-50 text-success text-sm">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="p-3 mb-4 rounded bg-red-50 text-danger text-sm">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('patients.update', $patient->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="first_name" class="block text-sm font-medium text-muted mb-2">First name <span class="text-danger">*</span></label>
            <input id="first_name" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            @error('first_name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="last_name" class="block text-sm font-medium text-muted mb-2">Last name</label>
            <input id="last_name" name="last_name" value="{{ old('last_name', $patient->last_name) }}"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            @error('last_name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-muted mb-2">Phone</label>
            <input id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" type="tel"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            <p class="mt-1 text-xs text-muted">Start with <code>0</code>, digits only.</p>
            @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="whatsapp" class="block text-sm font-medium text-muted mb-2">WhatsApp</label>
            <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $patient->whatsapp) }}" type="tel"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            @error('whatsapp') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>

          <div class="md:col-span-2">
            <label for="address" class="block text-sm font-medium text-muted mb-2">Address</label>
            <input id="address" name="address" value="{{ old('address', $patient->address) }}"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            @error('address') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="next_of_kin_name" class="block text-sm font-medium text-muted mb-2">Next of kin</label>
            <input id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name', $patient->next_of_kin_name) }}"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            @error('next_of_kin_name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="next_of_kin_phone" class="block text-sm font-medium text-muted mb-2">Next of kin phone</label>
            <input id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $patient->next_of_kin_phone) }}" type="tel"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition" />
            @error('next_of_kin_phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="flex gap-3 justify-end pt-4 border-t border-surface">
          <a href="{{ route('patients.show', $patient->id) }}" class="btn-ghost">Cancel</a>
          <button type="submit" class="btn-primary">
            Save changes
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
