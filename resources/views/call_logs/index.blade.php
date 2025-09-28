@extends('layouts.app')
@section('title','Call Logs')
@section('page-title','Call Logs')

@section('content')
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Call Logs</h2>
    <a href="/appointments/create" class="px-3 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors">Back to Appointments</a>
  </div>

  <div class="bg-white dark:bg-gray-800 rounded shadow relative overflow-hidden border border-gray-200 dark:border-gray-700">
    <table class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th class="p-3 text-left text-gray-900 dark:text-white">#</th>
          <th class="p-3 text-left text-gray-900 dark:text-white cursor-pointer" onclick="toggleAllDetails()">Patient <span class="text-xs text-gray-500 dark:text-gray-400">(click to expand)</span></th>
          <th class="p-3 text-left text-gray-900 dark:text-white">Time</th>
          <th class="p-3 text-left text-gray-900 dark:text-white">Result</th>
          <th class="p-3 text-left text-gray-900 dark:text-white">Notes</th>
          <th class="p-3 text-left text-gray-900 dark:text-white">Actions</th>
        </tr>
      </thead>
      <tbody id="calllogs-body"></tbody>
    </table>
  </div>

  <!-- Slide-over detail panel (hidden) -->
  <aside id="callDetailPanel" class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl transform translate-x-full transition-transform duration-200 z-50" aria-hidden="true" style="will-change: transform;">
    <div class="p-6 flex items-start justify-between border-b border-gray-200 dark:border-gray-700">
      <div>
        <h3 id="detailPatientName" class="text-lg font-semibold text-gray-900 dark:text-white">Patient name</h3>
        <p id="detailScheduled" class="text-sm text-gray-500 dark:text-gray-400">Scheduled</p>
      </div>
      <button id="closeDetail" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1 transition-all" aria-label="Close details">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="p-6 space-y-4">
      <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400">Phone</label>
        <div class="flex items-center gap-3 mt-2">
          <a id="detailPhoneLink" href="#" class="text-lg font-medium text-blue-600 dark:text-blue-400 underline hover:text-blue-700 dark:hover:text-blue-300">+233 000 000 000</a>
          <button id="copyPhone" class="px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Copy</button>
        </div>
      </div>

      <div class="flex gap-3">
        <a id="telCallBtn" href="#" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-800 transition-colors" aria-label="Call patient">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1"/></svg>
          Call
        </a>

        <button id="logCallOpen" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Log Call</button>
      </div>

      <hr class="border-dashed border-gray-200 dark:border-gray-700">

      <!-- Inline Log Call form -->
      <form id="inlineLogForm" class="space-y-3 hidden" aria-hidden="true">
        <input type="hidden" id="inlineAppointmentId" name="appointment_id">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Result</label>
          <select id="inlineResult" name="result" required class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            <option value="">Select outcome</option>
            <option value="no_answer">No Answer</option>
            <option value="rescheduled">Rescheduled</option>
            <option value="will_attend">Will Attend</option>
            <option value="refused">Refused</option>
            <option value="incorrect_number">Incorrect Number</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
          <textarea id="inlineNotes" name="notes" rows="3" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Notes..."></textarea>
        </div>
        <div class="flex gap-3 justify-end">
          <button type="button" id="inlineCancel" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors">Save Log</button>
        </div>
      </form>
    </div>
  </aside>

  <!-- Toast container -->
  <div id="toast" class="fixed bottom-6 right-6 z-50 space-y-2"></div>
@endsection

