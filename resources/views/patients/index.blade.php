@extends('layouts.app')
@section('title', 'Patients')
@section('page-title', 'Patients')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
  <div>
    <h2 class="text-xl font-semibold text-body" style="color:var(--text)">All Patients</h2>
    <p class="text-sm text-muted" style="color:var(--muted)">Listing of all registered patients.</p>
  </div>

  <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 w-full sm:w-auto">
    <div class="relative w-full sm:w-64">
        <input
          id="patientSearch"
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Search name, phone or ID..."
          class="w-full border border-surface rounded px-3 py-2 bg-app text-body focus:outline-none focus:ring-2 focus:ring-opacity-50"
          aria-label="Search patients"
          style="background:var(--bg); color:var(--text); border:1px solid var(--border); --tw-ring-color: var(--brand);"
        >
        @if(request('search'))
            <a href="{{ url()->current() }}" class="absolute right-2 top-1/2 -translate-y-1/2 text-xs hover:underline" style="color:var(--muted)">Clear</a>
        @endif
    </div>
    {{-- <a href="{{ route('patients.create') }}" class="btn-primary whitespace-nowrap px-4 py-2 rounded" style="background:var(--brand); color:white;">New Patient</a> --}}
  </form>
</div>

<div class="grid grid-cols-1 gap-4 mb-6">
  <div class="card p-4 flex items-center justify-between rounded-lg" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow); color:var(--text)">
    <div>
      <div class="text-sm text-muted mb-1" style="color:var(--muted)">Total Patients</div>
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

