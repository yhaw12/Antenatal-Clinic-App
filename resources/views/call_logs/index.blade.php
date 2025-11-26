@extends('layouts.app')

@section('title', 'Call Logs')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 bg-app text-body">

  <div class="max-w-7xl mx-auto space-y-6">

    <div class="card p-6 bg-surface border border-border rounded-xl shadow-sm">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-body">Call Logs</h1>
          <p class="text-sm text-muted mt-1">Track patient outreach and pending appointment confirmations.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <form method="GET" action="{{ route('call_logs') }}" class="flex items-center gap-2 bg-gray-50 dark:bg-white/5 p-1 rounded-lg border border-border">
            <input
              type="date"
              name="date"
              value="{{ request('date', now()->toDateString()) }}"
              class="rounded-md px-3 py-1.5 bg-transparent border-none text-sm focus:ring-0 text-body"
              aria-label="Filter by date"
            />
            <div class="h-6 w-px bg-border"></div>
            <select name="period" class="rounded-md px-3 py-1.5 bg-transparent border-none text-sm focus:ring-0 text-body cursor-pointer">
              <option value="week" {{ request('period', 'week') === 'week' ? 'selected' : '' }}>This Week</option>
              <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>This Month</option>
            </select>
            <button type="submit" class="p-1.5 text-brand hover:bg-brand/10 rounded-md transition-colors" title="Apply Filters">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
          </form>

          <a href="{{ route('call_logs.create') }}" class="btn-primary shadow-lg shadow-brand/20 flex items-center gap-2 px-4 py-2 rounded-lg bg-brand text-white hover:bg-brand/90 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Log Call</span>
          </a>
        </div>
      </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
      <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
      </div>
    @endif

    {{-- KPI Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      
      <div class="bg-surface p-4 rounded-xl shadow-sm border border-border relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
        <div class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1">Calls (Week)</div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-body">{{ number_format($callsWeekCount) }}</span>
            <span class="text-xs text-muted">total</span>
        </div>
        <div class="text-xs text-muted mt-2 truncate">{{ $weekStart->format('d M') }} - {{ $weekEnd->format('d M') }}</div>
      </div>

      <div class="bg-surface p-4 rounded-xl shadow-sm border border-border relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
        <div class="text-xs font-bold uppercase tracking-wider text-green-600 dark:text-green-400 mb-1">Expected (Week)</div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-body">{{ number_format($apptsWeekCount) }}</span>
            <span class="text-xs text-muted">appts</span>
        </div>
        <div class="text-xs text-muted mt-2 truncate">{{ $weekStart->format('d M') }} - {{ $weekEnd->format('d M') }}</div>
      </div>

      <div class="bg-surface p-4 rounded-xl shadow-sm border border-border relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
        <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1">Calls (Month)</div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-body">{{ number_format($callsMonthCount) }}</span>
            <span class="text-xs text-muted">total</span>
        </div>
        <div class="text-xs text-muted mt-2 truncate">{{ $monthStart->format('F Y') }}</div>
      </div>

      <div class="bg-surface p-4 rounded-xl shadow-sm border border-border relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
        <div class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1">Pending Actions</div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-body">{{ number_format($notCalledWeekCount) }}</span>
            <span class="text-xs text-muted">missed</span>
        </div>
        <div class="text-xs text-muted mt-2 truncate">Need attention this week</div>
      </div>
    </div>

    {{-- Main Content Area with Tabs --}}
    <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
      
      {{-- Tabs Navigation --}}
      <div class="border-b border-border px-6 pt-4">
        <nav class="flex gap-6" aria-label="Call tabs">
          <button 
            id="tab-history"
            onclick="switchTab('history')"
            class="pb-3 text-sm font-semibold border-b-2 border-brand text-brand transition-colors">
            Calls History
          </button>
          <button 
            id="tab-pending"
            onclick="switchTab('pending')"
            class="pb-3 text-sm font-semibold border-b-2 border-transparent text-muted hover:text-body transition-colors flex items-center gap-2">
            Pending Calls
            @if($notCalledWeekCount > 0)
                <span class="bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300 py-0.5 px-2 rounded-full text-xs">{{ $notCalledWeekCount }}</span>
            @endif
          </button>
        </nav>
      </div>

      {{-- Tab 1: Calls Made Table --}}
      <div id="panel-history" class="p-0">
        @if ($logs->count() > 0)
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-sm">
              <thead class="bg-gray-50 dark:bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left font-semibold text-muted uppercase tracking-wider text-xs">Patient</th>
                  <th class="px-6 py-3 text-left font-semibold text-muted uppercase tracking-wider text-xs">Result</th>
                  <th class="px-6 py-3 text-left font-semibold text-muted uppercase tracking-wider text-xs">Notes</th>
                  <th class="px-6 py-3 text-left font-semibold text-muted uppercase tracking-wider text-xs">Logged By</th>
                  <th class="px-6 py-3 text-left font-semibold text-muted uppercase tracking-wider text-xs">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border">
                @foreach ($logs as $log)
                  <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-body">
                        {{ optional($log->patient)->first_name ? ($log->patient->first_name . ' ' . $log->patient->last_name) : 'Unknown Patient' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      @php 
                        $colors = [
                            'no_answer' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                            'rescheduled' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                            'will_attend' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                            'default' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800'
                        ];
                        $class = $colors[$log->result] ?? $colors['default'];
                      @endphp
                      <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $class }}">
                        {{ ucfirst(str_replace('_', ' ', $log->result)) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate text-muted" title="{{ $log->notes }}">{{ $log->notes ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-muted">{{ $log->caller->name ?? 'System' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-muted">
                        {{ $log->call_time ? \Carbon\Carbon::parse($log->call_time)->format('M d, h:i A') : '—' }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="px-6 py-4 border-t border-border">
            {{ $logs->appends(request()->query())->links() }}
          </div>
        @else
          <div class="flex flex-col items-center justify-center py-16 text-muted">
              <div class="h-16 w-16 bg-gray-100 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
              </div>
              <p class="font-medium">No calls logged for this period.</p>
          </div>
        @endif
      </div>

      {{-- Tab 2: Pending Calls --}}
      <div id="panel-pending" class="hidden p-6">
        
        <div class="space-y-8">
            <section>
                <div class="flex items-center gap-3 mb-4">
                    <h3 class="text-lg font-bold text-body">This Week</h3>
                    <div class="h-px flex-1 bg-border"></div>
                </div>

                @if ($notCalledAppointmentsWeek->isEmpty())
                    <div class="p-6 text-center border-2 border-dashed border-border rounded-xl text-muted bg-gray-50/50 dark:bg-white/5">
                        <span class="text-2xl block mb-2">🎉</span>
                        All appointments for this week have been contacted!
                    </div>
                @else
                    <div class="overflow-x-auto border border-border rounded-xl">
                        <table class="min-w-full divide-y divide-border text-sm">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-muted text-xs uppercase">Appt Date</th>
                                    <th class="px-4 py-3 text-left font-semibold text-muted text-xs uppercase">Patient</th>
                                    <th class="px-4 py-3 text-left font-semibold text-muted text-xs uppercase">Phone</th>
                                    <th class="px-4 py-3 text-right font-semibold text-muted text-xs uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-surface">
                                @foreach ($notCalledAppointmentsWeek as $appt)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}</div>
                                        <div class="text-xs text-muted">{{ $appt->time ? \Carbon\Carbon::parse($appt->time)->format('h:i A') : '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-medium">
                                        {{ optional($appt->patient)->first_name }} {{ optional($appt->patient)->last_name }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-muted select-all">
                                        {{ optional($appt->patient)->phone ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('call_logs.create', ['appointment_id' => $appt->id]) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand text-white hover:bg-brand/90 transition-colors shadow-sm">
                                            Log Call
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            @if($notCalledAppointmentsMonth->isNotEmpty())
            <section>
                <div class="flex items-center gap-3 mb-4">
                    <h3 class="text-lg font-bold text-body">Remainder of Month</h3>
                    <div class="h-px flex-1 bg-border"></div>
                </div>
                
                <div class="overflow-x-auto border border-border rounded-xl">
                    <table class="min-w-full divide-y divide-border text-sm">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-muted text-xs uppercase">Date</th>
                                <th class="px-4 py-3 text-left font-semibold text-muted text-xs uppercase">Patient</th>
                                <th class="px-4 py-3 text-right font-semibold text-muted text-xs uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-surface">
                            @foreach ($notCalledAppointmentsMonth as $appt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 font-medium">{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ optional($appt->patient)->first_name }} {{ optional($appt->patient)->last_name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('call_logs.create', ['appointment_id' => $appt->id]) }}" class="text-xs font-medium text-brand hover:underline">Log Call</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
    // Vanilla JS Tab Switcher
    function switchTab(tabName) {
        // Elements
        const tabHistory = document.getElementById('tab-history');
        const tabPending = document.getElementById('tab-pending');
        const panelHistory = document.getElementById('panel-history');
        const panelPending = document.getElementById('panel-pending');

        // Styles
        const activeClasses = ['border-brand', 'text-brand'];
        const inactiveClasses = ['border-transparent', 'text-muted'];

        if (tabName === 'history') {
            // Show History Panel
            panelHistory.classList.remove('hidden');
            panelPending.classList.add('hidden');

            // Style Tabs
            tabHistory.classList.add(...activeClasses);
            tabHistory.classList.remove(...inactiveClasses);
            
            tabPending.classList.remove(...activeClasses);
            tabPending.classList.add(...inactiveClasses);
        } else {
            // Show Pending Panel
            panelHistory.classList.add('hidden');
            panelPending.classList.remove('hidden');

            // Style Tabs
            tabPending.classList.add(...activeClasses);
            tabPending.classList.remove(...inactiveClasses);
            
            tabHistory.classList.remove(...activeClasses);
            tabHistory.classList.add(...inactiveClasses);
        }
    }
</script>
@endpush
@endsection