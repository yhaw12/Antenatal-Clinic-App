@extends('layouts.app')

@section('title', 'Daily Queue')

@section('content')
<div class="min-h-screen" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 3%, transparent));">
  <div class="max-w-7xl mx-auto p-6">
    <div class="glass-card rounded-3xl overflow-hidden shadow-2xl card-hover" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
      <!-- Header -->
      <div class="px-6 py-4 border-b" style="background: linear-gradient(90deg, color-mix(in srgb, var(--surface) 92%, transparent), color-mix(in srgb, var(--bg) 92%, transparent)); border-bottom:1px solid var(--border); backdrop-filter: blur(6px);">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" aria-hidden="true" style="background: linear-gradient(90deg, var(--brand), var(--accent)); color:#fff;">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold" style="color:var(--text)">Daily Queue</h3>
              <p class="text-sm" id="queueSubtitle" style="color:var(--muted)">{{ $daily->count() }} appointments scheduled</p>
            </div>
          </div>

          <div class="flex items-center space-x-3">
            <!-- Date filter -->
            <div class="flex items-center space-x-2">
              <label for="dateFilter" class="text-sm" style="color:var(--muted)">Date</label>
              <input id="dateFilter" name="date" type="date" value="{{ request('date', \Illuminate\Support\Carbon::today()->toDateString()) }}" class="px-3 py-2 rounded-xl text-sm focus:ring-2" style="background:var(--bg); color:var(--text); border:1px solid var(--border);">
            </div>

            <!-- Search -->
            <div class="relative" style="width:24rem">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--muted);">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input id="globalSearch" class="z-50 w-full pl-12 pr-12 py-3 rounded-2xl text-sm" placeholder="Search patients, appointments... (Press / to focus)" type="text" autocomplete="off" style="background:var(--bg); color:var(--text); border:1px solid var(--border);">
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <kbd class="px-2 py-1 text-xs font-medium rounded" style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--muted); border:1px solid var(--border);">/</kbd>
              </div>

              <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 rounded-2xl hidden max-h-72 overflow-y-auto z-50" style="background:var(--surface); border:1px solid var(--border); backdrop-filter: blur(8px);">
                <div class="p-4 text-sm" style="color:var(--muted)">Type to search patients or appointments for the selected date...</div>
              </div>
            </div>

            <!-- Status filter & refresh -->
            <select id="statusFilter" class="px-3 py-2 rounded-xl text-sm focus:ring-2" style="background:var(--bg); color:var(--text); border:1px solid var(--border);">
              <option value="">All Status</option>
              <option value="present">Present</option>
              <option value="scheduled">Scheduled</option>
              <option value="missed">Missed</option>
            </select>

            <button id="refreshQueue" class="p-2 rounded-xl transition-all" title="Refresh queue" style="background: var(--surface); border:1px solid var(--border); color:var(--muted);">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:inherit"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions Bar -->
      <div class="px-6 py-3 border-b" style="background: linear-gradient(90deg, color-mix(in srgb, var(--surface) 92%, transparent), color-mix(in srgb, var(--bg) 92%, transparent)); border-bottom:1px solid var(--border); backdrop-filter: blur(6px);">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <label class="flex items-center space-x-2" style="color:var(--muted)">
              <input type="checkbox" id="selectAll" class="w-4 h-4" style="accent-color:var(--brand); border:1px solid var(--border);">
              <span class="text-sm font-medium">Select All</span>
            </label>

            <div id="bulkActions" class="flex items-center space-x-2 opacity-0 transition-opacity duration-200">
              <button id="bulkMarkPresent" class="px-3 py-1.5 rounded-lg text-sm" style="background:var(--success); color:#fff;">Mark Present</button>
              <button id="bulkMarkAbsent" class="px-3 py-1.5 rounded-lg text-sm" style="background:var(--danger); color:#fff;">Mark Absent</button>
            </div>
          </div>

          <span class="text-sm" id="selectionCount" style="color:var(--muted)">0 selected</span>
        </div>
      </div>

      <!-- Queue List -->
      <div class="max-h-[calc(100vh-300px)] overflow-y-auto" id="queueList">
        @forelse($daily as $appt)
          @php
            $patient = $appt->patient;
            $status = $appt->status ?? 'scheduled';
            $time = $appt->time ? \Illuminate\Support\Carbon::parse($appt->time)->format('h:i A') : '-';
            $initials = trim(ucfirst(substr($patient->first_name ?? '',0,1)) . ucfirst(substr($patient->last_name ?? '',0,1)));
          @endphp

          <div class="queue-item px-6 py-4 border-b transition-all" style="border-color:var(--border)" data-status="{{ $status }}" data-appointment-id="{{ $appt->id }}">
            <div class="flex items-center space-x-4">
              <input type="checkbox" class="queue-checkbox w-4 h-4" data-appointment-id="{{ $appt->id }}" style="accent-color:var(--brand);">
              <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold"
                   style="background:linear-gradient(135deg, var(--brand), var(--accent));">
                {{ $initials ?: 'P' }}
              </div>

              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <div>
                    <h4 class="font-semibold" style="color:var(--text)">{{ $patient->first_name ?? 'Unknown' }} {{ $patient->last_name ?? '' }}</h4>
                    <p class="text-sm" style="color:var(--muted)">{{ $patient->address ?? '—' }}</p>
                    <p class="text-xs" style="color:var(--muted)">Phone: {{ $patient->phone ?? '—' }} • Next of Kin: {{ $patient->next_of_kin_name ?? '—' }}</p>
                  </div>

                  <div class="text-right">
                    <p class="text-sm" style="color:var(--muted)">{{ $time }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-badge" style="
                      background: {{ $status === 'present' ? 'color-mix(in srgb, var(--success) 10%, transparent)' : ($status === 'missed' ? 'color-mix(in srgb, var(--danger) 10%, transparent)' : 'color-mix(in srgb, var(--brand) 10%, transparent)') }};
                      color: {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }};
                      border:1px solid color-mix(in srgb, {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }} 20%, transparent);">
                      @if($status === 'present')
                        <span class="w-2 h-2 rounded-full mr-1.5" style="background:var(--success);"></span>
                        Present
                      @elseif($status === 'missed')
                        <span class="w-2 h-2 rounded-full mr-1.5" style="background:var(--danger);"></span>
                        Missed
                      @else
                        <span class="w-2 h-2 rounded-full mr-1.5" style="background:var(--brand);"></span>
                        Scheduled
                      @endif
                    </span>
                  </div>
                </div>

                <div class="flex items-center space-x-2 mt-3">
                  <button class="mark-present single-action px-3 py-1.5 rounded-lg text-sm" data-appt-id="{{ $appt->id }}" style="background:var(--success); color:#fff;">Mark Present</button>

                  <button class="single-absent px-3 py-1.5 rounded-lg text-sm" data-appt-id="{{ $appt->id }}" style="background:var(--danger); color:#fff;">Mark Absent</button>

                  <a href="{{ route('patients.show', optional($patient)->id) }}" class="px-3 py-1.5 rounded-lg text-sm" style="background: color-mix(in srgb, var(--surface) 96%, transparent); border:1px solid var(--border); color:var(--text);">View</a>

                  <button class="px-3 py-1.5 rounded-lg text-sm" data-action="call-now" data-name="{{ $patient->first_name ?? 'Patient' }}" style="background: color-mix(in srgb, var(--accent) 60%, transparent); color:#fff;">Call Now</button>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="p-12 text-center" id="emptyQueue">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: color-mix(in srgb, var(--surface) 90%, transparent);">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color:var(--text)">No appointments found</h3>
            <p style="color:var(--muted)" class="mb-6">Try adjusting your filters or the date</p>
            <a href="{{ route('appointments.create') }}" class="px-4 py-2 rounded-lg" style="background:var(--brand); color:#fff;">Create Appointment</a>
          </div>
        @endforelse
      </div>

      <!-- Floating Action Button (kept) -->
      <div class="fixed bottom-6 right-6 z-50">
        <button id="fabButton" class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center floating-action" style="background: linear-gradient(90deg, var(--brand), var(--accent)); color:#fff;">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Call Modal (unchanged visually but uses tokens) -->
  <div id="callModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="callModalTitle">
    <div class="absolute inset-0" onclick="closeModal('callModal')" aria-hidden="true" style="background:rgba(0,0,0,0.6); backdrop-filter: blur(6px)"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="rounded-3xl max-w-md w-full overflow-hidden transform transition-all" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
        <div class="p-6" style="border-bottom:1px solid var(--border); background: linear-gradient(90deg, color-mix(in srgb, var(--surface) 90%, transparent), color-mix(in srgb, var(--bg) 90%, transparent));">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--brand), var(--accent)); color:#fff;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </div>
              <div>
                <h3 id="callModalTitle" class="text-lg font-bold" style="color:var(--text)">Call Patient</h3>
                <p class="text-sm" id="callPatientName" style="color:var(--muted)">Patient</p>
              </div>
            </div>
            <button onclick="closeModal('callModal')" class="p-2 rounded-lg transition-colors" aria-label="Close call modal" style="color:var(--muted);">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6">
          <p class="text-sm" style="color:var(--muted)">Call actions for <strong id="callPatientNameBody" style="color:var(--text)">Patient</strong></p>
          <div class="mt-4 flex gap-3">
            <button class="px-4 py-2 rounded-lg" id="callConfirmBtn" style="background:var(--success); color:#fff;">Call</button>
            <button class="px-4 py-2 rounded-lg" onclick="closeModal('callModal')" style="background: var(--bg); border:1px solid var(--border); color:var(--text);">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')

