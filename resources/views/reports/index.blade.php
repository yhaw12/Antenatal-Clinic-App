@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
@php
  // Defensive defaults
  $from = $from ?? now()->format('Y-m-d');
  $to = $to ?? now()->format('Y-m-d');
  $status = $status ?? '';
  $kpis = $kpis ?? ['total' => 0, 'present' => 0, 'notArrived' => 0, 'new' => 0, 'review' => 0];
  $chart = $chart ?? ['labels' => [], 'counts' => []];
  $callStats = $callStats ?? [];
  $comparison = $comparison ?? null;
  $appts = $appts ?? collect();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" style="color:var(--text); background:transparent;">
  
  {{-- Header & Toolbar --}}
  <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-8">
    <div>
      <h1 class="text-2xl font-extrabold" style="color:var(--text)">Appointment Reports</h1>
      <p class="mt-1 text-sm" style="color:var(--muted)">Analyze clinic performance, attendance, and workload.</p>
    </div>

    <div class="flex flex-col items-end gap-2">
      {{-- Quick Filters --}}
      <div class="flex gap-2">
        <button type="button" onclick="setDateRange('{{ now()->startOfMonth()->toDateString() }}', '{{ now()->endOfMonth()->toDateString() }}')" class="text-xs px-2 py-1 rounded border hover:opacity-80 transition" style="border-color:var(--border); color:var(--muted); background:var(--surface)">This Month</button>
        <button type="button" onclick="setDateRange('{{ now()->subMonth()->startOfMonth()->toDateString() }}', '{{ now()->subMonth()->endOfMonth()->toDateString() }}')" class="text-xs px-2 py-1 rounded border hover:opacity-80 transition" style="border-color:var(--border); color:var(--muted); background:var(--surface)">Last Month</button>
        <button type="button" onclick="setDateRange('{{ now()->subDays(7)->toDateString() }}', '{{ now()->toDateString() }}')" class="text-xs px-2 py-1 rounded border hover:opacity-80 transition" style="border-color:var(--border); color:var(--muted); background:var(--surface)">Last 7 Days</button>
      </div>

      <div class="flex items-center gap-3">
        <form id="reportFilters" action="{{ route('reports.generate') }}" method="GET" class="flex items-center gap-2">
          <input type="date" name="from" value="{{ $from }}" class="h-10 px-3 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-brand" style="background:var(--bg); color:var(--text); border:1px solid var(--border)"/>
          <span style="color:var(--muted)">to</span>
          <input type="date" name="to" value="{{ $to }}" class="h-10 px-3 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-brand" style="background:var(--bg); color:var(--text); border:1px solid var(--border)"/>
          
          <select name="status" class="h-10 px-3 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-brand" style="background:var(--bg); color:var(--text); border:1px solid var(--border)">
            <option value="" {{ $status === '' ? 'selected' : '' }}>All Status</option>
            <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
            <option value="missed" {{ $status === 'missed' ? 'selected' : '' }}>Missed</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>

          <button type="submit" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg text-sm font-bold shadow hover:brightness-110 transition-all" style="background:var(--brand); color:#fff;">
            Generate
          </button>
        </form>

        {{-- Export --}}
        <div class="relative">
          <button id="exportToggle" type="button" aria-expanded="false" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg text-sm font-bold shadow hover:brightness-110 transition-all" style="background:var(--success); color:#fff;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
          </button>
          <div id="exportMenu" class="hidden absolute right-0 mt-2 w-44 rounded-xl shadow-xl z-20 overflow-hidden" style="background:var(--surface); border:1px solid var(--border);">
            <form action="{{ route('exports.queue') }}" method="POST">
              @csrf
              <input type="hidden" name="from" value="{{ $from }}"><input type="hidden" name="to" value="{{ $to }}">
              <button name="format" value="csv" class="w-full text-left px-4 py-3 text-sm hover:bg-black/5 transition-colors" style="color:var(--text)">CSV (.csv)</button>
              <button name="format" value="excel" class="w-full text-left px-4 py-3 text-sm hover:bg-black/5 transition-colors border-t" style="color:var(--text); border-color:var(--border)">Excel (.xlsx)</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 1. Main KPI Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: color-mix(in srgb, var(--brand) 10%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--brand)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <div>
        <div class="text-xs uppercase font-bold tracking-wide" style="color:var(--muted)">Total Appts</div>
        <div class="text-2xl font-black" style="color:var(--text)">{{ number_format($kpis['total']) }}</div>
      </div>
    </div>

    <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: color-mix(in srgb, var(--success) 10%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--success)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <div class="text-xs uppercase font-bold tracking-wide" style="color:var(--muted)">Present</div>
        <div class="text-2xl font-black" style="color:var(--text)">{{ number_format($kpis['present']) }}</div>
      </div>
    </div>

    <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: color-mix(in srgb, var(--accent) 10%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--accent)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
      </div>
      <div>
        <div class="text-xs uppercase font-bold tracking-wide" style="color:var(--muted)">Attendance Rate</div>
        @php $rate = ($kpis['total'] > 0) ? round(($kpis['present'] / $kpis['total']) * 100, 1) : 0; @endphp
        <div class="text-2xl font-black" style="color:var(--text)">{{ $rate }}<span class="text-sm font-medium" style="color:var(--muted)">%</span></div>
      </div>
    </div>

    <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1" style="background:var(--surface); border:1px solid var(--border);">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: color-mix(in srgb, var(--danger) 10%, transparent);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--danger)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <div class="text-xs uppercase font-bold tracking-wide" style="color:var(--muted)">Missed</div>
        <div class="text-2xl font-black" style="color:var(--text)">{{ number_format($kpis['notArrived']) }}</div>
      </div>
    </div>
  </div>

  {{-- 2. Daily Trends Chart --}}
  <div class="mb-6">
    <div class="rounded-2xl p-6 shadow-sm" style="background:var(--surface); border:1px solid var(--border);">
      <h3 class="text-sm font-bold mb-4" style="color:var(--text)">Daily Appointments Trend</h3>
      @if(empty($chart['labels']) && !$comparison)
        <div class="text-center py-12" style="color:var(--muted)">No data available for this range.</div>
      @else
        <div class="relative h-64 w-full">
            <canvas id="appointmentChart"></canvas>
        </div>
      @endif
    </div>
  </div>

  {{-- 3. Insights Row (New vs Review & Call Stats) --}}
  @if(!$comparison)
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      
      <div class="rounded-2xl p-6 shadow-sm" style="background:var(--surface); border:1px solid var(--border);">
          <h3 class="text-sm font-bold mb-6 flex items-center gap-2" style="color:var(--text)">
              <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              Clinical Workload
          </h3>
          <div class="flex items-center gap-8">
              <div class="w-32 h-32 relative">
                  <canvas id="workloadChart"></canvas>
                  <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
                      <span class="text-2xl font-bold" style="color:var(--text)">{{ $kpis['total'] }}</span>
                  </div>
              </div>
              <div class="flex-1 space-y-4">
                  <div>
                      <div class="flex justify-between text-xs mb-1">
                          <span style="color:var(--muted)">New Patients</span>
                          <span class="font-bold" style="color:var(--brand)">{{ $kpis['new'] ?? 0 }}</span>
                      </div>
                      <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                          <div class="h-full rounded-full" style="background:var(--brand); width: {{ $kpis['total'] ? ($kpis['new']/$kpis['total'])*100 : 0 }}%"></div>
                      </div>
                      <div class="text-[10px] mt-1 opacity-60" style="color:var(--text)">Est. 30 mins per visit</div>
                  </div>
                  <div>
                      <div class="flex justify-between text-xs mb-1">
                          <span style="color:var(--muted)">Reviews</span>
                          <span class="font-bold" style="color:var(--success)">{{ $kpis['review'] ?? 0 }}</span>
                      </div>
                      <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                          <div class="h-full rounded-full" style="background:var(--success); width: {{ $kpis['total'] ? ($kpis['review']/$kpis['total'])*100 : 0 }}%"></div>
                      </div>
                      <div class="text-[10px] mt-1 opacity-60" style="color:var(--text)">Est. 10 mins per visit</div>
                  </div>
              </div>
          </div>
      </div>

      <div class="rounded-2xl p-6 shadow-sm" style="background:var(--surface); border:1px solid var(--border);">
          <h3 class="text-sm font-bold mb-6 flex items-center gap-2" style="color:var(--text)">
              <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              Call Outcomes
          </h3>
          @if(empty($callStats))
              <div class="h-32 flex flex-col items-center justify-center text-sm" style="color:var(--muted)">
                  <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                  No calls logged in this period
              </div>
          @else
              <div class="flex items-center gap-8">
                  <div class="w-32 h-32 relative">
                      <canvas id="callChart"></canvas>
                  </div>
                  <div class="flex-1 space-y-2 text-sm">
                      @foreach($callStats as $res => $count)
                          <div class="flex justify-between items-center p-2 rounded hover:bg-black/5 transition-colors">
                              <span class="capitalize" style="color:var(--muted)">{{ str_replace('_', ' ', $res) }}</span>
                              <span class="font-bold px-2 py-0.5 rounded text-xs" style="background:var(--bg); color:var(--text); border:1px solid var(--border)">{{ $count }}</span>
                          </div>
                      @endforeach
                  </div>
              </div>
          @endif
      </div>
  </div>
  @endif

  {{-- 4. Detailed List / Table --}}
  <div class="rounded-2xl shadow-sm overflow-hidden" style="background:var(--surface); border:1px solid var(--border);">
    <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color:var(--border);">
        <h3 class="font-bold text-sm" style="color:var(--text)">Detailed List</h3>
        <span class="text-xs px-2 py-1 rounded" style="background:var(--bg); color:var(--muted)">{{ $appts->total() }} records</span>
    </div>
    
    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm whitespace-nowrap">
        <thead class="uppercase tracking-wider border-b" style="background: color-mix(in srgb, var(--bg) 50%, transparent); border-color:var(--border); color:var(--muted); font-size:11px;">
          <tr>
            <th class="px-6 py-3 font-semibold">Date & Time</th>
            <th class="px-6 py-3 font-semibold">Patient</th>
            <th class="px-6 py-3 font-semibold">Status</th>
            <th class="px-6 py-3 font-semibold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y" style="divide-color:var(--border);">
          @forelse($appts as $appt)
            <tr class="hover:bg-black/5 transition-colors">
              <td class="px-6 py-3">
                <div class="font-medium" style="color:var(--text)">{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}</div>
                <div class="text-xs" style="color:var(--muted)">{{ $appt->time ? \Carbon\Carbon::parse($appt->time)->format('h:i A') : 'TBD' }}</div>
              </td>
              <td class="px-6 py-3">
                <div class="font-medium" style="color:var(--text)">{{ optional($appt->patient)->first_name ?? 'Unknown' }} {{ optional($appt->patient)->last_name }}</div>
                <div class="text-xs" style="color:var(--muted)">{{ optional($appt->patient)->phone ?? 'No phone' }}</div>
              </td>
              <td class="px-6 py-3">
                @php $s = $appt->status ?? 'scheduled' @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize" style="
                  background: {{ $s === 'missed' ? 'color-mix(in srgb, var(--danger) 10%, transparent)' : ($s === 'present' || $s === 'seen' ? 'color-mix(in srgb, var(--success) 10%, transparent)' : 'color-mix(in srgb, var(--brand) 10%, transparent)') }};
                  color: {{ $s === 'missed' ? 'var(--danger)' : ($s === 'present' || $s === 'seen' ? 'var(--success)' : 'var(--brand)') }};
                ">
                  {{ $s }}
                </span>
              </td>
              <td class="px-6 py-3 text-right">
                <a href="{{ route('patients.show', optional($appt->patient)->id) }}" class="text-blue-500 hover:underline text-xs font-medium">View Profile</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-12 text-center text-gray-500">No appointments found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    @if($appts->hasPages())
    <div class="px-6 py-4 border-t" style="border-color:var(--border)">
      {{ $appts->links() }}
    </div>
    @endif
  </div>