<div class="card overflow-hidden rounded-lg" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow); color:var(--text)">

  <div class="hidden md:block overflow-x-auto overflow-y-auto max-h-[70vh] appointments-table">
    <table class="w-full text-left border-collapse" role="table" style="color:var(--text)">
      <thead class="sticky top-0 z-10 shadow-sm">
        <tr style="color:var(--muted); background: var(--bg);">
          <th class="p-3 font-semibold whitespace-nowrap">#</th>
          <th class="p-3 font-semibold whitespace-nowrap">Name</th>
          <th class="p-3 font-semibold whitespace-nowrap">Phone</th>
          <th class="p-3 font-semibold whitespace-nowrap">Hospital / ID</th>
          <th class="p-3 font-semibold whitespace-nowrap">Created</th>
          <th class="p-3 font-semibold whitespace-nowrap text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($patients as $i => $patient)
          <tr class="transition-colors hover:bg-black/5" style="border-bottom: 1px solid var(--border);">
            <td class="p-3 align-middle whitespace-nowrap" style="color:var(--text)">
                {{ $patients->firstItem() + $i }}
            </td>
            <td class="p-3 align-middle">
              <div class="flex flex-col">
                  <a href="{{ route('patients.show', $patient->id) }}" class="font-medium text-sm hover:underline decoration-dotted" style="color:var(--text)">
                    {{ $patient->first_name }} {{ $patient->last_name }}
                  </a>
                  {{-- Folder Number is usually internal/safe, but masking it if it's sensitive --}}
                  <span class="text-xs" style="color:var(--muted)">
                      @if(auth()->user()->hasRole('admin'))
                          {{ $patient->folder_no ?? '' }}
                      @else
                          {{ $patient->folder_no ? Str::mask($patient->folder_no, '*', 2) : '' }}
                      @endif
                  </span>
              </div>
            </td>
            
            {{-- PHONE COLUMN: Masked for non-admins --}}
            <td class="p-3 align-middle text-sm whitespace-nowrap" style="color:var(--text)">
                @if(auth()->user()->hasRole('admin'))
                    {{ $patient->phone ?? '—' }}
                @else
                    {{-- Shows 020 **** 123 --}}
                    <span class="opacity-70 select-none cursor-not-allowed" title="Hidden for privacy">
                        {{ $patient->phone ? Str::mask($patient->phone, '*', 3, 4) : '—' }}
                    </span>
                @endif
            </td>

            {{-- ID COLUMN: Masked for non-admins --}}
            <td class="p-3 align-middle text-sm whitespace-nowrap" style="color:var(--text)">
                @if(auth()->user()->hasRole('admin'))
                    {{ $patient->hospital_number ?? $patient->id_number ?? '—' }}
                @else
                    {{-- Shows ****A123 --}}
                    @php $val = $patient->hospital_number ?? $patient->id_number; @endphp
                    <span class="opacity-70 select-none cursor-not-allowed" title="Hidden for privacy">
                        {{ $val ? Str::mask($val, '*', 0, max(0, strlen($val) - 4)) : '—' }}
                    </span>
                @endif
            </td>

            <td class="p-3 align-middle text-sm whitespace-nowrap" style="color:var(--text)">
                {{ optional($patient->created_at)->format('Y-m-d') }}
            </td>
            <td class="p-3 align-middle text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-2">
                <a class="px-3 py-1 text-xs rounded hover:opacity-80 transition-opacity" href="{{ route('patients.show', $patient->id) }}" style="background:transparent; border:1px solid var(--border); color:var(--text)">View</a>
                
                {{-- Only Admin/Midwife usually edit, but if others can, let them click --}}
                <a class="px-3 py-1 text-xs rounded hover:opacity-80 transition-opacity" href="{{ route('patients.edit', $patient->id) }}" style="background:transparent; border:1px solid var(--border); color:var(--text)">Edit</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-8 text-center">
                <div class="flex flex-col items-center justify-center text-muted" style="color:var(--muted)">
                    <p class="text-lg">No patients found</p>
                </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div id="patients-cards" class="md:hidden divide-y" style="border-color: var(--border)">
    @forelse ($patients as $patient)
      <article class="p-4 hover:bg-black/5 transition-colors">
        <div class="flex justify-between items-start mb-2">
          <div>
            <a href="{{ route('patients.show', $patient->id) }}" class="text-base font-semibold block mb-1 hover:underline" style="color:var(--text)">
                {{ $patient->first_name }} {{ $patient->last_name }}
            </a>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-xs" style="background: color-mix(in srgb, var(--text) 10%, transparent); color:var(--text)">
                @if(auth()->user()->hasRole('admin'))
                    {{ $patient->hospital_number ?? $patient->id_number ?? 'No ID' }}
                @else
                    @php $val = $patient->hospital_number ?? $patient->id_number; @endphp
                    {{ $val ? Str::mask($val, '*', 0, max(0, strlen($val) - 3)) : 'No ID' }}
                @endif
            </div>
          </div>
          <span class="text-xs" style="color:var(--muted)">
            {{ optional($patient->created_at)->format('M d, Y') }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-2 mb-4 text-sm" style="color:var(--muted)">
            <div>
                <span class="block text-xs opacity-70">Phone</span>
                <span style="color:var(--text)">
                    @if(auth()->user()->hasRole('admin'))
                        {{ $patient->phone ?? '—' }}
                    @else
                        {{ $patient->phone ? Str::mask($patient->phone, '*', 3, 4) : '—' }}
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-xs opacity-70">Folder No</span>
                <span style="color:var(--text)">
                    @if(auth()->user()->hasRole('admin'))
                        {{ $patient->folder_no ?? '—' }}
                    @else
                        {{ $patient->folder_no ? Str::mask($patient->folder_no, '*', 0, 2) : '—' }}
                    @endif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-3">
          <a class="py-2 text-center text-sm rounded border" 
             href="{{ route('patients.show', $patient->id) }}" 
             style="border-color:var(--border); color:var(--text)">
             View Details
          </a>
          <a class="py-2 text-center text-sm rounded border" 
             href="{{ route('patients.edit', $patient->id) }}" 
             style="border-color:var(--border); color:var(--text)">
             Edit
          </a>
        </div>
      </article>
    @empty
      <div class="p-8 text-center text-muted" style="color:var(--muted)">No patients available.</div>
    @endforelse
  </div>

  <div class="p-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4" style="border-top:1px solid var(--border);">
    <div class="text-sm text-muted" style="color:var(--muted)">
        Page {{ $patients->currentPage() }} of {{ $patients->lastPage() }}
    </div>
    <div class="w-full sm:w-auto overflow-x-auto">
        {{ $patients->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit search debounce
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('patientSearch');
    let timeout = null;

    if (searchInput) {
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;

        searchInput.addEventListener('input', function (e) {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                searchInput.closest('form').submit();
            }, 600); 
        });
    }
});
</script>
@endpush