<script>
(() => {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const csrf = '{{ csrf_token() }}';
  const markPresentUrl = '{{ route("daily-queue.mark-present") }}';
  const markAbsentUrl  = '{{ route("daily-queue.mark-absent") }}';
  const patientSearchUrl = '{{ url("daily-queue/search") }}';

  // Toast helper (non-blocking)
  function toast(message, type = 'info', ms = 4000) {
    const container = document.getElementById('toasts');
    if (!container) return;
    const el = document.createElement('div');
    el.className = 'max-w-sm px-4 py-2 rounded-xl shadow-lg border flex items-center gap-3';
    el.classList.add(type === 'error' ? 'bg-red-50 text-red-800 border-red-200' : type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-700');
    el.innerHTML = `<div class="flex-1 text-sm">${message}</div>
                    <button aria-label="Dismiss" class="ml-2 text-xs opacity-70">Dismiss</button>`;
    container.appendChild(el);
    const btn = el.querySelector('button');
    btn.addEventListener('click', () => el.remove());
    setTimeout(() => el.remove(), ms);
  }

  // Small spinner element
  function spinnerHTML(size = 4) {
    return `<svg class="animate-spin w-${size} h-${size} inline-block" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke-width="3" stroke-opacity="0.25"/><path d="M22 12a10 10 0 00-10-10" stroke-width="3" stroke-linecap="round"/></svg>`;
  }

  // keyboard to focus search input
  const searchInput = $('#globalSearch');
  if (searchInput) {
    window.addEventListener('keydown', (e) => {
      if (e.key === '/' && document.activeElement !== searchInput && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) {
        e.preventDefault();
        searchInput.focus();
        searchInput.select();
      }
    });
  }

  // date change -> reload with query param
  const dateFilter = $('#dateFilter');
  if (dateFilter) {
    dateFilter.addEventListener('change', () => {
      const d = dateFilter.value;
      const url = new URL(window.location.href);
      if (d) url.searchParams.set('date', d);
      else url.searchParams.delete('date');
      window.location.href = url.toString();
    });
  }

  // status filter
  const statusFilter = $('#statusFilter'), queueList = $('#queueList'), emptyQueue = $('#emptyQueue');
  function applyStatusFilter() {
    if (!statusFilter || !queueList) return;
    const val = statusFilter.value;
    const items = $$('.queue-item', queueList);
    let visible = 0;
    items.forEach(it => {
      const s = it.dataset.status || '';
      const hide = val && val !== '' && s !== val;
      it.classList.toggle('hidden', hide);
      if (!hide) visible++;
    });
    if (emptyQueue) emptyQueue.classList.toggle('hidden', visible > 0);
  }
  statusFilter?.addEventListener('change', applyStatusFilter);

  // refresh button visual feedback
  $('#refreshQueue')?.addEventListener('click', (e) => {
    const icon = e.currentTarget.querySelector('svg');
    e.currentTarget.classList.add('opacity-80','pointer-events-none');
    icon?.classList.add('animate-spin');
    setTimeout(() => {
      e.currentTarget.classList.remove('opacity-80','pointer-events-none');
      icon?.classList.remove('animate-spin');
      toast('Queue refreshed', 'success', 1600);
    }, 700);
  });

  // Selection + bulk UI
  const selectAll = $('#selectAll'), bulkActions = $('#bulkActions'), selectionCountEl = $('#selectionCount');
  function updateBulkUI() {
    const checkedEls = $$('.queue-checkbox:checked', queueList);
    const ids = checkedEls.map(c => c.dataset.appointmentId).filter(Boolean);
    const count = ids.length;
    selectionCountEl.textContent = `${count} selected`;
    if (bulkActions) {
      bulkActions.classList.toggle('opacity-0', count === 0);
      bulkActions.classList.toggle('pointer-events-none', count === 0);
    }
    return ids;
  }
  selectAll?.addEventListener('change', (e) => {
    const checked = e.target.checked;
    $$('.queue-checkbox', queueList).forEach(cb => { cb.checked = checked; });
    updateBulkUI();
  });
  queueList.addEventListener('change', (e) => {
    if (e.target.matches('.queue-checkbox')) updateBulkUI();
  });

  // POST JSON helper with disabled state and error handling
  async function postJson(url, payload, { disableEl=null }={}) {
    if (disableEl) {
      disableEl.dataset.orig = disableEl.innerHTML;
      disableEl.innerHTML = spinnerHTML(4) + ' Working';
      disableEl.setAttribute('aria-busy','true');
      disableEl.disabled = true;
    }
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf, 'Accept':'application/json'},
        body: JSON.stringify(payload)
      });
      const text = await res.text();
      let json = {};
      try { json = JSON.parse(text || '{}'); } catch (_) { json = { ok: res.ok, text }; }
      if (!res.ok) throw json;
      return json;
    } finally {
      if (disableEl) {
        disableEl.innerHTML = disableEl.dataset.orig ?? disableEl.innerHTML;
        disableEl.removeAttribute('aria-busy');
        disableEl.disabled = false;
      }
    }
  }

  // row UI update and animation
  function setRowStatusAndMove(row, status) {
    if (!row) return;
    row.dataset.status = status;
    const badge = row.querySelector('.status-badge');
    if (badge) {
      if (status === 'present') {
        badge.innerHTML = '<span class="w-2 h-2 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>Present';
      } else if (status === 'missed' || status === 'absent') {
        badge.innerHTML = '<span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span>Missed';
      } else {
        badge.innerHTML = '<span class="w-2 h-2 bg-blue-400 rounded-full mr-1.5"></span>Scheduled';
      }
    }
    // move marked items to bottom (so unmarked remain on top)
    if (row.parentNode) {
      row.parentNode.appendChild(row);
      row.classList.add('opacity-80','translate-y-1');
      setTimeout(() => row.classList.remove('opacity-80','translate-y-1'), 700);
    }
  }

  function isMarkedStatus(s) {
    return ['present','missed','absent'].includes(String(s || '').toLowerCase());
  }

  // SINGLE mark present (delegated)
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.mark-present');
    if (!btn) return;
    const apptId = btn.dataset.apptId;
    if (!apptId) return;
    const row = document.querySelector(`.queue-item[data-appointment-id="${apptId}"]`);
    const current = row?.dataset.status || '';

    if (isMarkedStatus(current) && current !== 'present') {
      if (!confirm(`Overwrite current status "${current}" and mark as PRESENT?`)) return;
    }

    try {
      const resp = await postJson(markPresentUrl, { appointment_ids: [parseInt(apptId,10)] }, { disableEl: btn });
      if (resp && resp.success) {
        setRowStatusAndMove(row, 'present');
        toast('Marked present', 'success', 2400);
      } else {
        toast(resp.message ?? 'Could not mark present', 'error', 4000);
      }
    } catch (err) {
      console.error(err);
      toast('Network/server error marking present', 'error', 4000);
    } finally {
      applyStatusFilter();
      updateBulkUI();
    }
  });

  // SINGLE mark absent
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.single-absent');
    if (!btn) return;
    const apptId = btn.dataset.apptId;
    if (!apptId) return;
    const row = document.querySelector(`.queue-item[data-appointment-id="${apptId}"]`);
    const current = row?.dataset.status || '';

    if (isMarkedStatus(current) && !['missed','absent'].includes(current)) {
      if (!confirm(`Overwrite current status "${current}" and mark as ABSENT/MISSED?`)) return;
    }

    try {
      const resp = await postJson(markAbsentUrl, { appointment_ids: [parseInt(apptId,10)] }, { disableEl: btn });
      if (resp && resp.success) {
        setRowStatusAndMove(row, 'missed');
        toast('Marked absent/missed', 'success', 2400);
      } else {
        toast(resp.message ?? 'Could not mark absent', 'error', 4000);
      }
    } catch (err) {
      console.error(err);
      toast('Network/server error marking absent', 'error', 4000);
    } finally {
      applyStatusFilter();
      updateBulkUI();
    }
  });

  // BULK mark present
  $('#bulkMarkPresent')?.addEventListener('click', async (e) => {
    const ids = updateBulkUI();
    if (!ids.length) return toast('No appointments selected', 'info', 2200);

    const overwrite = ids.map(id => document.querySelector(`.queue-item[data-appointment-id="${id}"]`))
                         .filter(r => r && isMarkedStatus(r.dataset.status) && r.dataset.status !== 'present').length;

    if (overwrite) {
      if (!confirm(`${overwrite} selected already marked. Overwrite and mark ${ids.length} as PRESENT?`)) return;
    } else if (!confirm(`Mark ${ids.length} appointment(s) as PRESENT?`)) return;

    try {
      const resp = await postJson(markPresentUrl, { appointment_ids: ids.map(i => parseInt(i,10)) }, { disableEl: e.currentTarget });
      if (resp && resp.success) {
        ids.forEach(id => setRowStatusAndMove(document.querySelector(`.queue-item[data-appointment-id="${id}"]`), 'present'));
        toast(`Marked ${ids.length} appointment(s) present`, 'success', 3000);
      } else {
        toast(resp.message ?? 'Bulk present failed', 'error', 4000);
      }
    } catch (err) {
      console.error(err);
      toast('Network/server error during bulk present', 'error', 4000);
    } finally {
      applyStatusFilter(); updateBulkUI();
    }
  });

  // BULK mark absent
  $('#bulkMarkAbsent')?.addEventListener('click', async (e) => {
    const ids = updateBulkUI();
    if (!ids.length) return toast('No appointments selected', 'info', 2200);

    const overwrite = ids.map(id => document.querySelector(`.queue-item[data-appointment-id="${id}"]`))
                         .filter(r => r && isMarkedStatus(r.dataset.status) && !['missed','absent'].includes(r.dataset.status)).length;

    if (overwrite) {
      if (!confirm(`${overwrite} selected already marked. Overwrite and mark ${ids.length} as ABSENT/MISSED?`)) return;
    } else if (!confirm(`Mark ${ids.length} appointment(s) as ABSENT/MISSED?`)) return;

    try {
      const resp = await postJson(markAbsentUrl, { appointment_ids: ids.map(i => parseInt(i,10)) }, { disableEl: e.currentTarget });
      if (resp && resp.success) {
        ids.forEach(id => setRowStatusAndMove(document.querySelector(`.queue-item[data-appointment-id="${id}"]`), 'missed'));
        toast(`Marked ${ids.length} appointment(s) absent/missed`, 'success', 3000);
      } else {
        toast(resp.message ?? 'Bulk absent failed', 'error', 4000);
      }
    } catch (err) {
      console.error(err);
      toast('Network/server error during bulk absent', 'error', 4000);
    } finally {
      applyStatusFilter(); updateBulkUI();
    }
  });

  // ======= SEARCH (only this block changed) =======
  // Debounced search that avoids clipping by positioning the results as fixed overlay
  (function initSearch() {
    if (!searchInput) return;
    const resultsBox = $('#searchResults');
    if (!resultsBox) return;

    let searchTimer = null;

    // small HTML escape helper
    function escapeHtml(s) {
      if (!s && s !== 0) return '';
      return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,"&#039;");
    }

    // position the results box as fixed so ancestor overflow/transform won't clip it
    function positionResultsFixed() {
      if (!resultsBox || !searchInput) return;
      const rect = searchInput.getBoundingClientRect();
      resultsBox.style.position = 'fixed';
      resultsBox.style.left = rect.left + 'px';
      resultsBox.style.top = (rect.bottom + 6) + 'px';
      resultsBox.style.width = rect.width + 'px';
      resultsBox.style.maxHeight = '320px';
      resultsBox.style.overflowY = 'auto';
      resultsBox.style.zIndex = '10099';
    }

    // restore to original inline/absolute style (when hidden)
    function restoreResultsStyle() {
      if (!resultsBox) return;
      resultsBox.style.position = '';
      resultsBox.style.left = '';
      resultsBox.style.top = '';
      resultsBox.style.width = '';
      resultsBox.style.maxHeight = '';
      resultsBox.style.overflowY = '';
      resultsBox.style.zIndex = '';
    }

    function renderItems(items) {
      if (!items || !items.length) {
        resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">No results</div>`;
        positionResultsFixed();
        return;
      }

      const html = items.map(item => {
        const label = escapeHtml(item.label || ((item.first_name || '') + ' ' + (item.last_name || '')).trim() || 'Unknown');
        const sub = item.appointment_date ? `${escapeHtml(item.appointment_date)} ${escapeHtml(item.appointment_time || '')}` : (escapeHtml(item.hospital_number || item.phone || ''));
        const href = item.id ? ('/patients/' + encodeURIComponent(item.id)) : '#';
        return `<a href="${href}" class="block p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">${label}</div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">${sub}</div>
                    </div>
                    <div class="text-xs text-gray-400">${escapeHtml(item.hospital_number || '')}</div>
                  </div>
                </a>`;
      }).join('');
      resultsBox.innerHTML = `<div class="divide-y">${html}</div>`;
      positionResultsFixed();
    }

    searchInput.addEventListener('input', (ev) => {
      const q = (ev.target.value || '').trim();
      clearTimeout(searchTimer);

      if (!q || q.length < 1) {
        resultsBox.classList.add('hidden');
        restoreResultsStyle();
        return;
      }

      resultsBox.classList.remove('hidden');
      resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">Searching…</div>`;
      positionResultsFixed();

      searchTimer = setTimeout(async () => {
        try {
          const dateParam = dateFilter && dateFilter.value ? `&date=${encodeURIComponent(dateFilter.value)}` : '';
          const url = patientSearchUrl + '?term=' + encodeURIComponent(q) + dateParam;

          const res = await fetch(url, { headers: { Accept: 'application/json' }});
          if (!res.ok) throw new Error('Search failed');

          const json = await res.json();
          renderItems(Array.isArray(json) ? json : []);
        } catch (err) {
          console.error('Search error', err);
          resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">Search failed</div>`;
          positionResultsFixed();
        }
      }, 300);
    });

    // hide results on click outside; restore styles when hidden
    document.addEventListener('click', (ev) => {
      if (!ev.target.closest('#searchResults') && !ev.target.closest('#globalSearch')) {
        resultsBox.classList.add('hidden');
        restoreResultsStyle();
      } else {
        if (!resultsBox.classList.contains('hidden')) positionResultsFixed();
      }
    });

    // reposition on resize/scroll so the fixed box follows the input
    window.addEventListener('resize', () => {
      if (!resultsBox.classList.contains('hidden')) positionResultsFixed();
    });
    window.addEventListener('scroll', () => {
      if (!resultsBox.classList.contains('hidden')) positionResultsFixed();
    }, true);

    // ESC key to close search results
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        $('#searchResults')?.classList.add('hidden');
        restoreResultsStyle();
        searchInput.blur();
      }
    });
  })();
  // ======= end SEARCH block =======

  // Call modal wiring (keeps your existing open/close functions)
  window.openModal = function(id) {
    const el = document.getElementById(id); if (!el) return;
    el.classList.remove('hidden'); el.querySelector('[tabindex="-1"], button, input, a')?.focus();
  };
  window.closeModal = function(id) {
    const el = document.getElementById(id); if (!el) return;
    el.classList.add('hidden');
  };

  // wire call modal open
  document.addEventListener('click', (ev) => {
    const callBtn = ev.target.closest('[data-action="call-now"]');
    if (!callBtn) return;
    const name = callBtn.dataset.name || 'Patient';
    $('#callPatientName') && ($('#callPatientName').textContent = name);
    $('#callPatientNameBody') && ($('#callPatientNameBody').textContent = name);
    openModal('callModal');
  });

  // initial UI
  applyStatusFilter();
  updateBulkUI();

})();
</script>

@endpush

@endsection
