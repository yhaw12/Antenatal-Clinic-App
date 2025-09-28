@extends('layouts.app')
@section('title','Make an Appointment')
@section('page-title','Make an Appointment')

@section('content')
  <div class="lhims-container space-y-6 bg-surface border-surface">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-body">Make an Appointment</h1>
        <p class="text-sm text-muted">Fill the form below to register a patient and optionally schedule a review.</p>
      </div>
      <a href="{{ route('patients.index') }}" class="btn-ghost">Back to list</a>
    </div>

    <div class="card p-6 bg-surface border-surface">
      @if(session('success'))
        <div class="p-3 mb-4 rounded bg-green-50 text-success text-sm">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="p-3 mb-4 rounded bg-red-50 text-danger text-sm">{{ session('error') }}</div>
      @endif

      <div id="ajaxErrors" class="hidden mb-4 p-3 rounded bg-red-50 text-danger" role="alert" aria-live="assertive"></div>

      <form id="patient-form" method="POST" action="{{ route('patients.store') }}" novalidate>
        @csrf

        @if($errors->any())
          <div class="mb-4 p-3 rounded bg-red-50 text-danger">
            <strong class="block mb-1">Please fix the following:</strong>
            <ul class="list-disc list-inside text-sm">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="first_name" class="block text-sm font-medium text-muted mb-2">Patient Name / Full Name <span class="text-danger">*</span></label>
            <input id="first_name" name="first_name" required type="text" autocomplete="name"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                   placeholder="Enter patient full name" value="{{ old('first_name') }}" />
            <p class="mt-1 text-xs text-danger">@error('first_name'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="first_name"></p>
          </div>

          <div>
            <label for="folder_no" class="block text-sm font-medium text-muted mb-2">FOLDER NO.</label>
            <input id="folder_no" name="folder_no" type="text" autocomplete="off"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                   placeholder="Enter folder number (if available)" value="{{ old('folder_no') }}" />
            <p class="mt-1 text-xs text-danger">@error('folder_no'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="folder_no"></p>
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-muted mb-2">Phone Number</label>
            <input id="phone" name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{6,14}$" maxlength="15" autocomplete="tel"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                   placeholder="e.g., 02XXXXXXXX" value="{{ old('phone') }}" />
            <p class="mt-1 text-xs text-muted">Must start with <code>0</code> and contain digits only.</p>
            <p class="mt-1 text-xs text-danger">@error('phone'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="phone"></p>
          </div>

          <div>
            <label for="whatsapp" class="block text-sm font-medium text-muted mb-2">WhatsApp</label>
            <input id="whatsapp" name="whatsapp" type="tel" inputmode="numeric" pattern="^0[0-9]{6,14}$" maxlength="15" autocomplete="tel"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                   placeholder="e.g., 02XXXXXXXX" value="{{ old('whatsapp') }}" />
            <p class="mt-1 text-xs text-danger">@error('whatsapp'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="whatsapp"></p>
          </div>

          <div>
            <label for="room" class="block text-sm font-medium text-muted mb-2">Room</label>
            <select id="room" name="room" class="w-32 px-3 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition">
              <option value="">Select</option>
              <option value="1" {{ old('room') == '1' ? 'selected' : '' }}>1</option>
              <option value="2" {{ old('room') == '2' ? 'selected' : '' }}>2</option>
              <option value="3" {{ old('room') == '3' ? 'selected' : '' }}>3</option>
            </select>
            <p class="mt-1 text-xs text-danger">@error('room'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="room"></p>
          </div>

          <div>
            <label for="next_of_kin_name" class="block text-sm font-medium text-muted mb-2">Next of Kin</label>
            <input id="next_of_kin_name" name="next_of_kin_name" type="text"
                   class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                   placeholder="Emergency contact name" value="{{ old('next_of_kin_name') }}" />
            <p class="mt-1 text-xs text-danger">@error('next_of_kin_name'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs text-danger hidden" data-error-for="next_of_kin_name"></p>
          </div>

          <div class="md:col-span-2 grid grid-cols-2 gap-4">
            <div>
              <label for="next_of_kin_phone" class="block text-sm font-medium text-muted mb-2">Kin Phone</label>
              <input id="next_of_kin_phone" name="next_of_kin_phone" type="tel" inputmode="numeric" pattern="^0[0-9]{6,14}$" maxlength="15"
                     class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                     placeholder="e.g., 07XXXXXXXX" value="{{ old('next_of_kin_phone') }}" />
              <p class="mt-1 text-xs text-danger">@error('next_of_kin_phone'){{ $message }}@enderror</p>
              <p class="mt-1 text-xs text-danger hidden" data-error-for="next_of_kin_phone"></p>
            </div>

            <div>
              <label for="next_review_date" class="block text-sm font-medium text-muted mb-2">Next Review Date</label>
              <input id="next_review_date" name="next_review_date" type="date"
                     class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                     value="{{ old('next_review_date', now()->toDateString()) }}" />
              <p class="mt-1 text-xs text-danger">@error('next_review_date'){{ $message }}@enderror</p>
              <p class="mt-1 text-xs text-danger hidden" data-error-for="next_review_date"></p>
            </div>
          </div>
        </div>

        <div class="md:col-span-2">
          <label for="address" class="block text-sm font-medium text-muted mb-2">Address</label>
          <textarea id="address" name="address" rows="2" /* Increased from 1 for better UX */
                    class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                    placeholder="Patient address">{{ old('address') }}</textarea>
          <p class="mt-1 text-xs text-danger">@error('address'){{ $message }}@enderror</p>
        </div>

        <div class="mt-4">
          <label for="complaints" class="block text-sm font-medium text-muted mb-2">Initial Complaints / Notes</label>
          <textarea id="complaints" name="complaints" rows="4"
                    class="w-full px-4 py-2 border border-surface rounded-lg bg-app text-body focus:ring-2 focus:ring-brand focus:outline-none transition"
                    placeholder="Describe any initial concerns or notes...">{{ old('complaints') }}</textarea>
          <p class="mt-1 text-xs text-danger">@error('complaints'){{ $message }}@enderror</p>
          <p class="mt-1 text-xs text-danger hidden" data-error-for="complaints"></p>
        </div>

        <div class="flex gap-3 justify-end pt-4 border-t border-surface">
          <a href="{{ route('patients.index') }}" class="btn-ghost">Cancel</a>
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

    <noscript>
      <div class="p-4 text-sm text-muted">JavaScript is disabled — form will submit via normal POST.</div>
    </noscript>
  </div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  // element refs
  const form = document.getElementById('patient-form');
  const submitBtn = document.getElementById('patient-submit');
  const submitText = document.getElementById('patient-submit-text');
  const submitSpinner = document.getElementById('patient-submit-spinner');
  const ajaxErrors = document.getElementById('ajaxErrors');

  // utility: show a light toast (uses window.toast if available)
  function toast(msg, type = 'success', ms = 3000) {
    if (typeof window.toast === 'function') return window.toast(msg, type, ms);
    const el = document.createElement('div');
    el.textContent = msg;
    el.className = 'fixed bottom-6 right-6 p-3 rounded shadow bg-surface text-body';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), ms);
  }

  // clear client-side validation UI
  function clearValidationUI() {
    ajaxErrors.classList.add('hidden');
    ajaxErrors.innerHTML = '';
    document.querySelectorAll('[data-error-for]').forEach(el => {
      el.textContent = '';
      el.classList.add('hidden');
    });
  }

  // show validation errors from Laravel (422)
  function showValidationErrors(errors = {}) {
    clearValidationUI();
    const top = [];
    Object.keys(errors).forEach(field => {
      const msgs = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
      const el = document.querySelector(`[data-error-for="${field}"]`);
      if (el) {
        el.textContent = msgs.join(' ');
        el.classList.remove('hidden');
      } else {
        top.push(`${field}: ${msgs.join(' ')}`);
      }
    });
    if (top.length) {
      ajaxErrors.innerHTML = top.map(t => `<div>${t}</div>`).join('');
      ajaxErrors.classList.remove('hidden');
    }
  }

  // attach submit handler (AJAX)
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearValidationUI();

    // client-side quick validation: check phone patterns (starts with 0)
    const phone = form.querySelector('[name="phone"]')?.value?.trim();
    const whatsapp = form.querySelector('[name="whatsapp"]')?.value?.trim();
    const kinPhone = form.querySelector('[name="next_of_kin_phone"]')?.value?.trim();
    const phoneRegex = /^0[0-9]{6,14}$/;
    if (phone && !phoneRegex.test(phone)) {
      showValidationErrors({ phone: ['Phone must start with 0 and be digits only (7–15 digits).'] });
      return;
    }
    if (whatsapp && !phoneRegex.test(whatsapp)) {
      showValidationErrors({ whatsapp: ['WhatsApp must start with 0 and be digits only (7–15 digits).'] });
      return;
    }
    if (kinPhone && !phoneRegex.test(kinPhone)) {
      showValidationErrors({ next_of_kin_phone: ['Kin phone must start with 0 and be digits only (7–15 digits).'] });
      return;
    }

    submitBtn.disabled = true;
    submitText.textContent = 'Registering...';
    submitSpinner.classList.remove('hidden');

    const action = form.action;
    const fd = new FormData(form);

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: fd
      });

      if (res.status === 422) {
        const payload = await res.json().catch(() => ({}));
        showValidationErrors(payload.errors || payload || {});
        toast('Please fix the errors and try again.', 'warn', 5000);
      } else if (res.ok) {
        // success — parse json if returned (AJAX flow)
        let payload = null;
        try { payload = await res.json(); } catch (err) { /* ignore */ }

        // show success & optionally link to created appointment
        toast(payload?.message ?? 'Patient registered', 'success', 2500);

        // if server returned appointment object, show quick link (no full reload)
        if (payload && payload.appointment && payload.appointment.id) {
          const link = document.createElement('div');
          link.className = 'mt-3';
          link.innerHTML = `<a class="btn-ghost" href="/appointments/${payload.appointment.id}">Open appointment #${payload.appointment.id}</a>`;
          // insert after form
          form.parentNode.insertBefore(link, form.nextSibling);
        }

        // optionally redirect if server says so
        if (payload && payload.redirect) {
          setTimeout(() => { window.location.href = payload.redirect; }, 700);
        } else {
          // reset the form so user can add another quickly
          form.reset();
          // set date inputs back to today (if any)
          const nextReview = document.getElementById('next_review_date');
          if (nextReview) nextReview.value = new Date().toISOString().slice(0,10);
        }
      } else {
        const text = await res.text().catch(()=> '');
        console.error('Server error', res.status, text);
        toast('Error saving patient. See console for details.', 'error', 5000);
      }
    } catch (err) {
      console.error('Network error', err);
      toast('Network error while saving patient', 'error', 5000);
    } finally {
      submitBtn.disabled = false;
      submitText.textContent = 'Register Patient';
      submitSpinner.classList.add('hidden');
    }
  });
})();
</script>
@endpush
