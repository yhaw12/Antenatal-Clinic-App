{{-- resources/views/call_logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Call Logs')

@section('content')
<div class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-indigo-950 min-h-screen py-10 px-6">
  <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Call Logs</h1>
        <p class="text-sm text-muted">Overview of calls made and appointments not yet called.</p>
      </div>

      <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('call_logs') }}" class="flex items-center gap-2">
          <input
            type="date"
            name="date"
            value="{{ $filterDate ?? request('date') }}"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
            aria-label="Filter by date"
          />
          <select name="period" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 px-3 py-2">
            <option value="week" {{ ($selectedPeriod ?? request('period')) === 'week' ? 'selected' : '' }}>This week</option>
            <option value="month" {{ ($selectedPeriod ?? request('period')) === 'month' ? 'selected' : '' }}>This month</option>
          </select>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
            Apply
          </button>
        </form>

        <a href="{{ route('call_logs.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
          + New Call Log
        </a>
      </div>
    </div>

    {{-- flash messages --}}
    @if (session('success'))
      <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-lg mb-6">
        {{ session('success') }}
      </div>
    @endif
    @if (session('error'))
      <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-lg mb-6">
        {{ session('error') }}
      </div>
    @endif

    {{-- Summary cards for week / month --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
      <div class="card p-4">
        <div class="text-xs text-muted">Week ({{ $weekStart->toDateString() }} → {{ $weekEnd->toDateString() }})</div>
        <div class="mt-2 text-xl font-bold">{{ number_format($callsWeekCount) }}</div>
        <div class="text-sm text-muted">Calls made this week</div>
      </div>

      <div class="card p-4">
        <div class="text-xs text-muted">Expected (Week)</div>
        <div class="mt-2 text-xl font-bold">{{ number_format($apptsWeekCount) }}</div>
        <div class="text-sm text-muted">Appointments scheduled this week</div>
      </div>

      <div class="card p-4">
        <div class="text-xs text-muted">Month ({{ $monthStart->toDateString() }} → {{ $monthEnd->toDateString() }})</div>
        <div class="mt-2 text-xl font-bold">{{ number_format($callsMonthCount) }}</div>
        <div class="text-sm text-muted">Calls made this month</div>
      </div>

      <div class="card p-4">
        <div class="text-xs text-muted">Expected (Month)</div>
        <div class="mt-2 text-xl font-bold">{{ number_format($apptsMonthCount) }}</div>
        <div class="text-sm text-muted">Appointments scheduled this month</div>
      </div>
    </div>

    {{-- small KPI row for not-called counts --}}
    <div class="flex gap-4 items-center mb-6">
      <div class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 flex-1">
        <div class="text-sm text-muted">Appointments not called (week)</div>
        <div class="text-2xl font-semibold">{{ number_format($notCalledWeekCount) }}</div>
      </div>

      <div class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 flex-1">
        <div class="text-sm text-muted">Appointments not called (month)</div>
        <div class="text-2xl font-semibold">{{ number_format($notCalledMonthCount) }}</div>
      </div>
    </div>

    {{-- Tabs: Calls Made | Appointments Not Called --}}
    <div class="mb-4">
      <nav class="flex gap-2" aria-label="Call tabs">
        <button id="tab-calls-made" class="px-4 py-2 rounded-md bg-blue-600 text-white">Calls Made</button>
        <button id="tab-not-called" class="px-4 py-2 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">Appointments Not Called</button>
      </nav>
    </div>

    {{-- Calls Made table (paginated) --}}
    <div id="calls-made-panel">
      @if ($logs->count() > 0)
        <div class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-700">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Patient</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Result</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Notes</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Called By</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Call Time</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Appointment</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              @foreach ($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ optional($log->patient)->first_name ? (optional($log->patient)->first_name . ' ' . optional($log->patient)->last_name) : ($log->patient?->id ? 'Patient #' . $log->patient->id : 'Unknown') }}</td>

                  <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($log->result === 'no_answer') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                        @elseif($log->result === 'rescheduled') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                        @elseif($log->result === 'will_attend') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                        @elseif($log->result === 'refused') bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                        @else bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $log->result)) }}
                    </span>
                  </td>

                  <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $log->notes ?? '—' }}</td>
                  <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $log->called_by ?? 'System' }}</td>
                  <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $log->call_time ? \Carbon\Carbon::parse($log->call_time)->format('d M Y, h:i A') : '—' }}</td>
                  <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $log->appointment_id ? 'Appt #' . $log->appointment_id : '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-6">
          {{ $logs->links() }}
        </div>
      @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-8 italic">No call logs found for this period.</p>
      @endif
    </div>

    {{-- Appointments not called panel --}}
    <div id="not-called-panel" class="hidden">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="card p-4">
          <div class="text-sm text-muted">Week not-called ({{ $weekStart->toDateString() }} → {{ $weekEnd->toDateString() }})</div>
          <div class="text-2xl font-semibold mt-2">{{ number_format($notCalledWeekCount) }}</div>
          <p class="text-sm text-muted mt-1">Showing up to 200 appointments.</p>
        </div>

        <div class="card p-4">
          <div class="text-sm text-muted">Month not-called ({{ $monthStart->toDateString() }} → {{ $monthEnd->toDateString() }})</div>
          <div class="text-2xl font-semibold mt-2">{{ number_format($notCalledMonthCount) }}</div>
          <p class="text-sm text-muted mt-1">Showing up to 200 appointments.</p>
        </div>
      </div>

      <div class="space-y-6">
        <section>
          <h3 class="text-lg font-medium mb-2">Appointments this week without a call</h3>
          @if ($notCalledAppointmentsWeek->isEmpty())
            <p class="text-gray-500 dark:text-gray-400 italic">All appointments this week have associated calls (or there are no appointments).</p>
          @else
            <div class="overflow-x-auto">
              <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Time</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Patient</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Phone</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @foreach ($notCalledAppointmentsWeek as $appt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                      <td class="px-4 py-3">{{ $appt->date }}</td>
                      <td class="px-4 py-3">{{ $appt->time ?? '—' }}</td>
                      <td class="px-4 py-3">{{ optional($appt->patient)->first_name ?? 'Patient #' . $appt->patient_id }} {{ optional($appt->patient)->last_name }}</td>
                      <td class="px-4 py-3">{{ optional($appt->patient)->phone ?? '—' }}</td>
                      <td class="px-4 py-3">
                        <a href="{{ route('call_logs.create', ['appointment_id' => $appt->id]) }}" class="btn-ghost">Log Call</a>
                        <a href="{{ route('patients.show', optional($appt->patient)->id) }}" class="btn-ghost">View Patient</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </section>

        <section>
          <h3 class="text-lg font-medium mb-2">Appointments this month without a call</h3>
          @if ($notCalledAppointmentsMonth->isEmpty())
            <p class="text-gray-500 dark:text-gray-400 italic">All appointments this month have associated calls (or there are no appointments).</p>
          @else
            <div class="overflow-x-auto">
              <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Time</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Patient</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Phone</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @foreach ($notCalledAppointmentsMonth as $appt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                      <td class="px-4 py-3">{{ $appt->date }}</td>
                      <td class="px-4 py-3">{{ $appt->time ?? '—' }}</td>
                      <td class="px-4 py-3">{{ optional($appt->patient)->first_name ?? 'Patient #' . $appt->patient_id }} {{ optional($appt->patient)->last_name }}</td>
                      <td class="px-4 py-3">{{ optional($appt->patient)->phone ?? '—' }}</td>
                      <td class="px-4 py-3">
                        <a href="{{ route('call_logs.create', ['appointment_id' => $appt->id]) }}" class="btn-ghost">Log Call</a>
                        <a href="{{ route('patients.show', optional($appt->patient)->id) }}" class="btn-ghost">View Patient</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </section>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Simple tab switches
  (function () {
    const callsBtn = document.getElementById('tab-calls-made');
    const notCalledBtn = document.getElementById('tab-not-called');
    const callsPanel = document.getElementById('calls-made-panel');
    const notCalledPanel = document.getElementById('not-called-panel');

    function showCalls() {
      callsPanel.classList.remove('hidden');
      notCalledPanel.classList.add('hidden');
      callsBtn.classList.add('bg-blue-600','text-white');
      callsBtn.classList.remove('bg-white','text-gray-700');
      notCalledBtn.classList.remove('bg-blue-600','text-white');
      notCalledBtn.classList.add('bg-white','text-gray-700');
    }
    function showNotCalled() {
      callsPanel.classList.add('hidden');
      notCalledPanel.classList.remove('hidden');
      notCalledBtn.classList.add('bg-blue-600','text-white');
      notCalledBtn.classList.remove('bg-white','text-gray-700');
      callsBtn.classList.remove('bg-blue-600','text-white');
      callsBtn.classList.add('bg-white','text-gray-700');
    }

    callsBtn.addEventListener('click', showCalls);
    notCalledBtn.addEventListener('click', showNotCalled);

    // Restore default based on selectedPeriod (pref: 'week' show not-called)
    // If you prefer default tab, adjust below.
    @if(($selectedPeriod ?? 'week') === 'week')
      showNotCalled();
    @else
      showCalls();
    @endif
  })();
</script>
@endpush
@endsection