</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('scripts')
<script>
  // Quick Filter Logic
  function setDateRange(start, end) {
      document.querySelector('input[name="from"]').value = start;
      document.querySelector('input[name="to"]').value = end;
      document.getElementById('reportFilters').submit();
  }

  // Export Menu Toggle
  document.addEventListener('DOMContentLoaded', () => {
    const exportToggle = document.getElementById('exportToggle');
    const exportMenu = document.getElementById('exportMenu');
    if (exportToggle && exportMenu) {
      exportToggle.addEventListener('click', () => exportMenu.classList.toggle('hidden'));
      document.addEventListener('click', (e) => {
        if (!exportMenu.contains(e.target) && !exportToggle.contains(e.target)) {
          exportMenu.classList.add('hidden');
        }
      });
    }
  });

  // Chart Rendering
  (function () {
    const cssVar = (name, fallback) => {
      const val = getComputedStyle(document.documentElement).getPropertyValue(name);
      return val ? val.trim() : fallback;
    };

    // 1. Main Line Chart
    const lineCtx = document.getElementById('appointmentChart')?.getContext('2d');
    const lineData = {!! json_encode($chart) !!};
    if (lineCtx && lineData.labels.length) {
      new Chart(lineCtx, {
        type: 'line',
        data: {
          labels: lineData.labels,
          datasets: [{
            label: 'Appointments',
            data: lineData.counts,
            borderColor: cssVar('--brand', '#3b82f6'),
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4] } }, x: { grid: { display: false } } }
        }
      });
    }

    // 2. Workload Doughnut
    const workloadCtx = document.getElementById('workloadChart')?.getContext('2d');
    if (workloadCtx) {
        new Chart(workloadCtx, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Review'],
                datasets: [{
                    data: [{{ $kpis['new'] ?? 0 }}, {{ $kpis['review'] ?? 0 }}],
                    backgroundColor: [cssVar('--brand', '#3b82f6'), cssVar('--success', '#10b981')],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }, 
                cutout: '75%' 
            }
        });
    }

    // 3. Call Stats Doughnut
    const callCtx = document.getElementById('callChart')?.getContext('2d');
    if (callCtx) {
        const callData = {!! json_encode($callStats) !!};
        const labels = Object.keys(callData).map(s => s.replace(/_/g, ' '));
        const data = Object.values(callData);
        
        new Chart(callCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#6b7280'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }, 
                cutout: '75%' 
            }
        });
    }
  })();
</script>
@endpush
@endsection