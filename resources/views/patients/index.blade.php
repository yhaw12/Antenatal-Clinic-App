@extends('layouts.app')
@section('title','Daily Attendance')
@section('page-title','Daily Attendance')

@section('content')
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
    <div>
      <h2 class="text-xl font-semibold text-body">Today's Patients</h2>
      <p class="text-sm text-muted">{{ date('l, F j, Y') }}</p>
      <div class="flex items-center gap-4 mt-2">
        <span class="flex items-center gap-2 text-xs">
          <span class="w-3 h-3 rounded-full bg-green-500"></span>
          <span class="text-muted">Present</span>
        </span>
        <span class="flex items-center gap-2 text-xs">
          <span class="w-3 h-3 rounded-full bg-red-400"></span>
          <span class="text-muted">Absent</span>
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <input id="patientSearch" type="search" placeholder="Search by name, phone or ID..." class="border border-surface rounded px-3 py-2 bg-app text-body" aria-label="Search patients">
      <button id="refreshBtn" class="btn-ghost" aria-label="Refresh patient list">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh
      </button>
      <a href="{{ route('patients.create') }}" class="btn-primary" aria-label="Create new patient">New Patient</a>
    </div>
  </div>

  <!-- Attendance Summary Card -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card p-4">
      <div class="text-sm text-muted mb-1">Total Expected</div>
      <div class="text-2xl font-bold text-body" id="totalCount">0</div>
    </div>
    <div class="card p-4">
      <div class="text-sm text-muted mb-1">Present</div>
      <div class="text-2xl font-bold text-green-600" id="presentCount">0</div>
    </div>
    <div class="card p-4">
      <div class="text-sm text-muted mb-1">Absent</div>
      <div class="text-2xl font-bold text-red-500" id="absentCount">0</div>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div id="patients-status" class="sr-only" aria-live="polite"></div>

    <!-- Desktop table -->
    <div class="overflow-x-auto appointments-table">
      <table class="w-full text-left" role="table" aria-describedby="patients-status">
        <thead>
          <tr class="text-sm text-muted">
            <th class="p-3">#</th>
            <th class="p-3">Name</th>
            <th class="p-3">Phone</th>
            <th class="p-3">Appointment Time</th>
            <th class="p-3">Status</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody id="patients-body">
          <!-- JS populates rows or skeletons while loading -->
        </tbody>
      </table>
    </div>

    <!-- Mobile card fallback -->
    <div id="patients-cards" class="p-4 space-y-3 appointments-cards" aria-hidden="true">
      <!-- populated by JS -->
    </div>
  </div>
@endsection

