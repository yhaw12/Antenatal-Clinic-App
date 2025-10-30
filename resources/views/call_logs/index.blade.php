{{-- resources/views/call_logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Call Logs')

@section('content')
<div class="min-h-screen py-10 px-6" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 3%, transparent)); color:var(--text)">

  <div class="max-w-7xl mx-auto" style="color:var(--text)">

    <div class="glass-card rounded-xl p-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow)">

      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold" style="color:var(--text)">Call Logs</h1>
          <p class="text-sm" style="color:var(--muted)">Overview of calls made and appointments not yet called.</p>
        </div>

        <div class="flex items-center gap-3">
          <form method="GET" action="{{ route('call_logs') }}" class="flex items-center gap-2">
            <input
              type="date"
              name="date"
              value="{{ request('date', \Illuminate\Support\Carbon::today()->toDateString()) }}"
              class="rounded-md px-3 py-2"
              aria-label="Filter by date"
              style="background:var(--bg); color:var(--text); border:1px solid var(--border)"
            />
            <select name="period" class="rounded-md px-3 py-2" style="background:var(--bg); color:var(--text); border:1px solid var(--border)">
              <option value="week" {{ ($selectedPeriod ?? request('period')) === 'week' ? 'selected' : '' }}>This week</option>
              <option value="month" {{ ($selectedPeriod ?? request('period')) === 'month' ? 'selected' : '' }}>This month</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg" style="background:var(--brand); color:#fff; box-shadow:var(--shadow)">
              Apply
            </button>
          </form>

          <a href="{{ route('call_logs.create') }}" class="px-4 py-2 rounded-lg" style="background:var(--success); color:#fff; box-shadow:var(--shadow)">
            + New Call Log
          </a>
        </div>
      </div>

      {{-- flash messages --}}
      @if (session('success'))
        <div class="p-4 rounded-lg mb-6" style="background: color-mix(in srgb, var(--success) 10%, transparent); color:var(--success); border:1px solid color-mix(in srgb, var(--success) 20%, transparent)">
          {{ session('success') }}
        </div>
      @endif
      @if (session('error'))
        <div class="p-4 rounded-lg mb-6" style="background: color-mix(in srgb, var(--danger) 10%, transparent); color:var(--danger); border:1px solid color-mix(in srgb, var(--danger) 20%, transparent)">
          {{ session('error') }}
        </div>
      @endif

      {{-- Summary cards for week / month --}}
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-xl shadow-md bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
          <div class="text-xs text-blue-700 dark:text-blue-300">Week ({{ $weekStart->toDateString() }} → {{ $weekEnd->toDateString() }})</div>
          <div class="mt-2 text-xl font-bold text-blue-800 dark:text-blue-200">{{ number_format($callsWeekCount) }}</div>
          <div class="text-sm text-blue-700 dark:text-blue-400">Calls made this week</div>
        </div>

        <div class="p-4 rounded-xl shadow-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800">
          <div class="text-xs text-green-700 dark:text-green-300">Expected (Week)</div>
          <div class="mt-2 text-xl font-bold text-green-800 dark:text-green-200">{{ number_format($apptsWeekCount) }}</div>
          <div class="text-sm text-green-700 dark:text-green-400">Appointments scheduled this week</div>
        </div>

        <div class="p-4 rounded-xl shadow-md bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800">
          <div class="text-xs text-indigo-700 dark:text-indigo-300">Month ({{ $monthStart->toDateString() }} → {{ $monthEnd->toDateString() }})</div>
          <div class="mt-2 text-xl font-bold text-indigo-800 dark:text-indigo-200">{{ number_format($callsMonthCount) }}</div>
          <div class="text-sm text-indigo-700 dark:text-indigo-400">Calls made this month</div>
        </div>

        <div class="p-4 rounded-xl shadow-md bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800">
          <div class="text-xs text-emerald-700 dark:text-emerald-300">Expected (Month)</div>
          <div class="mt-2 text-xl font-bold text-emerald-800 dark:text-emerald-200">{{ number_format($apptsMonthCount) }}</div>
          <div class="text-sm text-emerald-700 dark:text-emerald-400">Appointments scheduled this month</div>
        </div>
      </div>

      {{-- small KPI row for not-called counts --}}
      <div class="flex gap-4 items-center mb-6">
        <div class="flex-1 p-4 rounded-xl shadow-md bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800">
          <div class="text-sm text-amber-700 dark:text-amber-300">Appointments not called (week)</div>
          <div class="text-2xl font-semibold text-amber-800 dark:text-amber-200 mt-1">{{ number_format($notCalledWeekCount) }}</div>
        </div>

        <div class="flex-1 p-4 rounded-xl shadow-md bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800">
          <div class="text-sm text-amber-700 dark:text-amber-300">Appointments not called (month)</div>
          <div class="text-2xl font-semibold text-amber-800 dark:text-amber-200 mt-1">{{ number_format($notCalledMonthCount) }}</div>
        </div>
      </div>


      {{-- Tabs: Calls Made | Appointments Not Called --}}
      <div class="mb-4">
        <nav class="flex gap-2" aria-label="Call tabs">
          <button id="tab-calls-made" class="px-4 py-2 rounded-md" style="background:var(--brand); color:#fff;">Calls Made</button>
          <button id="tab-not-called" class="px-4 py-2 rounded-md" style="background:var(--bg); color:var(--text); border:1px solid var(--border)">Appointments Not Called</button>
        </nav>
      </div>

      {{-- Calls Made table (paginated) --}}
      <div id="calls-made-panel">
        @if ($logs->count() > 0)
          <div class="overflow-x-auto">
            <table class="min-w-full" style="border-collapse:separate; border-spacing:0; width:100%;">
              <thead>
                <tr style="background: color-mix(in srgb, var(--surface) 92%, transparent);">
                  <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Patient</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Result</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Notes</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Called By</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Call Time</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Appointment</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($logs as $log)
                  <tr style="border-top:1px solid var(--border);">
                    <td class="px-4 py-3" style="color:var(--text)">{{ optional($log->patient)->first_name ? (optional($log->patient)->first_name . ' ' . optional($log->patient)->last_name) : ($log->patient?->id ? 'Patient #' . $log->patient->id : 'Unknown') }}</td>

                    <td class="px-4 py-3">
                      @php $result = $log->result @endphp
                      <span class="px-2 py-1 text-xs font-semibold rounded-full" style="
                        background: {{ $result === 'no_answer' ? 'color-mix(in srgb, var(--danger) 10%, transparent)' : ($result === 'rescheduled' ? 'color-mix(in srgb, #f59e0b 10%, transparent)' : ($result === 'will_attend' ? 'color-mix(in srgb, var(--success) 10%, transparent)' : ($result === 'refused' ? 'color-mix(in srgb, var(--muted) 10%, transparent)' : 'color-mix(in srgb, var(--brand) 10%, transparent)')) ) }};
                        color: {{ $result === 'no_answer' ? 'var(--danger)' : ($result === 'rescheduled' ? '#f59e0b' : ($result === 'will_attend' ? 'var(--success)' : ($result === 'refused' ? 'var(--muted)' : 'var(--brand)')) ) }};
                        border:1px solid color-mix(in srgb, currentColor 18%, transparent);
                      ">
                        {{ ucfirst(str_replace('_', ' ', $log->result)) }}
                      </span>
                    </td>

                    <td class="px-4 py-3" style="color:var(--text)">{{ $log->notes ?? '—' }}</td>
                    <td class="px-4 py-3" style="color:var(--text)">{{ $log->called_by ?? 'System' }}</td>
                    <td class="px-4 py-3" style="color:var(--text)">{{ $log->call_time ? \Carbon\Carbon::parse($log->call_time)->format('d M Y, h:i A') : '—' }}</td>
                    <td class="px-4 py-3" style="color:var(--text)">{{ $log->appointment_id ? 'Appt #' . $log->appointment_id : '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="mt-6">
            {{ $logs->links() }}
          </div>
        @else
          <p class="text-center py-8" style="color:var(--muted)">No call logs found for this period.</p>
        @endif
      </div>

      {{-- Appointments not called panel --}}
      <div id="not-called-panel" class="hidden mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div class="card p-4" style="background:var(--surface); border:1px solid var(--border)">
            <div class="text-sm" style="color:var(--muted)">Week not-called ({{ $weekStart->toDateString() }} → {{ $weekEnd->toDateString() }})</div>
            <div class="text-2xl font-semibold mt-2" style="color:var(--text)">{{ number_format($notCalledWeekCount) }}</div>
            <p class="text-sm" style="color:var(--muted)">Showing up to 200 appointments.</p>
          </div>

          <div class="card p-4" style="background:var(--surface); border:1px solid var(--border)">
            <div class="text-sm" style="color:var(--muted)">Month not-called ({{ $monthStart->toDateString() }} → {{ $monthEnd->toDateString() }})</div>
            <div class="text-2xl font-semibold mt-2" style="color:var(--text)">{{ number_format($notCalledMonthCount) }}</div>
            <p class="text-sm" style="color:var(--muted)">Showing up to 200 appointments.</p>
          </div>
        </div>

        <div class="space-y-6">
          <section>
            <h3 class="text-lg font-medium mb-2" style="color:var(--text)">Appointments this week without a call</h3>
            @if ($notCalledAppointmentsWeek->isEmpty())
              <p class="italic" style="color:var(--muted)">All appointments this week have associated calls (or there are no appointments).</p>
            @else
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead style="background: color-mix(in srgb, var(--surface) 92%, transparent);">
                    <tr>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Date</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Time</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Patient</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Phone</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($notCalledAppointmentsWeek as $appt)
                      <tr style="border-top:1px solid var(--border);">
                        <td class="px-4 py-3" style="color:var(--text)">{{ $appt->date }}</td>
                        <td class="px-4 py-3" style="color:var(--text)">{{ $appt->time ?? '—' }}</td>
                        <td class="px-4 py-3" style="color:var(--text)">{{ optional($appt->patient)->first_name ?? 'Patient #' . $appt->patient_id }} {{ optional($appt->patient)->last_name }}</td>
                        <td class="px-4 py-3" style="color:var(--text)">{{ optional($appt->patient)->phone ?? '—' }}</td>
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
            <h3 class="text-lg font-medium mb-2" style="color:var(--text)">Appointments this month without a call</h3>
            @if ($notCalledAppointmentsMonth->isEmpty())
              <p class="italic" style="color:var(--muted)">All appointments this month have associated calls (or there are no appointments).</p>
            @else
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead style="background: color-mix(in srgb, var(--surface) 92%, transparent);">
                    <tr>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Date</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Time</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Patient</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Phone</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold" style="color:var(--muted)">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($notCalledAppointmentsMonth as $appt)
                      <tr style="border-top:1px solid var(--border);">
                        <td class="px-4 py-3" style="color:var(--text)">{{ $appt->date }}</td>
                        <td class="px-4 py-3" style="color:var(--text)">{{ $appt->time ?? '—' }}</td>
                        <td class="px-4 py-3" style="color:var(--text)">{{ optional($appt->patient)->first_name ?? 'Patient #' . $appt->patient_id }} {{ optional($appt->patient)->last_name }}</td>
                        <td class="px-4 py-3" style="color:var(--text)">{{ optional($appt->patient)->phone ?? '—' }}</td>
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
</div>

@push('scripts')
<script>
  // Tabs behavior (small vanilla JS)
  document.addEventListener('DOMContentLoaded', function () {
    const tabCalls = document.getElementById('tab-calls-made');
    const tabNot = document.getElementById('tab-not-called');
    const panelCalls = document.getElementById('calls-made-panel');
    const panelNot = document.getElementById('not-called-panel');

    if (!tabCalls || !tabNot || !panelCalls || !panelNot) return;

    function showCalls() {
      panelCalls.classList.remove('hidden');
      panelNot.classList.add('hidden');
      tabCalls.style.background = 'var(--brand)';
      tabCalls.style.color = '#fff';
      tabNot.style.background = 'var(--bg)';
      tabNot.style.color = 'var(--text)';
    }

    function showNot() {
      panelCalls.classList.add('hidden');
      panelNot.classList.remove('hidden');
      tabNot.style.background = 'var(--brand)';
      tabNot.style.color = '#fff';
      tabCalls.style.background = 'var(--bg)';
      tabCalls.style.color = 'var(--text)';
    }

    tabCalls.addEventListener('click', showCalls);
    tabNot.addEventListener('click', showNot);

    // default
    showCalls();
  });
</script>
@endpush

@endsection
