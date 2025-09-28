@extends('layouts.app')
@section('title','Register Patient')
@section('page-title','Register Patient')

@section('content')
  <div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-body">Register Patient</h1>
        <p class="text-sm text-muted">Quickly add a new patient or use the full register form below.</p>
      </div>

      <!-- Visible button to open the quick-register modal -->
      <div class="flex items-center gap-2">
        <button type="button" data-open-quick-register class="btn-primary" aria-haspopup="dialog" aria-controls="quickRegisterModal">
          Quick Register
        </button>

        <!-- fallback to full-page create (in case you have a separate route/view) -->
        <a href="{{ route('patients.create') }}#full-register" class="btn-ghost">Open full form</a>
      </div>
    </div>

    <!-- Optional: a small explanation or recent patients preview -->
    <div class="card p-4 bg-surface border-surface">
      <p class="text-sm text-muted">Use Quick Register for fast capture (AJAX). If you prefer a full page form, scroll down to the no-JS fallback.</p>
    </div>

    {{-- =========== Modal (your existing modal) =========== --}}
    <!-- Quick Register Modal -->
    <div id="quickRegisterModal" class="fixed inset-0 bg-black/60 hidden z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="quickRegisterTitle" aria-hidden="true">
      <div id="quickRegisterDialog"
           class="w-full max-w-2xl mx-auto bg-surface rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-200 max-h-[90vh] overflow-y-auto"
           role="document">
        <div class="sticky top-0 p-6 border-b border-surface bg-surface z-10">
          <div class="flex items-center justify-between">
            <h3 id="quickRegisterTitle" class="text-xl font-bold text-body">Quick Patient Registration</h3>
            <button type="button" id="quickRegisterClose" class="p-2 rounded-lg text-muted hover:text-body hover:bg-app transition" aria-label="Close modal">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        {{-- NOTE: form id is "patient-form" to match script below --}}
        <form id="patient-form" class="p-6 space-y-6" method="POST" action="{{ route('patients.store') }}" novalidate>
          @csrf

          <!-- global form errors (accessible) -->
          <div id="form-errors" class="hidden" role="alert" aria-live="assertive"></div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="first_name" class="block text-sm font-medium text-muted mb-2">First Name <span class="text-danger">*</span></label>
              <input id="first_name" name="first_name" required type="text" autocomplete="given-name"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                     placeholder="Enter first name" value="{{ old('first_name') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="first_name"></p>
            </div>

            <div>
              <label for="last_name" class="block text-sm font-medium text-muted mb-2">Last Name</label>
              <input id="last_name" name="last_name" type="text" autocomplete="family-name"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                     placeholder="Enter last name" value="{{ old('last_name') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="last_name"></p>
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-muted mb-2">Phone Number</label>
              <input id="phone" name="phone" type="tel" autocomplete="tel"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                     placeholder="e.g., +1 234 567 8900" value="{{ old('phone') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="phone"></p>
            </div>

            <div>
              <label for="whatsapp" class="block text-sm font-medium text-muted mb-2">WhatsApp</label>
              <input id="whatsapp" name="whatsapp" type="tel" autocomplete="tel"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                     placeholder="WhatsApp number" value="{{ old('whatsapp') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="whatsapp"></p>
            </div>

            <div>
              <label for="next_of_kin_name" class="block text-sm font-medium text-muted mb-2">Next of Kin</label>
              <input id="next_of_kin_name" name="next_of_kin_name" type="text"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                     placeholder="Emergency contact name" value="{{ old('next_of_kin_name') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="next_of_kin_name"></p>
            </div>

            <div>
              <label for="next_of_kin_phone" class="block text-sm font-medium text-muted mb-2">Kin Phone</label>
              <input id="next_of_kin_phone" name="next_of_kin_phone" type="tel"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                     placeholder="Emergency contact phone" value="{{ old('next_of_kin_phone') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="next_of_kin_phone"></p>
            </div>

            <div class="md:col-span-2">
              <label for="address" class="block text-sm font-medium text-muted mb-2">Address</label>
              <textarea id="address" name="address" rows="3"
                        class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                        placeholder="Patient address">{{ old('address') }}</textarea>
              <p class="mt-1 text-xs text-danger hidden" data-error-for="address"></p>
            </div>

            <div class="md:col-span-2">
              <label for="next_review_date" class="block text-sm font-medium text-muted mb-2">Next Review Date</label>
              <input id="next_review_date" name="next_review_date" type="date"
                     class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition" value="{{ old('next_review_date') }}" />
              <p class="mt-1 text-xs text-danger hidden" data-error-for="next_review_date"></p>
            </div>

          </div>

          <div>
            <label for="complaints" class="block text-sm font-medium text-muted mb-2">Initial Complaints / Notes</label>
            <textarea id="complaints" name="complaints" rows="4"
                      class="w-full px-4 py-3 border border-surface rounded-xl bg-app text-body focus:ring-2 focus:ring-brand transition"
                      placeholder="Describe any initial concerns or notes...">{{ old('complaints') }}</textarea>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="complaints"></p>
          </div>

          <div class="flex gap-3 justify-end pt-4 border-t border-surface">
            <button type="button" id="patient-cancel" class="btn-ghost" >Cancel</button>
            <button id="patient-submit" type="submit" class="btn-primary" aria-live="polite">
              <span id="patient-submit-text">Register Patient</span>
              <svg id="patient-submit-spinner" class="hidden w-4 h-4 ml-2 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke-width="3" stroke-opacity="0.25"></circle>
                <path d="M22 12a10 10 0 00-10-10" stroke-width="3" stroke-linecap="round"></path>
              </svg>
            </button>
          </div>
        </form>
      </div>
    </div>
    {{-- =========== end modal =========== --}}

    {{-- Full-page fallback form (visible if JS disabled or you want to do full page create) --}}
    <noscript>
      <div id="full-register" class="card p-6 mt-6 bg-surface border-surface">
        <h2 class="text-lg font-semibold text-body mb-4">Full Register (no JavaScript)</h2>
        <form method="POST" action="{{ route('patients.store') }}">
          @csrf
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-muted mb-1">First name</label>
              <input name="first_name" required class="w-full border border-surface rounded px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm text-muted mb-1">Last name</label>
              <input name="last_name" class="w-full border border-surface rounded px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm text-muted mb-1">Phone</label>
              <input name="phone" class="w-full border border-surface rounded px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm text-muted mb-1">WhatsApp</label>
              <input name="whatsapp" class="w-full border border-surface rounded px-3 py-2" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm text-muted mb-1">Address</label>
              <textarea name="address" class="w-full border border-surface rounded px-3 py-2"></textarea>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn-primary">Register</button>
          </div>
        </form>
      </div>
    </noscript>
  </div>
@endsection

@push('scripts')
<script>
  // Auto-open modal if server-side validation failed (helps the user see errors)
  document.addEventListener('DOMContentLoaded', () => {
    @if ($errors->any())
      // open modal so user sees validation errors
      if (window.openModal) window.openModal('quickRegisterModal');
    @endif
  });
</script>
@endpush
