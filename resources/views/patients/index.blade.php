@extends('layouts.app')
@section('title', 'Patients')
@section('page-title', 'Patients')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
  <div>
    <h2 class="text-xl font-semibold text-body" style="color:var(--text)">All Patients</h2>
    <p class="text-sm text-muted" style="color:var(--muted)">Listing of all registered patients (paginated).</p>
  </div>

  <div class="flex items-center gap-2">
    <input
      id="patientSearch"
      type="search"
      placeholder="Filter current page by name, phone or ID..."
      class="border border-surface rounded px-3 py-2 bg-app text-body"
      aria-label="Search patients"
      style="background:var(--bg); color:var(--text); border:1px solid var(--border);"
    >
    <a href="{{ route('patients.create') }}" class="btn-primary" aria-label="Create new patient">New Patient</a>
  </div>
</div>

<div class="grid grid-cols-1 gap-4 mb-6">
  <div class="card p-4 flex items-center justify-between" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow); color:var(--text)">
    <div>
      <div class="text-sm text-muted mb-1" style="color:var(--muted)">Total Patients (all pages)</div>
      <div class="text-2xl font-bold text-body" style="color:var(--text)">{{ $patients->total() }}</div>
    </div>

    <div class="text-right">
      <div class="text-sm text-muted mb-1" style="color:var(--muted)">Showing</div>
      <div class="text-lg font-semibold" style="color:var(--text)">
        {{ $patients->firstItem() ?? 0 }} - {{ $patients->lastItem() ?? 0 }}
        <span class="text-sm text-muted" style="color:var(--muted)">of {{ $patients->total() }}</span>
      </div>
    </div>
  </div>
</div>

<div class="card overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow); color:var(--text)">

  <div id="patients-status" class="sr-only" aria-live="polite"></div>

  <!-- Desktop table -->
  <div class="overflow-x-auto appointments-table">
    <table class="w-full text-left" role="table" aria-describedby="patients-status" style="color:var(--text)">
      <thead>
        <tr style="color:var(--muted); background: linear-gradient(90deg, color-mix(in srgb, var(--bg) 96%, transparent), color-mix(in srgb, var(--brand) 6%, transparent));">
          <th class="p-3">#</th>
          <th class="p-3">Name</th>
          <th class="p-3">Phone</th>
          <th class="p-3">Hospital / ID</th>
          <th class="p-3">Created</th>
          <th class="p-3">Actions</th>
        </tr>
      </thead>
      <tbody id="patients-body">
        @forelse ($patients as $i => $patient)
          <tr
            class="transition-colors"
            data-name="{{ strtolower($patient->first_name.' '.$patient->last_name) }}"
            data-phone="{{ strtolower($patient->phone ?? '') }}"
            data-id="{{ strtolower($patient->hospital_number ?? $patient->id_number ?? '') }}"
            style="color:var(--text);"
            >
            <td class="p-3 align-middle" style="color:var(--text)">{{ $patients->firstItem() + $i }}</td>
            <td class="p-3 align-middle">
              <div class="font-medium text-sm text-body" style="color:var(--text)">{{ $patient->first_name }} {{ $patient->last_name }}</div>
              <div class="text-xs text-muted" style="color:var(--muted)">{{ $patient->folder_no ?? '' }}</div>
            </td>
            <td class="p-3 align-middle text-sm" style="color:var(--text)">{{ $patient->phone ?? '—' }}</td>
            <td class="p-3 align-middle text-sm" style="color:var(--text)">{{ $patient->hospital_number ?? $patient->id_number ?? '—' }}</td>
            <td class="p-3 align-middle text-sm" style="color:var(--text)">{{ optional($patient->created_at)->format('Y-m-d') }}</td>
            <td class="p-3 align-middle">
              <div class="flex items-center gap-2">
                <a class="btn-ghost" href="{{ route('patients.show', $patient->id) }}" style="background:transparent; border:1px solid transparent; color:var(--text)">View</a>
                <a class="btn-ghost" href="{{ route('patients.edit', $patient->id) }}" style="background:transparent; border:1px solid transparent; color:var(--text)">Edit</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-6 text-center text-muted" style="color:var(--muted)">No patients available.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Mobile card fallback -->
  <div id="patients-cards" class="p-4 space-y-3 lg:hidden">
    @forelse ($patients as $patient)
      <article class="p-3 border-surface border rounded" style="background:var(--surface); border:1px solid var(--border); color:var(--text)">
        <div class="flex justify-between items-start gap-2 mb-2">
          <div>
            <div class="text-sm font-semibold text-body" style="color:var(--text)">{{ $patient->first_name }} {{ $patient->last_name }}</div>
            <div class="text-xs text-muted" style="color:var(--muted)">{{ $patient->hospital_number ?? $patient->id_number ?? '' }}</div>
          </div>
          <div class="text-xs text-muted" style="color:var(--muted)">{{ optional($patient->created_at)->format('Y-m-d') }}</div>
        </div>

        <div class="text-xs text-muted mb-1" style="color:var(--muted)">Phone: {{ $patient->phone ?? '—' }}</div>

        <div class="flex gap-2 mt-2">
          <a class="btn-ghost flex-1 text-center" href="{{ route('patients.show', $patient->id) }}" style="color:var(--text)">View</a>
          <a class="btn-ghost flex-1 text-center" href="{{ route('patients.edit', $patient->id) }}" style="color:var(--text)">Edit</a>
        </div>
      </article>
    @empty
      <div class="p-4 text-center text-muted" style="color:var(--muted)">No patients available.</div>
    @endforelse
  </div>

  <div class="p-4 border-t" style="border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
    <div class="text-sm text-muted" style="color:var(--muted)">Page {{ $patients->currentPage() }} of {{ $patients->lastPage() }}</div>
    <div>
      {{ $patients->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('patientSearch');
  const rows = Array.from(document.querySelectorAll('#patients-body tr'));

  function filterPage(q) {
    const term = q.trim().toLowerCase();
    if (!term) {
      rows.forEach(r => r.style.display = '');
      return;
    }
    rows.forEach(r => {
      const name = (r.dataset.name || '');
      const phone = (r.dataset.phone || '');
      const id = (r.dataset.id || '');
      const show = name.includes(term) || phone.includes(term) || id.includes(term);
      r.style.display = show ? '' : 'none';
    });
  }

  if (search) {
    search.addEventListener('input', (e) => {
      filterPage(e.target.value || '');
    });
  }
});
</script>
@endpush
