{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
@php
  // Defensive defaults so template never throws undefined variable errors
  $from = $from ?? now()->format('Y-m-d');
  $to = $to ?? now()->format('Y-m-d');
  $status = $status ?? '';
  $kpis = $kpis ?? ['total' => 0, 'present' => 0, 'notArrived' => 0];
  $chart = $chart ?? ['labels' => [], 'counts' => []];
  $comparison = $comparison ?? null;
  $appts = $appts ?? collect();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" style="color:var(--text); background:transparent;">
  {{-- Toolbar / Hero --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold" style="color:var(--text)">Appointment Reports</h1>
      <p class="mt-1 text-sm" style="color:var(--muted)">Generate, compare and export appointment data.</p>
    </div>

    <div class="flex items-center gap-3">
      <form id="reportFilters" action="{{ route('reports.generate') }}" method="GET" class="flex items-center gap-2">
        <input type="date" name="from" value="{{ $from }}" class="h-10 px-3 rounded-lg text-sm" style="background:var(--bg); color:var(--text); border:1px solid var(--border)"/>
        <input type="date" name="to" value="{{ $to }}" class="h-10 px-3 rounded-lg text-sm" style="background:var(--bg); color:var(--text); border:1px solid var(--border)"/>
        <select name="status" class="h-10 px-3 rounded-lg text-sm" style="background:var(--bg); color:var(--text); border:1px solid var(--border)">
          <option value="" {{ $status === '' ? 'selected' : '' }}>All</option>
          <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
          <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
          <option value="missed" {{ $status === 'missed' ? 'selected' : '' }}>Missed</option>
          <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg text-sm shadow" style="background:var(--brand); color:#fff;">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:inherit"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Generate
        </button>
      </form>

      {{-- Export button (simple dropdown) --}}
      <div class="relative">
        <button id="exportToggle" type="button" aria-expanded="false" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg text-sm shadow" style="background:var(--success); color:#fff;">
          Export
          <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="color:inherit"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06-.02L10 10.69l3.71-3.51a.75.75 0 011.02 1.1l-4.2 4a.75.75 0 01-1.02 0l-4.2-4a.75.75 0 01-.02-1.06z" clip-rule="evenodd"/></svg>
        </button>

        <div id="exportMenu" class="hidden absolute right-0 mt-2 w-44" style="background:var(--surface); border:1px solid var(--border); border-radius:0.5rem; box-shadow:var(--shadow); z-index:20;">
          <form id="exportCsvForm" action="{{ route('exports.queue') }}" method="POST" class="px-3 py-1">
            @csrf
            <input type="hidden" name="from" value="{{ $from }}">
            <input type="hidden" name="to" value="{{ $to }}">
            <input type="hidden" name="format" value="csv">
            <button type="submit" class="w-full text-left text-sm px-2 py-2 rounded" style="color:var(--text);">CSV (.csv)</button>
          </form>
          <form id="exportExcelForm" action="{{ route('exports.queue') }}" method="POST" class="px-3 py-1">
            @csrf
            <input type="hidden" name="from" value="{{ $from }}">
            <input type="hidden" name="to" value="{{ $to }}">
            <input type="hidden" name="format" value="excel">
            <button type="submit" class="w-full text-left text-sm px-2 py-2 rounded" style="color:var(--text);">Excel (.xlsx)</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- KPI cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="p-5 rounded-xl shadow flex items-center gap-4" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: color-mix(in srgb, var(--brand) 8%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--brand)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <div>
        <div class="text-sm" style="color:var(--muted)">Total appointments</div>
        <div class="text-2xl font-semibold" style="color:var(--text)">{{ number_format($kpis['total']) }}</div>
      </div>
    </div>

    <div class="p-5 rounded-xl shadow flex items-center gap-4" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: color-mix(in srgb, var(--success) 8%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--success)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <div class="text-sm" style="color:var(--muted)">Patients present</div>
        <div class="text-2xl font-semibold" style="color:var(--text)">{{ number_format($kpis['present']) }}</div>
      </div>
    </div>

    <div class="p-5 rounded-xl shadow flex items-center gap-4" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: color-mix(in srgb, var(--danger) 8%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--danger)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <div class="text-sm" style="color:var(--muted)">Missed appointments</div>
        <div class="text-2xl font-semibold" style="color:var(--text)">{{ number_format($kpis['notArrived']) }}</div>
      </div>
    </div>
  </div>

  {{-- segmented control for Chart / Table / Comparison --}}
  <div class="mb-5">
    <div class="inline-flex rounded-lg p-1" style="background: color-mix(in srgb, var(--surface) 92%, transparent); border:1px solid var(--border);">
      <button id="seg-chart" class="px-4 py-2 rounded-md text-sm font-medium" style="background:var(--bg); color:var(--text);">Chart</button>
      <button id="seg-table" class="px-4 py-2 rounded-md text-sm font-medium" style="background:transparent; color:var(--muted);">Table</button>
      @if($comparison)
        <button id="seg-compare" class="px-4 py-2 rounded-md text-sm font-medium" style="background:transparent; color:var(--muted);">Comparison</button>
      @endif
    </div>
  </div>

  {{-- panels --}}
  <div>
    {{-- Chart panel --}}
    <div id="panel-chart" class="mb-6">
      <div class="rounded-xl p-4" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
        @if(empty($chart['labels']) && !$comparison)
          <div class="text-center py-12" style="color:var(--muted)">No chart data for the selected range.</div>
        @else
          <canvas id="appointmentChart" class="w-full" height="220"></canvas>
        @endif
      </div>
    </div>

    {{-- Table panel --}}
    <div id="panel-table" class="hidden mb-6">
      <div class="rounded-xl p-4" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow); overflow-x:auto;">
        <table class="min-w-full" style="border-collapse:collapse;">
          <thead style="background: color-mix(in srgb, var(--bg) 98%, transparent); position:sticky; top:0;">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold" style="color:var(--muted)">Date</th>
              <th class="px-4 py-3 text-left text-xs font-semibold" style="color:var(--muted)">Time</th>
              <th class="px-4 py-3 text-left text-xs font-semibold" style="color:var(--muted)">Patient</th>
              <th class="px-4 py-3 text-left text-xs font-semibold" style="color:var(--muted)">Status</th>
              <th class="px-4 py-3 text-right text-xs font-semibold" style="color:var(--muted)">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($appts as $appt)
              <tr style="border-top:1px solid var(--border);">
                <td class="px-4 py-3 text-sm" style="color:var(--text)">{{ $appt->date }}</td>
                <td class="px-4 py-3 text-sm" style="color:var(--text)">{{ $appt->time ?? '—' }}</td>
                <td class="px-4 py-3 text-sm" style="color:var(--text)">{{ optional($appt->patient)->first_name ?? 'Patient #' . $appt->patient_id }} {{ optional($appt->patient)->last_name }}</td>
                <td class="px-4 py-3 text-sm">
                  @php $s = $appt->status ?? 'scheduled' @endphp
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="
                    background: {{ $s === 'missed' ? 'color-mix(in srgb, var(--danger) 10%, transparent)' : ($s === 'present' || $s === 'queued' ? 'color-mix(in srgb, var(--success) 10%, transparent)' : 'color-mix(in srgb, var(--brand) 10%, transparent)') }};
                    color: {{ $s === 'missed' ? 'var(--danger)' : ($s === 'present' || $s === 'queued' ? 'var(--success)' : 'var(--brand)') }};
                    ">
                    {{ ucfirst($s) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right text-sm">
                  <a href="{{ route('patients.show', optional($appt->patient)->id) }}" style="color:var(--accent); margin-right:12px;">Patient</a>
                  <a href="{{ route('appointments.index', ['date' => $appt->date]) }}" style="color:var(--muted)">Appointments</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-6 text-center text-sm" style="color:var(--muted)">No appointments in this range.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="mt-4">
          @if(method_exists($appts, 'links'))
            {{ $appts->links() }}
          @endif
        </div>
      </div>
    </div>

    {{-- Comparison panel --}}
    @if($comparison)
      <div id="panel-compare" class="hidden mb-6">
        <div class="rounded-xl p-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow); display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
          <div class="space-y-4">
            <h3 class="text-sm" style="color:var(--muted)">Comparison summary</h3>
            <p class="text-sm" style="color:var(--text)">{{ $comparison['change_summary'] ?? 'Comparison data' }}</p>
            <div class="grid grid-cols-3 gap-3">
              <div class="p-3 rounded-lg text-center" style="background: color-mix(in srgb, var(--bg) 96%, transparent); border:1px solid var(--border);">
                <div class="text-xs" style="color:var(--muted)">Month 1</div>
                <div class="text-lg font-semibold" style="color:var(--text)">{{ $comparison['month1_data']['total'] ?? 0 }}</div>
              </div>
              <div class="p-3 rounded-lg text-center" style="background: color-mix(in srgb, var(--bg) 96%, transparent); border:1px solid var(--border);">
                <div class="text-xs" style="color:var(--muted)">Month 2</div>
                <div class="text-lg font-semibold" style="color:var(--text)">{{ $comparison['month2_data']['total'] ?? 0 }}</div>
              </div>
              <div class="p-3 rounded-lg text-center" style="background: color-mix(in srgb, var(--bg) 96%, transparent); border:1px solid var(--border);">
                <div class="text-xs" style="color:var(--muted)">Change</div>
                @php
                  $m1 = $comparison['month1_data']['total'] ?? 0;
                  $m2 = $comparison['month2_data']['total'] ?? 0;
                  $diff = $m2 - $m1;
                  $pct = ($m1 > 0) ? round(($diff / $m1) * 100) : ($m2>0?100:0);
                @endphp
                <div class="text-lg font-semibold" style="color:var(--text)">{{ $diff >=0 ? '+' : '' }}{{ $diff }} ({{ $pct }}%)</div>
              </div>
            </div>
          </div>

          <div>
            <canvas id="compareChart" height="180"></canvas>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@push('scripts')
<script>
  // small export menu toggle to avoid Alpine dependency
  document.addEventListener('DOMContentLoaded', () => {
    const exportToggle = document.getElementById('exportToggle');
    const exportMenu = document.getElementById('exportMenu');
    if (exportToggle && exportMenu) {
      exportToggle.addEventListener('click', (e) => {
        const open = exportMenu.classList.contains('hidden') === false;
        exportMenu.classList.toggle('hidden', open);
        exportToggle.setAttribute('aria-expanded', (!open).toString());
      });
      document.addEventListener('click', (e) => {
        if (!exportMenu.contains(e.target) && !exportToggle.contains(e.target)) {
          exportMenu.classList.add('hidden');
          exportToggle.setAttribute('aria-expanded', 'false');
        }
      });
    }
  });

  // Segmented control
  (function () {
    const segChart = document.getElementById('seg-chart');
    const segTable = document.getElementById('seg-table');
    const segCompare = document.getElementById('seg-compare');
    const panelChart = document.getElementById('panel-chart');
    const panelTable = document.getElementById('panel-table');
    const panelCompare = document.getElementById('panel-compare');

    function clearSelected() {
      [segChart, segTable, segCompare].forEach(b => {
        if (!b) return;
        b.style.background = 'transparent';
        b.style.color = 'var(--muted)';
      });
    }

    function activate(button) {
      clearSelected();
      if (button) {
        button.style.background = 'var(--bg)';
        button.style.color = 'var(--brand)';
      }
    }

    segChart && segChart.addEventListener('click', () => { panelChart.classList.remove('hidden'); panelTable.classList.add('hidden'); panelCompare && panelCompare.classList.add('hidden'); activate(segChart); });
    segTable && segTable.addEventListener('click', () => { panelChart.classList.add('hidden'); panelTable.classList.remove('hidden'); panelCompare && panelCompare.classList.add('hidden'); activate(segTable); });
    segCompare && segCompare.addEventListener('click', () => { panelChart.classList.add('hidden'); panelTable.classList.add('hidden'); panelCompare && panelCompare.classList.remove('hidden'); activate(segCompare); });

    // default pick Chart
    activate(segChart);
  })();

  // Chart rendering
  (function () {
    const chartLabels = {!! json_encode($chart['labels'] ?? []) !!};
    const chartCounts = {!! json_encode($chart['counts'] ?? []) !!};
    const comparison = {!! json_encode($comparison ? $comparison : null) !!};

    // helper to read CSS variable or fallback
    function cssVar(name, fallback) {
      const val = getComputedStyle(document.documentElement).getPropertyValue(name);
      return (val && val.trim()) ? val.trim() : fallback;
    }

    if (comparison && document.getElementById('compareChart')) {
      const ctxC = document.getElementById('compareChart').getContext('2d');
      const labels = comparison.chart_labels || [];
      const datasets = (comparison.chart_datasets || []).map((ds, i) => ({
        label: ds.label,
        data: ds.data,
        backgroundColor: ds.backgroundColor || cssVar('--brand', 'rgba(59,130,246,0.6)'),
      }));

      new Chart(ctxC, {
        type: 'bar',
        data: { labels, datasets },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
      });
    }

    if ((chartLabels && chartLabels.length) && document.getElementById('appointmentChart')) {
      const ctx = document.getElementById('appointmentChart').getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: chartLabels,
          datasets: [{
            label: 'Appointments',
            data: chartCounts,
            borderColor: cssVar('--brand', 'rgb(79,70,229)'),
            backgroundColor: cssVar('--brand', 'rgba(99,102,241,0.08)'),
            tension: 0.25,
            pointRadius: 3,
            pointHoverRadius: 5,
            fill: true
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true } }
        }
      });
    }
  })();
</script>
@endpush

@endsection
