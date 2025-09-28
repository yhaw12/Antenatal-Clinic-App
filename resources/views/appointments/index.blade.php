@extends('layouts.app')
@section('title','Appointments')
@section('page-title','Appointments')

@section('content')
<div class="flex items-center justify-between mb-4 gap-4">
  <div>
    <h2 class="text-xl font-semibold">Appointments</h2>
    <p class="text-sm text-muted">Manage upcoming patient appointments</p>
  </div>

  <div class="flex items-center gap-2">
    <!-- Theme toggle -->
    <button id="themeToggle" class="btn-ghost" aria-pressed="false" title="Toggle theme">
      {{-- <svg id="themeIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-11.66l-.707.707M4.05 19.95l-.707.707M21 12h-1M4 12H3m16.66 4.66l-.707-.707M4.05 4.05l-.707-.707" />
      </svg> --}}
      {{-- <span class="sr-only">Toggle dark mode</span> --}}
    </button>

    <a href="{{ route('appointments.create') }}" class="btn-primary" aria-label="Create new appointment">New</a>
  </div>
</div>

<!-- container -->
<div class="card p-4 bg-surface border-surface">
  <!-- accessible live region for status messages -->
  <div id="appointments-status" class="sr-only" aria-live="polite"></div>

  <!-- Responsive table (hidden on small screens) -->
  <div class="overflow-x-auto appointments-table">
    <table class="w-full text-left" role="table" aria-describedby="appointments-status">
      <thead>
        <tr class="text-sm text-muted">
          <th class="p-3">#</th>
          <th class="p-3">Patient</th>
          <th class="p-3">Date</th>
          <th class="p-3">Time</th>
          <th class="p-3">Status</th>
          <th class="p-3">Actions</th>
        </tr>
      </thead>
      <tbody id="appointments-body">
        <!-- JS will populate rows (or show skeletons while loading) -->
      </tbody>
    </table>
  </div>

  <!-- Mobile stacked cards fallback -->
  <div id="appointments-cards" class="space-y-3 appointments-cards" aria-hidden="true"></div>
</div>
@endsection

@push('scripts')
<script>
/* -----------------------
   THEME TOGGLE (persists, applies .dark to <html>)
   ----------------------- */
(function themeInit(){
  const html = document.documentElement;
  const stored = localStorage.getItem('lhims-theme');
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initial = stored || (prefersDark ? 'dark' : 'light');

  function applyTheme(mode) {
    if (mode === 'dark') html.classList.add('dark');
    else html.classList.remove('dark');
    const btn = document.getElementById('themeToggle');
    if (btn) btn.setAttribute('aria-pressed', mode === 'dark');
    localStorage.setItem('lhims-theme', mode);
  }

  applyTheme(initial);

  document.getElementById('themeToggle').addEventListener('click', () => {
    const now = html.classList.contains('dark') ? 'light' : 'dark';
    applyTheme(now);
  });
})();

/* -----------------------
   APPOINTMENTS UX: load, render, responsive cards, skeletons, error handling
   ----------------------- */
