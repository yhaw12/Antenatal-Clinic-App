@extends('layouts.app')
@section('title','Make an Appointment')
@section('page-title','Make an Appointment')

@section('content')
  <div class="lhims-container space-y-6 bg-surface border-surface" style="background:var(--surface); border-color:var(--border);">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-body" style="color:var(--text)">Make an Appointment</h1>
        <p class="text-sm text-muted" style="color:var(--muted)">Fill the form below to register a patient and optionally schedule a review.</p>
      </div>
      <a href="{{ route('patients.index') }}" class="btn-ghost" style="color:var(--text); border-color:var(--border)">Back to list</a>
    </div>

    <div class="card p-6 bg-surface border-surface" style="background:var(--surface); border-color:var(--border); color:var(--text)">
      @if(session('success'))
        <div class="p-3 mb-4 rounded" style="background: color-mix(in srgb, var(--success) 10%, transparent); color:var(--success); font-size:0.875rem;">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="p-3 mb-4 rounded" style="background: color-mix(in srgb, var(--danger) 10%, transparent); color:var(--danger); font-size:0.875rem;">
          {{ session('error') }}
        </div>
      @endif

      <div id="ajaxErrors" class="hidden mb-4 p-3 rounded" role="alert" aria-live="assertive" style="background: color-mix(in srgb, var(--danger) 10%, transparent); color:var(--danger);"></div>

      <form id="patient-form" method="POST" action="{{ route('patients.store') }}" novalidate>
        @csrf

        @if($errors->any())
          <div class="mb-4 p-3 rounded" style="background: color-mix(in srgb, var(--danger) 10%, transparent); color:var(--danger);">
            <strong class="block mb-1" style="color:var(--text)">Please fix the following:</strong>
            <ul class="list-disc list-inside text-sm" style="color:var(--text)">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="first_name" class="block text-sm font-medium mb-2" style="color:var(--muted)">Patient Name / Full Name <span style="color:var(--danger)">*</span></label>
            <input id="first_name" name="first_name" required type="text" autocomplete="name"
                   class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                   style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                   placeholder="Enter patient full name" value="{{ old('first_name') }}" />
            <p class="mt-1 text-xs" style="color:var(--danger)">@error('first_name'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="first_name"></p>
          </div>

          <div>
            <label for="folder_no" class="block text-sm font-medium mb-2" style="color:var(--muted)">FOLDER NO.</label>
            <input id="folder_no" name="folder_no" type="text" autocomplete="off"
                   class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                   style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                   placeholder="Enter folder number (if available)" value="{{ old('folder_no') }}" />
            <p class="mt-1 text-xs" style="color:var(--danger)">@error('folder_no'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="folder_no"></p>
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium mb-2" style="color:var(--muted)">Phone Number</label>
            <input id="phone" name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{6,14}$" maxlength="15" autocomplete="tel"
                   class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                   style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                   placeholder="e.g., 02XXXXXXXX" value="{{ old('phone') }}" />
            <p class="mt-1 text-xs" style="color:var(--muted)">Must start with <code>0</code> and contain digits only.</p>
            <p class="mt-1 text-xs" style="color:var(--danger)">@error('phone'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="phone"></p>
          </div>

          <div>
            <label for="whatsapp" class="block text-sm font-medium mb-2" style="color:var(--muted)">WhatsApp</label>
            <input id="whatsapp" name="whatsapp" type="tel" inputmode="numeric" pattern="^0[0-9]{6,14}$" maxlength="15" autocomplete="tel"
                   class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                   style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                   placeholder="e.g., 02XXXXXXXX" value="{{ old('whatsapp') }}" />
            <p class="mt-1 text-xs" style="color:var(--danger)">@error('whatsapp'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="whatsapp"></p>
          </div>

          <div>
            <label for="room" class="block text-sm font-medium mb-2" style="color:var(--muted)">Room</label>
            <select id="room" name="room" class="w-32 px-3 py-2 border rounded-lg bg-app text-body transition" style="background:var(--bg); color:var(--text); border:1px solid var(--border)">
              <option value="">Select</option>
              <option value="1" {{ old('room') == '1' ? 'selected' : '' }}>1</option>
              <option value="2" {{ old('room') == '2' ? 'selected' : '' }}>2</option>
              <option value="3" {{ old('room') == '3' ? 'selected' : '' }}>3</option>
            </select>
            <p class="mt-1 text-xs" style="color:var(--danger)">@error('room'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="room"></p>
          </div>

          <div>
            <label for="next_of_kin_name" class="block text-sm font-medium mb-2" style="color:var(--muted)">Next of Kin</label>
            <input id="next_of_kin_name" name="next_of_kin_name" type="text"
                   class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                   style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                   placeholder="Emergency contact name" value="{{ old('next_of_kin_name') }}" />
            <p class="mt-1 text-xs" style="color:var(--danger)">@error('next_of_kin_name'){{ $message }}@enderror</p>
            <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="next_of_kin_name"></p>
          </div>

          <div class="md:col-span-2 grid grid-cols-2 gap-4">
            <div>
              <label for="next_of_kin_phone" class="block text-sm font-medium mb-2" style="color:var(--muted)">Kin Phone</label>
              <input id="next_of_kin_phone" name="next_of_kin_phone" type="tel" inputmode="numeric" pattern="^0[0-9]{6,14}$" maxlength="15"
                     class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                     style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                     placeholder="e.g., 07XXXXXXXX" value="{{ old('next_of_kin_phone') }}" />
              <p class="mt-1 text-xs" style="color:var(--danger)">@error('next_of_kin_phone'){{ $message }}@enderror</p>
              <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="next_of_kin_phone"></p>
            </div>

            <div>
              <label for="next_review_date" class="block text-sm font-medium mb-2" style="color:var(--muted)">Next Review Date</label>
              <input id="next_review_date" name="next_review_date" type="date"
                     class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                     style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                     value="{{ old('next_review_date', now()->toDateString()) }}" />
              <p class="mt-1 text-xs" style="color:var(--danger)">@error('next_review_date'){{ $message }}@enderror</p>
              <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="next_review_date"></p>
            </div>
          </div>
        </div>

        <div class="md:col-span-2">
          <label for="address" class="block text-sm font-medium mb-2" style="color:var(--muted)">Address</label>
          <textarea id="address" name="address" rows="2"
                    class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                    style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                    placeholder="Patient address">{{ old('address') }}</textarea>
          <p class="mt-1 text-xs" style="color:var(--danger)">@error('address'){{ $message }}@enderror</p>
        </div>

        <div class="mt-4">
          <label for="complaints" class="block text-sm font-medium mb-2" style="color:var(--muted)">Initial Complaints / Notes</label>
          <textarea id="complaints" name="complaints" rows="4"
                    class="w-full px-4 py-2 border rounded-lg bg-app text-body transition"
                    style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
                    placeholder="Describe any initial concerns or notes...">{{ old('complaints') }}</textarea>
          <p class="mt-1 text-xs" style="color:var(--danger)">@error('complaints'){{ $message }}@enderror</p>
          <p class="mt-1 text-xs" style="color:var(--danger); display:none;" data-error-for="complaints"></p>
        </div>

        <div class="flex gap-3 justify-end pt-4 border-t" style="border-top:1px solid var(--border)">
          <a href="{{ route('patients.index') }}" class="btn-ghost" style="color:var(--text); border-color:var(--border)">Cancel</a>
          <button id="patient-submit" type="submit" class="btn-primary" aria-live="polite" style="background:var(--brand); color:white;">
            <span id="patient-submit-text">Register Patient</span>
            <svg id="patient-submit-spinner" class="hidden w-4 h-4 ml-2 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" style="color:inherit;">
              <circle cx="12" cy="12" r="10" stroke-width="3" stroke-opacity="0.25"></circle>
              <path d="M22 12a10 10 0 00-10-10" stroke-width="3" stroke-linecap="round"></path>
            </svg>
          </button>
        </div>
      </form>
    </div>

    <noscript>
      <div class="p-4 text-sm text-muted" style="color:var(--muted)">JavaScript is disabled — form will submit via normal POST.</div>
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
    // use theme tokens for fallback toast
    el.style.position = 'fixed';
    el.style.bottom = '1.5rem';
    el.style.right = '1.5rem';
    el.style.padding = '0.75rem 1rem';
    el.style.borderRadius = '0.75rem';
    el.style.boxShadow = 'var(--shadow)';
    el.style.background = 'var(--surface)';
    el.style.color = 'var(--text)';
    el.style.zIndex = 9999;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), ms);
  }

  // clear client-side validation UI
  function clearValidationUI() {
    if (ajaxErrors) {
      ajaxErrors.classList.add('hidden');
      ajaxErrors.innerHTML = '';
    }
    document.querySelectorAll('[data-error-for]').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
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
        el.style.display = '';
      } else {
        top.push(`${field}: ${msgs.join(' ')}`);
      }
    });
    if (top.length && ajaxErrors) {
      ajaxErrors.innerHTML = top.map(t => `<div>${t}</div>`).join('');
      ajaxErrors.classList.remove('hidden');
      ajaxErrors.style.display = '';
    }
  }

  // attach submit handler (AJAX)
  if (form) form.addEventListener('submit', async (e) => {
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
    if (submitSpinner) submitSpinner.classList.remove('hidden');

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
        let payload = null;
        try { payload = await res.json(); } catch (err) { /* ignore */ }

        toast(payload?.message ?? 'Patient registered', 'success', 2500);

        if (payload && payload.appointment && payload.appointment.id) {
          const link = document.createElement('div');
          link.className = 'mt-3';
          link.innerHTML = `<a class="btn-ghost" href="/appointments/${payload.appointment.id}" style="color:var(--text); border-color:var(--border);">Open appointment #${payload.appointment.id}</a>`;
          form.parentNode.insertBefore(link, form.nextSibling);
        }

        if (payload && payload.redirect) {
          setTimeout(() => { window.location.href = payload.redirect; }, 700);
        } else {
          form.reset();
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
      if (submitSpinner) submitSpinner.classList.add('hidden');
    }
  });
})();
</script>
@endpush
