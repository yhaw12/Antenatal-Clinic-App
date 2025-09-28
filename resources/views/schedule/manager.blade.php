@extends('layouts.app') {{-- or layouts.lhims --}}

@section('title','Schedule Manager')

@section('content')
<div class="bg-white rounded shadow p-3">
  <div class="flex items-center justify-between gap-3 mb-3">
    <div class="flex items-center gap-2">
      <input id="date-picker" type="date" value="{{ $date }}" class="border rounded px-3 py-1" />
      <select id="filter-doctor" class="border rounded px-3 py-1">
        <option value="">All Doctor</option>
        {{-- populate server-side or via API --}}
      </select>
      <select id="filter-clinic" class="border rounded px-3 py-1">
        <option value="">All Clinics</option>
      </select>

      <select id="view-mode" class="border rounded px-3 py-1">
        <option value="day">Day</option>
        <option value="week">Week</option>
        <option value="month">Month</option>
        <option value="list">List</option>
      </select>

      <button id="btn-refresh" class="px-3 py-1 bg-blue-600 text-white rounded">Refresh</button>
    </div>

    <div>
      <input id="search-patient" class="border rounded px-3 py-1" placeholder="Search Patient">
      <button id="btn-search" class="px-3 py-1 bg-gray-200 rounded">Search</button>
    </div>
  </div>

  <div class="flex gap-4">
    {{-- times column --}}
    <div class="w-24 border-r pr-2">
      <div class="text-xs text-gray-600 mb-2">Time</div>
      <div id="time-column" class="space-y-6 text-sm text-gray-700">
        {{-- JS fills hours 07:00 - 18:00 --}}
      </div>
    </div>

    {{-- main appointments column (scrollable) --}}
    <div class="flex-1">
      <div id="appointments-area" class="space-y-3">
        {{-- appointment tiles rendered here by JS --}}
        <div class="text-sm text-gray-500">Loading appointments...</div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const apiBase = "{{ url('/api') }}";
const axiosInstance = axios.create({ headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });

function hourRange(start=7, end=18) {
  const arr = [];
  for (let h = start; h <= end; h++){
    const hh = String(h).padStart(2,'0') + ':00';
    arr.push(hh);
  }
  return arr;
}

function renderTimeColumn() {
  const col = document.getElementById('time-column');
  col.innerHTML = '';
  hourRange().forEach(h => {
    const el = document.createElement('div');
    el.className = 'py-3 border-b';
    el.innerText = h;
    col.appendChild(el);
  });
}

async function loadSchedule(){
  const date = document.getElementById('date-picker').value;
  const doctor = document.getElementById('filter-doctor').value;
  const clinic = document.getElementById('filter-clinic').value;
  const url = `${apiBase}/schedule?date=${date}&doctor=${doctor}&clinic=${clinic}`;
  try {
    const res = await axiosInstance.get(url);
    renderAppointments(res.data.appointments);
  } catch(err) {
    console.error(err);
    document.getElementById('appointments-area').innerHTML = '<div class="text-red-600">Failed to load appointments.</div>';
  }
}

function renderAppointments(appts){
  const area = document.getElementById('appointments-area');
  area.innerHTML = '';
  if (!appts || appts.length === 0) {
    area.innerHTML = '<div class="text-sm text-gray-500">No appointments scheduled for this date.</div>';
    return;
  }

  // group by time slot (hour:minute)
  const grouped = {};
  appts.forEach(a => {
    const timeKey = a.time || '00:00';
    grouped[timeKey] = grouped[timeKey] || [];
    grouped[timeKey].push(a);
  });

  Object.keys(grouped).sort().forEach(time => {
    const row = document.createElement('div');
    row.className = 'border rounded overflow-hidden';
    // time header
    const header = document.createElement('div');
    header.className = 'bg-green-50 px-3 py-2 flex items-center justify-between';
    header.innerHTML = `<div class="text-sm font-semibold">${time}</div>
                        <div class="text-xs text-gray-600">${grouped[time].length} patients</div>`;
    row.appendChild(header);

    // tiles container
    const tiles = document.createElement('div');
    tiles.className = 'space-y-2 p-3';

    grouped[time].forEach(a => {
      const tile = document.createElement('div');
      tile.className = 'bg-green-100 p-3 rounded flex items-start justify-between';
      tile.innerHTML = `
        <div class="flex-1">
          <div class="font-semibold">${escapeHtml(a.patient.name)}</div>
          <div class="text-xs text-gray-700 mt-1">${a.procedure_list && a.procedure_list.length ? a.procedure_list.join(', ') : ''}</div>
          <div class="text-xs text-gray-600 mt-1">Status: <span class="font-medium">${a.status}</span></div>
        </div>
        <div class="ml-3 flex flex-col gap-2 items-end">
          <button class="btn-arrive px-2 py-1 bg-blue-600 text-white rounded text-xs" data-id="${a.id}">Arrived</button>
          <button class="btn-approve px-2 py-1 bg-gray-200 rounded text-xs" data-id="${a.id}">Approve</button>
          <button class="btn-bill px-2 py-1 bg-orange-500 text-white rounded text-xs" data-id="${a.id}">Bill</button>
        </div>`;
      tiles.appendChild(tile);
    });

    row.appendChild(tiles);
    area.appendChild(row);
  });

  // bind buttons
  document.querySelectorAll('.btn-arrive').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = e.target.dataset.id;
      try {
        await axiosInstance.post(`/api/schedule/${id}/arrive`);
        e.target.innerText = 'Queued';
        e.target.classList.remove('bg-blue-600');
        e.target.classList.add('bg-green-600');
        loadSchedule(); // refresh
      } catch(err){ console.error(err); alert('failed to mark arrived'); }
    });
  });

  document.querySelectorAll('.btn-approve').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = e.target.dataset.id;
      try {
        await axiosInstance.post(`/api/schedule/${id}/status`, { status: 'approved' });
        loadSchedule();
      } catch(err){ console.error(err); alert('failed to approve'); }
    });
  });

  document.querySelectorAll('.btn-bill').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = e.target.dataset.id;
      // navigate to billing page or open modal - example:
      window.location.href = `/appointments/${id}`; 
    });
  });
}

function escapeHtml(s='') {
  return String(s).replace(/[&<>"'`=\/]/g, function (c) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c];
  });
}

document.getElementById('btn-refresh').addEventListener('click', loadSchedule);
document.getElementById('btn-search').addEventListener('click', loadSchedule);
document.getElementById('date-picker').addEventListener('change', loadSchedule);

// init
renderTimeColumn();
loadSchedule();
</script>
@endpush
