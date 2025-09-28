@extends('layouts.app')
@section('title','Daily Queue')
@section('page-title','Daily Queue')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div>
      <label class="text-sm text-gray-600">Select Date</label>
      <input id="date-picker" type="date" value="{{ date('Y-m-d') }}" class="border rounded px-3 py-2" />
    </div>

    <div class="flex gap-2">
      <button id="btn-call-list" class="px-3 py-2 bg-yellow-500 rounded text-white">Show Call List</button>
      <button id="btn-refresh" class="px-3 py-2 bg-blue-600 rounded text-white">Refresh</button>
    </div>
  </div>

  <section class="grid grid-cols-3 gap-4 mb-4">
    <div class="p-4 bg-white rounded shadow">
      <p class="text-sm text-gray-500">Patients due</p>
      <h2 id="total" class="text-2xl font-bold text-blue-600">0</h2>
    </div>
    <div class="p-4 bg-white rounded shadow">
      <p class="text-sm text-gray-500">Present</p>
      <h2 id="present" class="text-2xl font-bold text-green-600">0</h2>
    </div>
    <div class="p-4 bg-white rounded shadow">
      <p class="text-sm text-gray-500">Not arrived</p>
      <h2 id="absent" class="text-2xl font-bold text-red-600">0</h2>
    </div>
  </section>

  <div class="bg-white rounded shadow overflow-auto">
    <table class="w-full" id="queue-table">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-3">#</th>
          <th class="p-3">Name</th>
          <th class="p-3">Phone</th>
          <th class="p-3">Next Review</th>
          <th class="p-3">Status</th>
          <th class="p-3">Actions</th>
        </tr>
      </thead>
      <tbody id="queue-body"></tbody>
    </table>
  </div>
@endsection

@push('scripts')
<script>
const api = axios.create();
async function loadQueue(date){
  try {
    const res = await api.get('/api/daily-queue?date=' + date);
    const data = res.data;
    document.getElementById('total').innerText = data.length;
    let present=0, absent=0;
    const tbody = document.getElementById('queue-body');
    tbody.innerHTML = '';
    data.forEach((row, idx) => {
      if(['queued','in_room','seen'].includes(row.status)) present++; else absent++;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="p-3">${idx+1}</td>
        <td class="p-3">${row.patient.first_name} ${row.patient.last_name || ''}</td>
        <td class="p-3"><a href="tel:${row.patient.phone}">${row.patient.phone || ''}</a></td>
        <td class="p-3">${row.scheduled_date}</td>
        <td class="p-3"><span class="px-2 py-1 rounded ${statusClass(row.status)}">${row.status}</span></td>
        <td class="p-3">
          <button onclick="markPresent(${row.id})" class="mr-2 bg-green-500 text-white px-2 py-1 rounded">Queue</button>
          <button onclick="openCallLog(${row.id}, ${row.patient.id})" class="bg-yellow-500 text-white px-2 py-1 rounded">Call</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
    document.getElementById('present').innerText = present;
    document.getElementById('absent').innerText = absent;
  } catch(e){ console.error(e); alert('Failed to load queue'); }
}

function statusClass(s){
  if(s==='queued' || s==='in_room' || s==='seen') return 'bg-green-100 text-green-800';
  if(s==='not_arrived' || s==='scheduled') return 'bg-red-100 text-red-800';
  return 'bg-yellow-100 text-yellow-800';
}

async function markPresent(id) {
  try {
    await api.post('/api/appointments/' + id + '/present', {}, {
      headers: { 'X-CSRF-TOKEN': csrfToken }
    });
    loadQueue(document.getElementById('date-picker').value);
  } catch (e) {
    console.error(e);
    alert('Failed to mark present');
  }
}

document.getElementById('btn-refresh').addEventListener('click', ()=> loadQueue(document.getElementById('date-picker').value));
document.getElementById('btn-call-list').addEventListener('click', ()=> {
  const date = document.getElementById('date-picker').value;
  window.location = '/call-logs?date=' + date;
});
loadQueue(document.getElementById('date-picker').value);
</script>
@endpush