(async function appointmentsModule() {
  const body = document.getElementById('appointments-body');
  const cardsWrap = document.getElementById('appointments-cards');
  const statusRegion = document.getElementById('appointments-status');

  function showStatus(msg) {
    if (statusRegion) statusRegion.textContent = msg;
  }

  function renderSkeletons(count = 6) {
    body.innerHTML = '';
    for (let i = 0; i < count; i++) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="p-3"><div class="h-4 w-6 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-32 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-24 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-16 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-20 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-10 skeleton"></div></td>
      `;
      body.appendChild(tr);
    }
    // mobile fallback skeleton
    cardsWrap.innerHTML = '';
    for (let i = 0; i < Math.min(4, count); i++) {
      const div = document.createElement('div');
      div.className = 'p-3 border-surface border rounded';
      div.innerHTML = `<div class="h-4 w-40 skeleton mb-3"></div><div class="h-4 w-28 skeleton mb-2"></div><div class="h-4 w-20 skeleton"></div>`;
      cardsWrap.appendChild(div);
    }
    cardsWrap.setAttribute('aria-hidden', 'false');
  }

  function formatDate(dateStr) {
    if (!dateStr) return '';
    try {
      const d = new Date(dateStr);
      return new Intl.DateTimeFormat(undefined, { year: 'numeric', month: 'short', day: 'numeric' }).format(d);
    } catch (e) { return dateStr; }
  }
  function formatTime(dateStr) {
    if (!dateStr) return '';
    try {
      const d = new Date(dateStr);
      return new Intl.DateTimeFormat(undefined, { hour: 'numeric', minute: '2-digit' }).format(d);
    } catch (e) { return dateStr; }
  }

  function statusClass(status) {
    if (!status) return 'status-pending';
    status = status.toLowerCase();
    if (status.includes('confirm')) return 'status-confirmed';
    if (status.includes('complete')) return 'status-completed';
    if (status.includes('cancel')) return 'status-cancelled';
    return 'status-pending';
  }

  function renderRow(a, index) {
    const tr = document.createElement('tr');
    tr.className = 'align-top';
    const patientName = `${a.patient?.first_name ?? ''} ${a.patient?.last_name ?? ''}`.trim() || '—';
    const dateStr = a.scheduled_date ?? a.scheduled_datetime ?? null;
    const timeStr = a.scheduled_time ?? a.scheduled_datetime ?? null;

    tr.innerHTML = `
      <td class="p-3 align-top">${index + 1}</td>
      <td class="p-3 align-top">
        <div class="text-sm font-medium">${escapeHtml(patientName)}</div>
        <div class="text-xs text-muted">${escapeHtml(a.patient?.id_number || a.patient?.hospital_number || '')}</div>
      </td>
      <td class="p-3 align-top">${escapeHtml(formatDate(dateStr))}</td>
      <td class="p-3 align-top">${escapeHtml(formatTime(timeStr))}</td>
      <td class="p-3 align-top">
        <span class="status-badge ${statusClass(a.status)}">${escapeHtml(a.status ?? 'pending')}</span>
      </td>
      <td class="p-3 align-top">
        <div class="flex items-center gap-2">
          <a class="btn-ghost focus-ring" href="/appointments/${a.id}" aria-label="View appointment ${index+1}">View</a>
          <button class="btn-danger focus-ring" data-action="cancel" data-id="${a.id}" aria-label="Cancel appointment ${index+1}">Cancel</button>
        </div>
      </td>
    `;
    return tr;
  }

  function renderCard(a, index) {
    const patientName = `${a.patient?.first_name ?? ''} ${a.patient?.last_name ?? ''}`.trim() || '—';
    const card = document.createElement('article');
    card.className = 'p-3 border-surface border rounded bg-surface';
    card.innerHTML = `
      <div class="flex justify-between items-start gap-3">
        <div>
          <div class="text-sm font-semibold">${escapeHtml(patientName)}</div>
          <div class="text-xs text-muted">${escapeHtml(a.patient?.id_number || '')}</div>
        </div>
        <div class="${statusClass(a.status)} status-badge">${escapeHtml(a.status ?? 'pending')}</div>
      </div>
      <div class="mt-3 flex items-center justify-between text-sm">
        <div>
          <div class="text-muted">Date</div>
          <div>${escapeHtml(formatDate(a.scheduled_date ?? a.scheduled_datetime))}</div>
        </div>
        <div>
          <div class="text-muted">Time</div>
          <div>${escapeHtml(formatTime(a.scheduled_time ?? a.scheduled_datetime))}</div>
        </div>
      </div>
      <div class="mt-3 flex gap-2">
        <a class="btn-ghost flex-1 text-center" href="/appointments/${a.id}">View</a>
        <button class="btn-danger flex-1" data-action="cancel" data-id="${a.id}">Cancel</button>
      </div>
    `;
    return card;
  }

  // Small helper to prevent XSS; appointment content is expected from backend, but escape anyway.
  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#39;');
  }

  // wire cancel handler (delegated)
  document.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('button[data-action="cancel"]');
    if (!btn) return;
    const id = btn.dataset.id;
    if (!id) return;
    const ok = confirm('Cancel this appointment? This action cannot be undone.');
    if (!ok) return;

    btn.disabled = true;
    btn.textContent = 'Cancelling...';
    try {
      await axios.post(`/api/appointments/${id}/cancel`);
      showStatus('Appointment cancelled.');
      // re-load to refresh list
      loadAppointments();
    } catch (err) {
      console.error(err);
      alert('Failed to cancel appointment. Try again.');
      btn.disabled = false;
      btn.textContent = 'Cancel';
    }
  });

  // Main load function
  async function loadAppointments() {
    showStatus('Loading appointments...');
    renderSkeletons(6);

    try {
      const res = await axios.get('/api/appointments');
      const data = Array.isArray(res.data) ? res.data : (res.data.data || []);
      body.innerHTML = '';
      cardsWrap.innerHTML = '';

      if (data.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="6" class="p-6 text-center text-muted">No appointments found. <a href="{{ route('appointments.create') }}" class="ml-2 btn-primary">Create one</a></td>`;
        body.appendChild(tr);

        // mobile empty
        const e = document.createElement('div');
        e.className = 'p-4 text-center text-muted';
        e.innerHTML = 'No appointments found. ' + '<a href="{{ route('appointments.create') }}" class="btn-primary">Create one</a>';
        cardsWrap.appendChild(e);
        cardsWrap.setAttribute('aria-hidden', 'false');
        showStatus('No appointments.');
        return;
      }

      // Render desktop rows and mobile cards
      data.forEach((a, i) => {
        body.appendChild(renderRow(a, i));
        cardsWrap.appendChild(renderCard(a, i));
      });

      // set visible mobile container ARIA
      cardsWrap.setAttribute('aria-hidden', 'false');
      showStatus(`Loaded ${data.length} appointments.`);

    } catch (err) {
      console.error(err);
      body.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-danger">Error loading appointments. <button id="retryAppointments" class="btn-ghost">Retry</button></td></tr>`;
      cardsWrap.innerHTML = `<div class="p-4 text-center text-danger">Error loading appointments. <button id="retryAppointmentsMobile" class="btn-ghost">Retry</button></div>`;
      document.getElementById('retryAppointments')?.addEventListener('click', loadAppointments);
      document.getElementById('retryAppointmentsMobile')?.addEventListener('click', loadAppointments);
      showStatus('Error loading appointments.');
    }
  }

  // initial load
  await loadAppointments();

  // re-load when coming back from visibility change (e.g., tab hidden then visible)
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') loadAppointments();
  });

})();
</script>
@endpush