@push('styles')
<style>
  .attendance-present {
    background: linear-gradient(135deg, #d4f4dd 0%, #e8f5e9 100%);
  }
  .attendance-absent {
    background: linear-gradient(135deg, #ffebee 0%, #fce4ec 100%);
  }
  .dark .attendance-present {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(76, 175, 80, 0.1) 100%);
  }
  .dark .attendance-absent {
    background: linear-gradient(135deg, rgba(244, 67, 54, 0.15) 0%, rgba(244, 67, 54, 0.1) 100%);
  }
  
  @keyframes statusPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
  
  .status-updating {
    animation: statusPulse 1s ease-in-out infinite;
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.getElementById('patients-body');
  const cardsWrap = document.getElementById('patients-cards');
  const search = document.getElementById('patientSearch');
  const statusRegion = document.getElementById('patients-status');
  const refreshBtn = document.getElementById('refreshBtn');
  
  // Summary counts
  const totalEl = document.getElementById('totalCount');
  const presentEl = document.getElementById('presentCount');
  const absentEl = document.getElementById('absentCount');

  function showStatus(msg) {
    if (statusRegion) statusRegion.textContent = msg;
  }

  function updateCounts() {
    const total = loadedPatients.length;
    const present = loadedPatients.filter(p => p.is_present).length;
    const absent = total - present;
    
    totalEl.textContent = total;
    presentEl.textContent = present;
    absentEl.textContent = absent;
  }

  function renderSkeletons(count = 6) {
    tableBody.innerHTML = '';
    for (let i = 0; i < count; i++) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="p-3"><div class="h-4 w-6 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-32 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-24 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-20 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-16 skeleton"></div></td>
        <td class="p-3"><div class="h-4 w-32 skeleton"></div></td>
      `;
      tableBody.appendChild(tr);
    }

    cardsWrap.innerHTML = '';
    for (let i = 0; i < Math.min(4, count); i++) {
      const card = document.createElement('div');
      card.className = 'p-3 border-surface border rounded bg-surface';
      card.innerHTML = `
        <div class="h-4 w-40 skeleton mb-2"></div>
        <div class="h-4 w-28 skeleton mb-1"></div>
        <div class="h-4 w-20 skeleton"></div>
      `;
      cardsWrap.appendChild(card);
    }
    cardsWrap.setAttribute('aria-hidden', 'false');
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#39;');
  }

  function renderRow(p, i) {
    const isPresent = p.is_present || false;
    const rowClass = isPresent ? 'attendance-present' : 'attendance-absent';
    const statusBadge = isPresent 
      ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Present</span>'
      : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Absent</span>';
    
    const tr = document.createElement('tr');
    tr.className = `${rowClass} transition-all duration-300`;
    tr.dataset.patientId = p.id;
    tr.innerHTML = `
      <td class="p-3 align-middle">${i+1}</td>
      <td class="p-3 align-middle">
        <div class="font-medium text-sm text-body">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name || '')}</div>
        <div class="text-xs text-muted">${escapeHtml(p.id_number || p.hospital_number || '')}</div>
      </td>
      <td class="p-3 align-middle text-sm">${escapeHtml(p.phone || '')}</td>
      <td class="p-3 align-middle text-sm">${escapeHtml(p.appointment_time || '—')}</td>
      <td class="p-3 align-middle">${statusBadge}</td>
      <td class="p-3 align-middle">
        <div class="flex items-center gap-2">
          ${!isPresent ? `
            <button class="btn-primary focus-ring" data-action="mark-present" data-id="${p.id}" aria-label="Mark ${escapeHtml(p.first_name)} as present">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Mark Present
            </button>
          ` : `
            <button class="btn-ghost focus-ring" data-action="mark-absent" data-id="${p.id}" aria-label="Mark ${escapeHtml(p.first_name)} as absent">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Mark Absent
            </button>
          `}
          <a class="btn-ghost focus-ring" href="/patients/${p.id}" aria-label="View patient ${escapeHtml(p.first_name)}">View</a>
        </div>
      </td>
    `;
    return tr;
  }

  function renderCard(p, i) {
    const isPresent = p.is_present || false;
    const cardClass = isPresent ? 'attendance-present' : 'attendance-absent';
    const statusBadge = isPresent 
      ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Present</span>'
      : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Absent</span>';
    
    const card = document.createElement('article');
    card.className = `p-3 border-surface border rounded ${cardClass} transition-all duration-300`;
    card.dataset.patientId = p.id;
    card.innerHTML = `
      <div class="flex justify-between items-start gap-2 mb-2">
        <div>
          <div class="text-sm font-semibold text-body">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name || '')}</div>
          <div class="text-xs text-muted">${escapeHtml(p.id_number || p.hospital_number || '')}</div>
        </div>
        ${statusBadge}
      </div>

      <div class="text-xs text-muted mb-1">Phone: ${escapeHtml(p.phone || '—')}</div>
      <div class="text-xs text-muted mb-3">Time: ${escapeHtml(p.appointment_time || '—')}</div>

      <div class="flex gap-2">
        ${!isPresent ? `
          <button class="btn-primary flex-1" data-action="mark-present" data-id="${p.id}">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Present
          </button>
        ` : `
          <button class="btn-ghost flex-1" data-action="mark-absent" data-id="${p.id}">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Absent
          </button>
        `}
        <a class="btn-ghost flex-1 text-center" href="/patients/${p.id}">View</a>
      </div>
    `;
    return card;
  }

  // Mark attendance handler
  async function markAttendance(patientId, isPresent) {
    try {
      // Add visual feedback
      const elements = document.querySelectorAll(`[data-patient-id="${patientId}"]`);
      elements.forEach(el => el.classList.add('status-updating'));
      
      // Make API call
      const response = await axios.post(`/api/patients/${patientId}/attendance`, {
        is_present: isPresent,
        date: new Date().toISOString().split('T')[0] // Today's date
      });
      
      // Update local data
      const patient = loadedPatients.find(p => p.id == patientId);
      if (patient) {
        patient.is_present = isPresent;
      }
      
      // Re-render without full reload
      applyFilter();
      updateCounts();
      
      // Show success message
      showStatus(isPresent ? 'Patient marked as present' : 'Patient marked as absent');
      
    } catch (err) {
      console.error('Failed to update attendance:', err);
      alert('Failed to update attendance. Please try again.');
      
      // Remove visual feedback on error
      const elements = document.querySelectorAll(`[data-patient-id="${patientId}"]`);
      elements.forEach(el => el.classList.remove('status-updating'));
    }
  }

  // Delegated click handler for attendance buttons
  document.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('button[data-action]');
    if (!btn) return;
    
    const action = btn.dataset.action;
    const id = btn.dataset.id;
    
    if (action === 'mark-present') {
      await markAttendance(id, true);
    } else if (action === 'mark-absent') {
      const confirm = window.confirm('Mark this patient as absent?');
      if (confirm) {
        await markAttendance(id, false);
      }
    }
  });

  // Main load function
  let loadedPatients = [];

  async function loadPatients() {
    showStatus('Loading today\'s patients...');
    renderSkeletons(6);

    try {
      // Fetch today's patients with their attendance status
      const today = new Date().toISOString().split('T')[0];
      const res = await axios.get(`/api/patients/daily-attendance?date=${today}`);
      const data = Array.isArray(res.data) ? res.data : (res.data.data || []);
      
      loadedPatients = data;
      applyFilter();
      updateCounts();
      showStatus(`Loaded ${data.length} patients for today.`);
    } catch (err) {
      console.error(err);
      tableBody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-danger">Error loading patients. <button id="retryPatients" class="btn-ghost">Retry</button></td></tr>`;
      cardsWrap.innerHTML = `<div class="p-4 text-center text-danger">Error loading patients. <button id="retryPatientsMobile" class="btn-ghost">Retry</button></div>`;
      document.getElementById('retryPatients')?.addEventListener('click', loadPatients);
      document.getElementById('retryPatientsMobile')?.addEventListener('click', loadPatients);
      showStatus('Error loading patients.');
    }
  }

  function applyFilter() {
    const q = (search.value || '').toLowerCase().trim();
    const filtered = loadedPatients.filter(p => {
      const name = `${p.first_name || ''} ${p.last_name || ''}`.toLowerCase();
      const phone = (p.phone || '').toLowerCase();
      const idnum = (p.id_number || p.hospital_number || '').toLowerCase();
      if (!q) return true;
      return name.includes(q) || phone.includes(q) || idnum.includes(q);
    });

    // Render table rows
    tableBody.innerHTML = '';
    if (filtered.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-muted">No patients found.</td></tr>`;
    } else {
      filtered.forEach((p, i) => tableBody.appendChild(renderRow(p, i)));
    }

    // Render mobile cards
    cardsWrap.innerHTML = '';
    if (filtered.length === 0) {
      const e = document.createElement('div');
      e.className = 'p-4 text-center text-muted';
      e.textContent = 'No patients found.';
      cardsWrap.appendChild(e);
    } else {
      filtered.forEach((p, i) => cardsWrap.appendChild(renderCard(p, i)));
    }
    cardsWrap.setAttribute('aria-hidden', 'false');
  }

  // Event listeners
  search.addEventListener('input', () => {
    applyFilter();
  });

  refreshBtn.addEventListener('click', () => {
    loadPatients();
  });

  // Initial load
  loadPatients();

  // Auto-refresh every 30 seconds
  setInterval(() => {
    loadPatients();
  }, 30000);

  // Reload when tab becomes visible
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') loadPatients();
  });
});
</script>
@endpush