@push('scripts')
<script>
/*
  Call Logs UI with slide-over detail & inline call logging.
  - Requires axios to be available globally (like your original script).
  - Expects GET /api/call-logs -> array of logs
  - Expects POST /api/call-logs to create a log { appointment_id, result, notes }
*/
(function(){
  // ensure axios has csrf header (if meta exists)
  const meta = document.querySelector('meta[name="csrf-token"]');
  if(meta && window.axios) axios.defaults.headers.common['X-CSRF-TOKEN'] = meta.content;

  const tbody = document.getElementById('calllogs-body');
  const detailPanel = document.getElementById('callDetailPanel');
  const closeDetail = document.getElementById('closeDetail');
  const detailPatientName = document.getElementById('detailPatientName');
  const detailScheduled = document.getElementById('detailScheduled');
  const detailPhoneLink = document.getElementById('detailPhoneLink');
  const telCallBtn = document.getElementById('telCallBtn');
  const copyPhoneBtn = document.getElementById('copyPhone');
  const logCallOpen = document.getElementById('logCallOpen');
  const inlineForm = document.getElementById('inlineLogForm');
  const inlineAppointmentId = document.getElementById('inlineAppointmentId');
  const inlineResult = document.getElementById('inlineResult');
  const inlineNotes = document.getElementById('inlineNotes');
  const inlineCancel = document.getElementById('inlineCancel');
  const toastWrap = document.getElementById('toast');

  // small toast helper
  function showToast(msg, timeout=2500){
    const el = document.createElement('div');
    el.className = 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 rounded shadow text-gray-900 dark:text-white';
    el.innerText = msg;
    toastWrap.appendChild(el);
    setTimeout(()=> {
      el.remove();
    }, timeout);
  }

  function openPanel() {
    detailPanel.style.transform = 'translateX(0)';
    detailPanel.setAttribute('aria-hidden', 'false');
  }
  function closePanel() {
    detailPanel.style.transform = '';
    detailPanel.setAttribute('aria-hidden', 'true');
    // hide form on close
    hideInlineForm();
  }

  closeDetail.addEventListener('click', closePanel);

  // copy to clipboard
  copyPhoneBtn.addEventListener('click', async () => {
    const phone = detailPhoneLink.dataset.phone || detailPhoneLink.textContent;
    try {
      await navigator.clipboard.writeText(phone);
      showToast('Phone copied');
    } catch (e) {
      showToast('Could not copy');
    }
  });

  // open log form inline
  logCallOpen.addEventListener('click', () => {
    showInlineForm();
  });
  inlineCancel.addEventListener('click', hideInlineForm);

  function showInlineForm(){
    inlineForm.classList.remove('hidden');
    inlineForm.setAttribute('aria-hidden', 'false');
    inlineResult.focus();
  }
  function hideInlineForm(){
    inlineForm.classList.add('hidden');
    inlineForm.setAttribute('aria-hidden', 'true');
    inlineResult.value = '';
    inlineNotes.value = '';
  }

  // handle inline form submit -> POST to /api/call-logs
  inlineForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const appointment_id = inlineAppointmentId.value;
    const result = inlineResult.value;
    const notes = inlineNotes.value;
    if(!result){ showToast('Select a result'); return; }
    try {
      const payload = { appointment_id, result, notes };
      const res = await axios.post('/api/call-logs', payload);
      if(res.data && (res.data.success || res.status === 201 || res.status === 200)){
        showToast('Logged call');
        hideInlineForm();
        loadCalls();          // refresh table
      } else {
        showToast(res.data.message || 'Could not save');
      }
    } catch (err) {
      console.error(err);
      showToast('Network error');
    }
  });

  // populate rows
  async function loadCalls(date = null){
    try {
      let url = '/api/call-logs';
      if(date) url += '?date=' + encodeURIComponent(date);
      const res = await axios.get(url);
      tbody.innerHTML = '';
      (res.data || []).forEach((c, i) => {
        // keep columns consistent: #, patient, time, result, notes, actions
        const tr = document.createElement('tr');
        tr.className = 'border-t';
        // escape html simple util
        const esc = (s) => (s===null || s===undefined) ? '' : String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        tr.innerHTML = `
          <td class="p-3 align-top">${i+1}</td>
          <td class="p-3 align-top cursor-pointer" onclick="toggleDetail('${c.id}')">
            <div class="flex items-center gap-2">
              <span class="text-gray-900 dark:text-white">${esc(c.patient_name || (c.patient && (c.patient.first_name + ' ' + (c.patient.last_name||''))))}</span>
              <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </td>
          <td class="p-3 align-top text-gray-900 dark:text-white">${esc(c.call_time || c.created_at || '')}</td>
          <td class="p-3 align-top text-gray-900 dark:text-white">${esc(c.result || '')}</td>
          <td class="p-3 align-top text-gray-900 dark:text-white">${esc(c.notes || '')}</td>
          <td class="p-3 align-top">
            <button class="view-detail inline-flex items-center gap-2 px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" 
              data-appointment='${JSON.stringify({
                id: c.appointment_id ?? c.appointment?.id ?? null,
                patient_name: c.patient_name ?? (c.patient && (c.patient.first_name + ' ' + (c.patient.last_name||''))) ?? '',
                phone: c.patient_phone ?? c.patient?.phone ?? c.phone ?? '',
                scheduled: c.scheduled_date ?? c.scheduled_time ?? ''
              }).replaceAll("'", "\\'")}'>
              View
            </button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    } catch (err) {
      console.error(err);
      showToast('Could not load call logs');
    }
  }

  // delegation: click view
  tbody.addEventListener('click', (ev) => {
    const btn = ev.target.closest('.view-detail');
    if(!btn) return;
    // data-appointment contains JSON string
    const dataStr = btn.getAttribute('data-appointment');
    let appt = {};
    try { appt = JSON.parse(dataStr); } catch(e){ console.error(e); }
    // populate panel
    detailPatientName.textContent = appt.patient_name || 'Unknown';
    detailScheduled.textContent = appt.scheduled || '';
    const phone = appt.phone || 'N/A';
    detailPhoneLink.dataset.phone = phone;
    detailPhoneLink.href = phone && phone !== 'N/A' ? `tel:${phone}` : '#';
    detailPhoneLink.textContent = phone;
    telCallBtn.href = phone && phone !== 'N/A' ? `tel:${phone}` : '#';
    inlineAppointmentId.value = appt.id || '';
    // open panel
    openPanel();
  });

  // initial load
  loadCalls();

  // close when clicking outside panel (optional)
  document.addEventListener('click', (e) => {
    if(!detailPanel.contains(e.target) && !e.target.closest('.view-detail') && detailPanel.getAttribute('aria-hidden') === 'false'){
      // keep it friendly: only close when clicking outside
      closePanel();
    }
  });

  // keyboard escape to close
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && detailPanel.getAttribute('aria-hidden') === 'false') closePanel();
  });
})();
</script>
@endpush