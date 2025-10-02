@extends('layouts.app')

@section('title', 'Daily Queue')

@section('content')
<div class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-indigo-950 min-h-screen">
  <div class="max-w-7xl mx-auto p-6">
    <div class="glass-card rounded-3xl overflow-hidden shadow-2xl card-hover">
      <!-- Header -->
      <div class="bg-gradient-to-r from-gray-50/90 to-blue-50/90 dark:from-gray-800/90 dark:to-blue-900/90 backdrop-blur-sm px-6 py-4 border-b border-gray-200/50 dark:border-gray-700/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center" aria-hidden="true">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daily Queue</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400" id="queueSubtitle">{{ $daily->count() }} appointments scheduled</p>
            </div>
          </div>

          <div class="flex items-center space-x-3">
            <!-- Date filter -->
            <div class="flex items-center space-x-2">
              <label for="dateFilter" class="text-sm text-gray-600 dark:text-gray-300">Date</label>
              <input id="dateFilter" name="date" type="date" value="{{ request('date', \Illuminate\Support\Carbon::today()->toDateString()) }}" class="px-3 py-2 bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Search -->
            <div class="relative w-96">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input id="globalSearch" class="w-full pl-12 pr-12 py-3 bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-2xl placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white" placeholder="Search patients, appointments... (Press / to focus)" type="text" autocomplete="off" />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <kbd class="px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 rounded border border-gray-200 dark:border-gray-600">/</kbd>
              </div>

              <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl hidden max-h-72 overflow-y-auto z-50">
                <div class="p-4 text-sm text-gray-600 dark:text-gray-300">Type to search patients or appointments for the selected date...</div>
              </div>
            </div>

            <!-- Status filter & refresh -->
            <select id="statusFilter" class="px-3 py-2 bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
              <option value="">All Status</option>
              <option value="present">Present</option>
              <option value="scheduled">Scheduled</option>
              <option value="missed">Missed</option>
            </select>

            <button id="refreshQueue" class="p-2 bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" title="Refresh queue">
              <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions Bar -->
      <div class="bg-gray-50/90 dark:bg-gray-800/90 backdrop-blur-sm px-6 py-3 border-b border-gray-200/50 dark:border-gray-700/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <label class="flex items-center space-x-2">
              <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select All</span>
            </label>

            <div id="bulkActions" class="flex items-center space-x-2 opacity-0 transition-opacity duration-200">
              <button id="bulkMarkPresent" class="px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-sm hover:bg-emerald-600">Mark Present</button>
              <button id="bulkMarkAbsent" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600">Mark Absent</button>
            </div>
          </div>

          <span class="text-sm text-gray-500 dark:text-gray-400" id="selectionCount">0 selected</span>
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

          <div class="queue-item border-b border-gray-100 dark:border-gray-800 px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-all" data-status="{{ $status }}" data-appointment-id="{{ $appt->id }}">
            <div class="flex items-center space-x-4">
              <input type="checkbox" class="queue-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" data-appointment-id="{{ $appt->id }}">
              <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold"
                   style="background:linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to)); --tw-gradient-from: #60a5fa; --tw-gradient-to: #6366f1;">
                {{ $initials ?: 'P' }}
              </div>

              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $patient->first_name ?? 'Unknown' }} {{ $patient->last_name ?? '' }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $patient->address ?? '—' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">Phone: {{ $patient->phone ?? '—' }} • Next of Kin: {{ $patient->next_of_kin_name ?? '—' }}</p>
                  </div>

                  <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $time }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-badge {{ $status }}">
                      @if($status === 'present')
                        <span class="w-2 h-2 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>
                        Present
                      @elseif($status === 'missed')
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span>
                        Missed
                      @else
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-1.5"></span>
                        Scheduled
                      @endif
                    </span>
                  </div>
                </div>

                <div class="flex items-center space-x-2 mt-3">
                  <button class="mark-present single-action px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-sm hover:bg-emerald-600" data-appt-id="{{ $appt->id }}">Mark Present</button>

                  <button class="single-absent px-3 py-1.5 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600" data-appt-id="{{ $appt->id }}">Mark Absent</button>

                  <a href="{{ route('patients.show', optional($patient)->id) }}" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50">View</a>

                  <button class="px-3 py-1.5 bg-yellow-500 text-white rounded-lg text-sm hover:bg-yellow-600" data-action="call-now" data-name="{{ $patient->first_name ?? 'Patient' }}">Call Now</button>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="p-12 text-center" id="emptyQueue">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No appointments found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Try adjusting your filters or the date</p>
            <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Create Appointment</a>
          </div>
        @endforelse
      </div>

      <!-- Floating Action Button (kept) -->
      <div class="fixed bottom-6 right-6 z-50">
        <button id="fabButton" class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-full shadow-2xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-110 flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Call Modal (unchanged) -->
  <div id="callModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="callModalTitle">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('callModal')" aria-hidden="true"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 p-6 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </div>
              <div>
                <h3 id="callModalTitle" class="text-lg font-bold text-gray-900 dark:text-white">Call Patient</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" id="callPatientName">Patient</p>
              </div>
            </div>
            <button onclick="closeModal('callModal')" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" aria-label="Close call modal">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6">
          <p class="text-sm text-gray-600 dark:text-gray-400">Call actions for <strong id="callPatientNameBody">Patient</strong></p>
          <div class="mt-4 flex gap-3">
            <button class="px-4 py-2 bg-emerald-500 text-white rounded-lg" id="callConfirmBtn">Call</button>
            <button class="px-4 py-2 bg-white border rounded-lg" onclick="closeModal('callModal')">Cancel</button>
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

  // CSRF token (blade injected)
  const csrf = '{{ csrf_token() }}';
  const markPresentUrl = '{{ route("daily-queue.mark-present") }}';
  const markAbsentUrl  = '{{ route("daily-queue.mark-absent") }}';
  const patientSearchUrl = '{{ url("/patients/search") }}'; // adjust if you have a named route

  // keyboard focus for search
  const searchInput = $('#globalSearch');
  if (searchInput) {
    window.addEventListener('keydown', (e) => {
      if (e.key === '/' && document.activeElement !== searchInput) {
        e.preventDefault();
        searchInput.focus();
        searchInput.select();
      }
    });
  }

  // date change -> reload page with ?date=...
  const dateFilter = $('#dateFilter');
  if (dateFilter) {
    dateFilter.addEventListener('change', () => {
      const d = dateFilter.value;
      const url = new URL(window.location.href);
      url.searchParams.set('date', d);
      window.location.href = url.toString();
    });
  }

  // status filter & helpers
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
  if (statusFilter) statusFilter.addEventListener('change', applyStatusFilter);

  // refresh visual
  $('#refreshQueue')?.addEventListener('click', () => {
    const card = queueList.closest('.glass-card');
    card?.classList.add('animate-pulse');
    setTimeout(() => card?.classList.remove('animate-pulse'), 550);
  });

  // selection + bulk actions
  const selectAll = $('#selectAll'), bulkActions = $('#bulkActions'), selectionCountEl = $('#selectionCount');
  function updateBulkUI() {
    const checked = $$('.queue-checkbox:checked', queueList).map(c => c.dataset.appointmentId);
    const count = checked.length;
    selectionCountEl.textContent = `${count} selected`;
    bulkActions.style.opacity = count > 0 ? '1' : '0';
    return checked;
  }
  if (selectAll) {
    selectAll.addEventListener('change', (e) => {
      const checked = e.target.checked;
      $$('.queue-checkbox', queueList).forEach(cb => cb.checked = checked);
      updateBulkUI();
    });
  }
  queueList.addEventListener('change', (e) => {
    if (e.target.matches('.queue-checkbox')) updateBulkUI();
  });

  // helper: POST JSON (returns parsed json or throws)
  async function postJson(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    if (!res.ok) {
      const text = await res.text().catch(() => '');
      try { return JSON.parse(text); } catch { throw new Error('Request failed'); }
    }
    return res.json().catch(() => ({}));
  }

  // Helper: update a row's UI and move to bottom
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

    // move to bottom so unmarked stay at top
    if (row.parentNode) {
      row.parentNode.appendChild(row);
      row.classList.add('opacity-80');
      setTimeout(() => row.classList.remove('opacity-80'), 600);
    }
  }

  // small helper to know if a status is considered "marked"
  function isMarkedStatus(s) {
    if (!s) return false;
    return ['present','missed','absent'].includes(s);
  }

  // SINGLE: mark present (delegated) with confirm if overwriting another marked status
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.mark-present');
    if (!btn) return;
    const apptId = btn.dataset.apptId;
    if (!apptId) return;

    const row = document.querySelector(`.queue-item[data-appointment-id="${apptId}"]`);
    const current = row?.dataset.status || '';

    // if currently marked (present/missed/absent) and different from target, confirm overwrite
    if (isMarkedStatus(current) && current !== 'present') {
      const ok = confirm(`This appointment is currently marked as "${current.toUpperCase()}". Are you sure you want to change it to PRESENT?`);
      if (!ok) return;
    }

    btn.disabled = true;
    btn.classList.add('opacity-70');
    try {
      const resp = await postJson(markPresentUrl, { appointment_ids: [parseInt(apptId,10)] });
      if (resp && resp.success) {
        setRowStatusAndMove(row, 'present');
      } else {
        alert(resp.message ?? 'Could not mark present');
      }
    } catch (err) {
      console.error('Mark present error', err);
      alert('Failed to mark present');
    } finally {
      btn.disabled = false;
      btn.classList.remove('opacity-70');
      applyStatusFilter();
      updateBulkUI();
    }
  });

  // SINGLE: mark absent (delegated) with confirm if overwriting another marked status
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.single-absent');
    if (!btn) return;
    const apptId = btn.dataset.apptId;
    if (!apptId) return;

    const row = document.querySelector(`.queue-item[data-appointment-id="${apptId}"]`);
    const current = row?.dataset.status || '';

    if (isMarkedStatus(current) && current !== 'missed' && current !== 'absent') {
      const ok = confirm(`This appointment is currently marked as "${current.toUpperCase()}". Are you sure you want to change it to ABSENT/MISSED?`);
      if (!ok) return;
    }

    btn.disabled = true; btn.classList.add('opacity-70');
    try {
      const resp = await postJson(markAbsentUrl, { appointment_ids: [parseInt(apptId,10)] });
      if (resp && resp.success) {
        setRowStatusAndMove(row, 'missed');
      } else {
        alert(resp.message ?? 'Could not mark absent');
      }
    } catch (err) {
      console.error('Mark absent error', err);
      alert('Failed to mark absent');
    } finally {
      btn.disabled = false; btn.classList.remove('opacity-70');
      applyStatusFilter();
      updateBulkUI();
    }
  });

  // BULK: mark present with overwrite prompt when necessary
  $('#bulkMarkPresent')?.addEventListener('click', async () => {
    const ids = updateBulkUI();
    if (!ids.length) return alert('No appointments selected');

    // count how many selected have marked status and are NOT already 'present'
    const overwriteRows = ids.map(id => document.querySelector(`.queue-item[data-appointment-id="${id}"]`))
                             .filter(r => r && isMarkedStatus(r.dataset.status) && r.dataset.status !== 'present');

    if (overwriteRows.length) {
      const ok = confirm(`${overwriteRows.length} of the selected appointment(s) are already marked (present/missed). Marking as PRESENT will overwrite them. Proceed?`);
      if (!ok) return;
    }

    if (!confirm(`Mark ${ids.length} appointment(s) as present?`)) return;

    try {
      const resp = await postJson(markPresentUrl, { appointment_ids: ids.map(i => parseInt(i,10)) });
      if (resp && resp.success) {
        ids.forEach(id => {
          const row = document.querySelector(`.queue-item[data-appointment-id="${id}"]`);
          setRowStatusAndMove(row, 'present');
        });
        updateBulkUI();
      } else {
        alert(resp.message ?? 'Failed to mark present');
      }
    } catch (err) {
      console.error('Bulk present error', err);
      alert('Failed to mark present');
    } finally {
      applyStatusFilter();
    }
  });

  // BULK: mark absent with overwrite prompt when necessary
  $('#bulkMarkAbsent')?.addEventListener('click', async () => {
    const ids = updateBulkUI();
    if (!ids.length) return alert('No appointments selected');

    // count how many selected have marked status and are NOT already 'missed'/'absent'
    const overwriteRows = ids.map(id => document.querySelector(`.queue-item[data-appointment-id="${id}"]`))
                             .filter(r => r && isMarkedStatus(r.dataset.status) && !['missed','absent'].includes(r.dataset.status));

    if (overwriteRows.length) {
      const ok = confirm(`${overwriteRows.length} of the selected appointment(s) are already marked (present/missed). Marking as ABSENT/MISSED will overwrite them. Proceed?`);
      if (!ok) return;
    }

    if (!confirm(`Mark ${ids.length} appointment(s) as absent/missed?`)) return;

    try {
      const resp = await postJson(markAbsentUrl, { appointment_ids: ids.map(i => parseInt(i,10)) });
      if (resp && resp.success) {
        ids.forEach(id => {
          const row = document.querySelector(`.queue-item[data-appointment-id="${id}"]`);
          setRowStatusAndMove(row, 'missed');
        });
        updateBulkUI();
      } else {
        alert(resp.message ?? 'Failed to mark absent');
      }
    } catch (err) {
      console.error('Bulk absent error', err);
      alert('Failed to mark absent');
    } finally {
      applyStatusFilter();
    }
  });

  // search (debounced)
  let searchTimer = null;
  if (searchInput) {
    const resultsBox = $('#searchResults');
    searchInput.addEventListener('input', (ev) => {
      const q = ev.target.value.trim();
      clearTimeout(searchTimer);
      if (!q) {
        resultsBox.classList.add('hidden');
        return;
      }
      resultsBox.classList.remove('hidden');
      resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">Searching...</div>`;
      searchTimer = setTimeout(async () => {
        try {
          const date = dateFilter?.value || '{{ \Illuminate\Support\Carbon::today()->toDateString() }}';
          const res = await fetch(patientSearchUrl + '?term=' + encodeURIComponent(q) + '&date=' + encodeURIComponent(date), { headers: { Accept: 'application/json' }});
          const json = await res.json();
          if (!json || !json.length) {
            resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">No results</div>`;
            return;
          }
          resultsBox.innerHTML = `<div class="divide-y">` + json.map(item => `
            <a href="${ item.id ? ('/patients/' + item.id) : '#'}" class="block p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl">
              <div class="text-sm font-medium">${item.first_name ?? 'Patient'} ${item.last_name ?? ''}</div>
              <div class="text-xs text-gray-500">${item.scheduled_time ? item.scheduled_time : (item.address ?? '')}</div>
            </a>
          `).join('') + `</div>`;
        } catch (err) {
          console.error(err);
          resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">Search failed</div>`;
        }
      }, 350);
    });

    document.addEventListener('click', (ev) => {
      if (!ev.target.closest('#searchResults, #globalSearch')) $('#searchResults')?.classList.add('hidden');
    });
  }

  // Call modal wiring
  window.openModal = function(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    const first = el.querySelector('button, [href], input');
    first?.focus();
  };
  window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
  };

  document.addEventListener('click', (ev) => {
    const callBtn = ev.target.closest('[data-action="call-now"]');
    if (!callBtn) return;
    const name = callBtn.dataset.name || 'Patient';
    $('#callPatientName') && ($('#callPatientName').textContent = name);
    $('#callPatientNameBody') && ($('#callPatientNameBody').textContent = name);
    openModal('callModal');
  });

  // initial UI adjust
  applyStatusFilter();
  updateBulkUI();
})();
</script>
@endpush



@endsection
