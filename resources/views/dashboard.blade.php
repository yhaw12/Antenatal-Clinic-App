@extends('layouts.app')

@section('title', 'ANC Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
  <!-- Hero Header -->
  <div class="relative overflow-hidden bg-white dark:bg-gray-900 shadow-lg mx-2 mt-8 p-3 lg:mx-0 lg:mt-0">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-emerald-500/10 "></div>
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
     
      <div class="flex items-center gap-4">
        <!-- Date Filter -->
        <form method="GET" action="{{ route('dashboard') }}" class="inline-block">
          <label for="dateFilter" class="sr-only">Filter by date</label>
          <input id="dateFilter" type="date" name="date" value="{{ request('date', now()->toDateString()) }}" class="px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm hover:shadow-md" onchange="this.form.submit()" aria-label="Filter by date">
        </form>
         <button id="openQuickRegister" class="group relative px-2 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg shadow-lg hover:shadow-xl hover:from-emerald-600 hover:to-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5" aria-haspopup="dialog">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Quick Register
          </button>
        <!-- Enhanced Search -->
        <div class="relative flex-1 max-w-md">
          <label for="globalSearch" class="sr-only">Search patients by name or hospital</label>
          <div class="relative">
            <input id="globalSearch" class="w-full pl-12 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm hover:shadow-md" placeholder="Search patients by name or hospital (press /)" aria-label="Search patients by name or hospital" aria-controls="searchSuggestions" aria-expanded="false" autocomplete="off">
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <!-- Search Suggestions Dropdown -->
          <div id="searchSuggestions" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg hidden z-50 max-h-60 overflow-y-auto transition-all duration-200 transform scale-95 opacity-0 origin-top">
            <div id="search-loading" class="p-4 text-center text-sm text-gray-600 dark:text-gray-400 hidden">
                <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Searching...
            </div>
            <div id="search-results" class="divide-y divide-gray-200 dark:divide-gray-700" role="listbox"></div>
            <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 text-center">
                Press ↓ to navigate, Enter to select
            </div>
          </div>
        </div>
        <!-- Quick Actions Buttons -->
        <div class="flex gap-3">
          <button id="quickActionsDropdown" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm" aria-haspopup="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Row -->
  <div class="max-w-7xl mx-4 grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-6">
    <div class="card p-6 rounded-lg shadow-lg bg-gradient-to-br from-indigo-500/10 to-blue-500/10 border border-indigo-200 dark:border-indigo-800 text-center">
      <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400 mb-2">Appointments Due</div>
      <div class="text-4xl font-bold text-indigo-700 dark:text-indigo-300 mb-1" id="kpi-total" aria-live="polite">{{ $total ?? 0 }}</div>
      <div class="text-xs text-indigo-500 dark:text-indigo-400">Total for {{ request('date', now()->toDateString()) }}</div>
      <div class="mt-3">
        <svg class="w-8 h-8 mx-auto text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
    </div>
    <div class="card p-6 rounded-lg shadow-lg bg-gradient-to-br from-emerald-500/10 to-green-500/10 border border-emerald-200 dark:border-emerald-800 text-center">
      <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-2">Patients Present</div>
      <div class="text-4xl font-bold text-emerald-700 dark:text-emerald-300 mb-1" id="kpi-present" aria-live="polite">{{ $present ?? 0 }}</div>
      <div class="text-xs text-emerald-500 dark:text-emerald-400">Arrived and queued today</div>
      <div class="mt-3">
        <svg class="w-8 h-8 mx-auto text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
    </div>
    <div class="card p-6 rounded-2xl shadow-lg bg-gradient-to-br from-red-500/10 to-orange-500/10 border border-red-200 dark:border-red-800 text-center">
      <div class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Follow-ups Needed</div>
      <div class="text-4xl font-bold text-red-700 dark:text-red-300 mb-1" id="kpi-notarrived" aria-live="polite">{{ $notArrived ?? 0 }}</div>
      <div class="text-xs text-red-500 dark:text-red-400">Missed appointments</div>
      <div class="mt-3">
        <svg class="w-8 h-8 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="max-w-7xl mx-4 grid grid-cols-1 lg:grid-cols-3 gap-8 pb-8">
    <!-- Queue Section -->
    <div class="lg:col-span-2">
      <div class="card overflow-hidden rounded-3xl shadow-xl">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-xl">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2M9 13v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1h2a1 1 0 001-1zm6 0v-1a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1h2a1 1 0 001-1z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Daily Queue</h3>
          </div>
          <div class="flex items-center gap-3">
            <select id="queueFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" aria-label="Filter queue">
              <option value="">All Status</option>
              <option value="scheduled">Scheduled</option>
              <option value="not_arrived">Not Arrived</option>
              <option value="queued">Present</option>
            </select>
            <button id="refreshQueue" class="p-2 bg-white dark:bg-gray-700 rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all" aria-label="Refresh queue">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-4 border-b bg-gray-50 dark:bg-gray-800 sticky top-0 z-10 flex items-center gap-4">
          <label class="inline-flex items-center gap-2 text-sm font-medium">
            <input id="selectAll" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
            <span class="text-gray-700 dark:text-gray-300">Select All</span>
          </label>
          <span class="text-sm text-gray-500 dark:text-gray-400">Bulk actions available</span>
          <span id="showingCount" class="ml-auto text-sm font-medium text-gray-700 dark:text-gray-300">Showing {{ $appointments->count() }} appointments</span>
        </div>
        <div id="appointmentsList" class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
          @forelse($appointments as $appt)
            @php
              $patient = $appt->patient;
              $status = $appt->status ?? 'scheduled';
              $isPresent = in_array($status, ['queued', 'in_room', 'seen']);
              $initials = trim(substr($patient->first_name, 0, 1) . substr($patient->last_name ?? '', 0, 1));
            @endphp
            <div data-appt-id="{{ $appt->id }}" data-status="{{ $status }}" data-present="{{ $isPresent ? 'true' : 'false' }}" class="appointment-item p-6 flex gap-6 items-start hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 group">
              <div class="w-12 flex-shrink-0">
                <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-semibold text-lg">{{ $initials ?: 'NA' }}</div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-4 mb-4">
                  <div>
                    <div class="font-medium text-gray-800 dark:text-gray-200 truncate text-lg">{{ $patient->first_name }} {{ $patient->last_name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $patient->address ?? 'No address' }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Phone: {{ $patient->phone ?? 'N/A' }} · Kin: {{ $patient->next_of_kin_name ?? '—' }}</div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ optional($appt->time)->format('H:i') ?? '' }} {{ $appt->date }}</div>
                    <div class="mt-2 flex gap-2 items-center justify-end">
                      <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isPresent ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                        {{ $isPresent ? 'Present' : ucfirst(str_replace('_', ' ', $status)) }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex gap-3 items-center">
                  <label class="inline-flex items-center gap-2">
                    <input class="rowCheckbox form-checkbox h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" value="{{ $appt->id }}" aria-label="Select appointment {{ $appt->id }}">
                  </label>
                  <div class="flex gap-3">
                    <button data-appt="{{ $appt->id }}" class="mark-present inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm {{ $isPresent ? 'opacity-50 cursor-not-allowed' : '' }} hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 transition-all" {{ $isPresent ? 'disabled' : '' }} data-tooltip="Mark as present">
                      Mark Present
                    </button>
                    <button data-appt="{{ $appt->id }}" class="mark-absent inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-all" data-tooltip="Mark as absent">
                      Mark Absent
                    </button>
                    <button type="button" onclick="openCallModal({{ $appt->id }}, '{{ addslashes($patient->first_name . ' ' . $patient->last_name) }}', '{{ $patient->phone }}')" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-all" data-tooltip="Call / Log">
                      Call / Log
                    </button>
                    <a href="{{ route('patients.show', $patient->id) }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-all" data-tooltip="View patient">
                      View
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="p-8 text-center text-gray-500">
              <svg class="h-12 w-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
              <p>No appointments for this date.</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- Call List -->
      <div class="card p-6 rounded-3xl shadow-xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-xl">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Call List</h4>
          </div>
          <span id="callCount" class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $callList->count() ?? 0 }} pending</span>
        </div>
        <div class="space-y-3 max-h-72 overflow-y-auto">
            @forelse($callList ?? [] as $c)
                <div class="group p-4 bg-surface border-surface rounded-2xl hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h5 class="font-semibold text-body text-sm">{{ $c->patient->first_name ?? 'Unknown' }} {{ $c->patient->last_name ?? '' }}</h5>
                            <p class="text-xs text-muted">{{ $c->patient->phone ?? 'N/A' }} • {{ $c->scheduled_date }}</p>
                        </div>
                        <button class="ml-3 px-3 py-2 bg-gradient-to-r from-brand to-accent text-white rounded-lg text-xs font-medium hover:from-brand hover:to-accent transition-all group-hover:scale-105" onclick="openCallModal({{ $c->id }}, '{{ addslashes($c->patient->first_name . ' ' . ($c->patient->last_name ?? '')) }}', '{{ $c->patient->phone ?? '' }}')" aria-label="Call {{ $c->patient->first_name }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <p class="p-4 text-muted text-center">No calls pending</p>
            @endforelse
        </div>
        <div class="mt-4 flex gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
          <button id="callNextBtn" class="flex-1 px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg font-medium hover:from-indigo-600 hover:to-indigo-700 transition-all" aria-label="Call next patient">
            Call Next
          </button>
          <a href="{{ route('call-logs') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">All Logs</a>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="card p-6 rounded-3xl shadow-xl">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
              <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h4>
          </div>
          <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" aria-label="View all activity">View All</a>
        </div>
        <div class="space-y-3 max-h-64 overflow-y-auto">
          @if(!empty($recentActivities) && $recentActivities->count())
            @foreach($recentActivities->take(5) as $act)
              <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-400">
                    {{ substr($act->user?->name ?? 'SYS', 0, 1) }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $act->user?->name ?? 'System' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $act->action }}</p>
                  </div>
                  <div class="text-xs text-gray-400 dark:text-gray-500">{{ $act->created_at->diffForHumans() }}</div>
                </div>
              </div>
            @endforeach
          @else
            <div class="text-center py-8">
              <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
              <p class="text-sm text-gray-500 dark:text-gray-400">Quiet day—no recent activity.</p>
            </div>
          @endif
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card p-6 rounded-3xl shadow-xl">
        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-3">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          Quick Actions
        </h4>
        <div class="flex flex-col gap-3">
          <button id="openQuickRegisterBtn" class="px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium shadow hover:shadow-lg hover:from-emerald-600 hover:to-emerald-700 transition-all transform hover:-translate-y-0.5" aria-label="Register new client">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Register New Client
          </button>
          <a href="{{ route('patients.index') }}" class="block px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all text-center" aria-label="View all patients">
            All Patients
          </a>
          @can('view-reports')
          <a href="{{ route('reports.index') }}" class="block px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all text-center" aria-label="View reports">
            Reports & Insights
          </a>
          @endcan
        </div>
      </div>
    </div>
  </div>

  <!-- Call Modal -->
  <div id="callModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="callModalTitle">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" role="document">
      <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
        <div class="flex items-center justify-between">
          <h3 id="callModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Log Call — <span id="callPatientName"></span></h3>
          <button onclick="closeModal('callModal')" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all" aria-label="Close modal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
      <form id="callLogForm" class="p-6 space-y-6" method="POST" action="{{ route('call-logs.store') }}">
        @csrf
        <input type="hidden" name="appointment_id" id="callAppointmentId">
        <div class="space-y-1">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Call Result <span class="text-red-500">*</span></label>
          <select name="result" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm">
            <option value="">Select outcome</option>
            <option value="no_answer">No Answer</option>
            <option value="rescheduled">Rescheduled</option>
            <option value="will_attend">Will Attend</option>
            <option value="refused">Refused</option>
            <option value="incorrect_number">Incorrect Number</option>
          </select>
        </div>
        <div>
          <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
          <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm" placeholder="Additional details about the call..."></textarea>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
          <a id="telLink" href="#" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition-colors">📞 Call Again</a>
          <div class="flex gap-3">
            <button type="button" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" onclick="closeModal('callModal')">Cancel</button>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium shadow hover:shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all focus:ring-2 focus:ring-blue-500">Save Log</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Bulk Confirm Modal -->
  <div id="confirmBulkModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="confirmBulkTitle">
    <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" role="document">
      <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
        <h3 id="confirmBulkTitle" class="text-xl font-bold text-gray-900 dark:text-white">Confirm Bulk Action</h3>
      </div>
      <div class="p-6">
        <p id="confirmBulkText" class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure?</p>
        <div class="flex gap-3 justify-end">
          <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" onclick="closeModal('confirmBulkModal')">Cancel</button>
          <button id="confirmBulkProceed" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium shadow hover:shadow-lg hover:from-emerald-600 hover:to-emerald-700 transition-all focus:ring-2 focus:ring-emerald-500">Proceed</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Export Modal -->
  @can('manage-exports')
  <div id="exportModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="exportModalTitle">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" role="document">
      <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
        <h3 id="exportModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Export Appointments</h3>
      </div>
      <form id="exportForm" class="p-6 space-y-6" method="POST" action="{{ route('exports.queue') }}">
        @csrf
        <div class="grid grid-cols-1 gap-6">
          <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date <span class="text-red-500">*</span></label>
            <input id="date_from" name="date_from" type="date" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm">
          </div>
          <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date <span class="text-red-500">*</span></label>
            <input id="date_to" name="date_to" type="date" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm">
          </div>
        </div>
        <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
          <button type="button" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all" onclick="closeModal('exportModal')">Cancel</button>
          <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl font-medium shadow hover:shadow-lg hover:from-indigo-600 hover:to-indigo-700 transition-all focus:ring-2 focus:ring-indigo-500">Queue Export</button>
        </div>
      </form>
    </div>
  </div>
  @endcan

  <!-- Enhanced Toast Container -->
  <div id="toast" class="fixed right-6 bottom-6 z-50 space-y-2"></div>
</div>

@push('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script>
// Toast Function
function toast(message, type = 'success') {
  const el = document.getElementById('toast');
  const id = 't' + Date.now();
  const color = type === 'error' ? 'bg-red-600 border-red-400' : type === 'warn' ? 'bg-yellow-500 border-yellow-400' : 'bg-emerald-500 border-emerald-400';
  const html = `
    <div id="${id}" class="border-l-4 ${color} bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg transform translate-y-10 opacity-0 transition-all duration-500 max-w-sm">
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'error' ? 'M6 18L18 6M6 6l12 12' : type === 'warn' ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
        </svg>
        <p class="text-sm font-medium text-gray-900 dark:text-white">${message}</p>
      </div>
    </div>
  `;
  el.innerHTML = html + el.innerHTML;
  el.classList.remove('hidden');
  setTimeout(() => {
    const node = document.getElementById(id);
    if (node) {
      node.classList.remove('translate-y-10', 'opacity-0');
      node.classList.add('translate-y-0', 'opacity-100');
      setTimeout(() => {
        node.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => {
          node.remove();
          if (el.children.length === 0) el.classList.add('hidden');
        }, 500);
      }, 4000);
    }
  }, 100);
}

// Chart
const ctx = document.getElementById('statusChart')?.getContext('2d');
let chart;
if (ctx) {
  chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Present', 'Not Arrived'],
      datasets: [{
        data: [{{ $present ?? 0 }}, {{ $notArrived ?? 0 }}],
        backgroundColor: ['#10B981', '#EF4444'],
        borderWidth: 0,
        hoverOffset: 20
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b' } }
      },
      animation: { duration: 1000, easing: 'easeOutQuart' }
    }
  });
}

// Modal Helpers
function openModal(id) {
  const modal = document.getElementById(id);
  const content = modal.querySelector('[role="document"]');
  modal.classList.remove('hidden');
  setTimeout(() => {
    content.classList.remove('scale-95', 'opacity-0');
    content.classList.add('scale-100', 'opacity-100');
    content.focus();
  }, 10);
}
function closeModal(id) {
  const modal = document.getElementById(id);
  const content = modal.querySelector('[role="document"]');
  content.classList.add('scale-95', 'opacity-0');
  setTimeout(() => {
    modal.classList.add('hidden');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
  }, 300);
}

// Call Modal
const openCallModal = (id, name, phone = '') => {
  document.getElementById('callAppointmentId').value = id;
  document.getElementById('callPatientName').textContent = name + (phone ? ' • ' + phone : '');
  const tel = phone ? `tel:${phone}` : '#';
  const telLink = document.getElementById('telLink');
  telLink.href = tel;
  telLink.style.display = phone ? 'block' : 'none';
  openModal('callModal');
};

// Keyboard Shortcuts
document.addEventListener('keydown', (e) => {
  if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
    e.preventDefault();
    document.getElementById('globalSearch').focus();
  }
  if (e.key.toLowerCase() === 'n') {
    openModal('quickRegisterModal');
  }
});

// Update Showing Count
function updateShowingCount() {
  const visible = document.querySelectorAll('.appointment-item:not([style*="display: none"])').length;
  document.getElementById('showingCount').textContent = `Showing ${visible} appointments`;
}

// Update Call Count
function updateCallCount() {
  const visible = document.querySelectorAll('#callList > div:not([style*="display: none"])').length;
  document.getElementById('callCount').textContent = `${visible} items`;
}

// Search Filtering for Queue and Call List (Local)
const debounce = (fn, delay) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
};

const handleLocalSearch = debounce((term) => {
  document.querySelectorAll('.appointment-item').forEach(el => {
    el.style.display = term === '' || el.textContent.toLowerCase().includes(term) ? '' : 'none';
  });
  document.querySelectorAll('#callList > div').forEach(el => {
    el.style.display = term === '' || el.textContent.toLowerCase().includes(term) ? '' : 'none';
  });
  updateShowingCount();
  updateCallCount();
}, 300);

// Global Patient Search Suggestions
const searchInput = document.getElementById('globalSearch');
const suggestions = document.getElementById('searchSuggestions');
const loading = document.getElementById('search-loading');
const results = document.getElementById('search-results');

let activeIndex = -1;

const fetchPatients = debounce(async (query) => {
  if (query.length < 2) {
    closeSuggestions();
    return;
  }
  loading.classList.remove('hidden');
  openSuggestions();
  results.innerHTML = '';
  try {
    const res = await fetch(`/api/patients/search?q=${encodeURIComponent(query)}`);
    if (!res.ok) throw new Error('Search failed');
    const patients = await res.json();
    renderSuggestions(patients, query);
  } catch (err) {
    console.error(err);
    results.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400">Search error - try again</div>';
  } finally {
    loading.classList.add('hidden');
  }
}, 300);

function renderSuggestions(patients, query) {
  if (!patients.length) {
    results.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400">No patient found</div>';
    return;
  }
  results.innerHTML = patients.map((p, index) => {
    const fullName = `${p.first_name} ${p.last_name || ''}`;
    const initials = (p.first_name[0] + (p.last_name?.[0] || '')).toUpperCase();
    const highlightedName = highlightMatch(fullName, query);
    const hospital = p.hospital || 'N/A';
    const phone = p.phone ? ` • ${p.phone}` : '';
    return `
      <a href="${getPatientUrl(p.id)}" role="option" aria-selected="${index === 0 ? 'true' : 'false'}" class="flex items-center gap-4 p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors ${index === 0 ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : ''}">
        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-semibold text-sm">${initials}</div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-900 dark:text-white truncate">${highlightedName}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${hospital}${phone}</p>
        </div>
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    `;
  }).join('');
  activeIndex = -1; // Reset active
}

function highlightMatch(text, query) {
  if (!query) return text;
  const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
  return text.replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-800 font-semibold">$1</span>');
}

function getPatientUrl(id) {
    return '{{ route("patients.show", ":id") }}'.replace(':id', id);
}

function openSuggestions() {
  suggestions.classList.remove('hidden', 'scale-95', 'opacity-0');
  suggestions.classList.add('scale-100', 'opacity-100');
  searchInput.setAttribute('aria-expanded', 'true');
}

function closeSuggestions() {
  suggestions.classList.add('hidden', 'scale-95', 'opacity-0');
  suggestions.classList.remove('scale-100', 'opacity-100');
  searchInput.setAttribute('aria-expanded', 'false');
  results.innerHTML = '';
  loading.classList.add('hidden');
  activeIndex = -1;
}

// Keyboard Navigation for Suggestions
suggestions.addEventListener('keydown', (e) => {
  const options = Array.from(results.querySelectorAll('[role="option"]'));
  if (!options.length) return;

  if (e.key === 'ArrowDown') {
    e.preventDefault();
    activeIndex = (activeIndex + 1) % options.length;
    setActiveSuggestion(options, activeIndex);
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    activeIndex = (activeIndex - 1 + options.length) % options.length;
    setActiveSuggestion(options, activeIndex);
  } else if (e.key === 'Enter' && activeIndex >= 0) {
    e.preventDefault();
    options[activeIndex].click();
  } else if (e.key === 'Escape') {
    closeSuggestions();
    searchInput.focus();
  }
});

function setActiveSuggestion(options, index) {
  options.forEach((opt, i) => {
    opt.setAttribute('aria-selected', i === index ? 'true' : 'false');
    opt.classList.toggle('bg-blue-50', i === index);
    opt.classList.toggle('dark:bg-blue-900/50', i === index);
    opt.classList.toggle('text-blue-700', i === index);
    opt.classList.toggle('dark:text-blue-300', i === index);
  });
  options[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

// Click Outside to Close Suggestions
document.addEventListener('click', (e) => {
  if (!searchInput.contains(e.target) && !suggestions.contains(e.target)) {
    closeSuggestions();
  }
});

// Combined Input Handler
searchInput?.addEventListener('input', (e) => {
  const term = e.target.value.toLowerCase().trim();
  handleLocalSearch(term); // Local filtering for queue/call list
  fetchPatients(term); // Global suggestions
});

// Focus to Open if Has Value
searchInput.addEventListener('focus', () => {
  const term = searchInput.value.trim();
  if (term.length >= 2) fetchPatients(term);
});

// Update Showing Count
function updateShowingCount() {
  const visible = document.querySelectorAll('.appointment-item:not([style*="display: none"])').length;
  document.getElementById('showingCount').textContent = `Showing ${visible} appointments`;
}

// Update Call Count
function updateCallCount() {
  const visible = document.querySelectorAll('#callList > div:not([style*="display: none"])').length;
  document.getElementById('callCount').textContent = `${visible} items`;
}

// Queue Filter
document.getElementById('queueFilter')?.addEventListener('change', (e) => {
  const val = e.target.value;
  document.querySelectorAll('.appointment-item').forEach(el => {
    let show = true;
    if (val === 'queued') show = el.dataset.present === 'true';
    else if (val === 'not_arrived') show = el.dataset.status === 'not_arrived';
    else if (val === 'scheduled') show = el.dataset.status === 'scheduled';
    else if (val) show = el.dataset.status === val;
    el.style.display = show ? '' : 'none';
  });
  updateShowingCount();
});

// Select All / Row Checkboxes
const selectAll = document.getElementById('selectAll');
const rowCheckboxes = () => Array.from(document.querySelectorAll('.rowCheckbox'));
const updateBulkButtons = () => {
  const any = rowCheckboxes().some(cb => cb.checked);
  document.getElementById('bulkMarkPresent').disabled = !any;
  document.getElementById('bulkMarkAbsent').disabled = !any;
};

selectAll?.addEventListener('change', (e) => {
  rowCheckboxes().forEach(cb => cb.checked = e.target.checked);
  updateBulkButtons();
});

document.getElementById('appointmentsList')?.addEventListener('change', (e) => {
  if (e.target.classList.contains('rowCheckbox')) updateBulkButtons();
});

// Bulk Actions
let bulkAction = null;
document.getElementById('bulkMarkPresent')?.addEventListener('click', () => {
  const ids = rowCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
  if (!ids.length) return toast('No items selected', 'warn');
  bulkAction = { type: 'present', ids };
  document.getElementById('confirmBulkText').textContent = `Mark ${ids.length} selected as PRESENT? This cannot be undone.`;
  openModal('confirmBulkModal');
});

document.getElementById('bulkMarkAbsent')?.addEventListener('click', () => {
  const ids = rowCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
  if (!ids.length) return toast('No items selected', 'warn');
  bulkAction = { type: 'absent', ids };
  document.getElementById('confirmBulkText').textContent = `Mark ${ids.length} selected as ABSENT?`;
  openModal('confirmBulkModal');
});

// Confirm Bulk Proceed
document.getElementById('confirmBulkProceed')?.addEventListener('click', async () => {
  if (!bulkAction) return closeModal('confirmBulkModal');
  const url = bulkAction.type === 'present' ? '/daily-queue/mark-present' : '/daily-queue/mark-absent';
  const button = document.getElementById(`bulkMark${bulkAction.type.charAt(0).toUpperCase() + bulkAction.type.slice(1)}`);
  const originalText = button.textContent;
  button.textContent = 'Loading...';
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ appointment_ids: bulkAction.ids })
    });
    if (!res.ok) throw new Error('Failed');
    bulkAction.ids.forEach(appt => {
      const item = document.querySelector(`[data-appt-id="${appt}"]`);
      if (item) {
        const pill = item.querySelector('.px-3.py-1.rounded-full.text-xs.font-semibold');
        if (bulkAction.type === 'present') {
          pill.classList.remove('bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300');
          pill.classList.add('bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-900', 'dark:text-emerald-300');
          pill.textContent = 'Present';
          item.dataset.status = 'queued';
          item.dataset.present = 'true';
          const presentBtn = item.querySelector('.mark-present');
          presentBtn.classList.add('opacity-50', 'cursor-not-allowed');
          presentBtn.disabled = true;
        } else {
          pill.classList.remove('bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-900', 'dark:text-emerald-300');
          pill.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300');
          pill.textContent = 'Absent';
          item.dataset.status = 'absent';
          item.dataset.present = 'false';
          const presentBtn = item.querySelector('.mark-present');
          presentBtn.classList.remove('opacity-50', 'cursor-not-allowed');
          presentBtn.disabled = false;
        }
      }
      if (bulkAction.type === 'present') removeFromCallList(appt);
    });
    const p = document.getElementById('kpi-present');
    const n = document.getElementById('kpi-notarrived');
    const delta = bulkAction.ids.length;
    if (bulkAction.type === 'present') {
      p.textContent = parseInt(p.textContent) + delta;
      n.textContent = parseInt(n.textContent) - delta;
    } else {
      p.textContent = parseInt(p.textContent) - delta;
      n.textContent = parseInt(n.textContent) + delta;
    }
    updateChart();
    selectAll.checked = false;
    rowCheckboxes().forEach(cb => cb.checked = false);
    updateBulkButtons();
    document.getElementById('queueFilter').dispatchEvent(new Event('change'));
    updateCallCount();
    toast('Bulk operation completed');
    closeModal('confirmBulkModal');
  } catch (err) {
    console.error(err);
    toast('Bulk operation failed', 'error');
  } finally {
    button.textContent = originalText;
  }
  bulkAction = null;
});

// Remove from Call List
function removeFromCallList(appt) {
  const callItems = document.querySelectorAll('#callList > div');
  for (let ci of callItems) {
    const onclick = ci.querySelector('button')?.getAttribute('onclick');
    const match = onclick?.match(/openCallModal\((\d+)/);
    if (match && match[1] === appt) {
      ci.classList.add('opacity-0', 'scale-95');
      setTimeout(() => ci.remove(), 300);
      break;
    }
  }
}

// Update Chart
function updateChart() {
  if (chart) {
    chart.data.datasets[0].data[0] = parseInt(document.getElementById('kpi-present').textContent);
    chart.data.datasets[0].data[1] = parseInt(document.getElementById('kpi-notarrived').textContent);
    chart.update();
  }
}

// Single Row Actions
document.getElementById('appointmentsList')?.addEventListener('click', async (e) => {
  const presentBtn = e.target.closest('.mark-present');
  const absentBtn = e.target.closest('.mark-absent');

  if (presentBtn && !presentBtn.disabled) {
    const appt = presentBtn.dataset.appt;
    if (!appt) return;
    const original = presentBtn.innerHTML;
    presentBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...';
    presentBtn.disabled = true;
    try {
      const res = await fetch('/daily-queue/mark-present', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ appointment_id: appt })
      });
      if (!res.ok) throw new Error('Failed');
      const item = document.querySelector(`[data-appt-id="${appt}"]`);
      if (item) {
        const pill = item.querySelector('.px-3.py-1.rounded-full.text-xs.font-semibold');
        pill.classList.remove('bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300');
        pill.classList.add('bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-900', 'dark:text-emerald-300');
        pill.textContent = 'Present';
        item.dataset.status = 'queued';
        item.dataset.present = 'true';
        presentBtn.classList.add('opacity-50', 'cursor-not-allowed');
        presentBtn.disabled = true;
      }
      const p = document.getElementById('kpi-present');
      const n = document.getElementById('kpi-notarrived');
      p.textContent = parseInt(p.textContent) + 1;
      n.textContent = parseInt(n.textContent) - 1;
      updateChart();
      removeFromCallList(appt);
      updateCallCount();
      document.getElementById('queueFilter').dispatchEvent(new Event('change'));
      toast('Marked present');
    } catch (err) {
      console.error(err);
      toast('Could not mark present', 'error');
      presentBtn.innerHTML = original;
      presentBtn.disabled = false;
    }
  }

  if (absentBtn) {
    const appt = absentBtn.dataset.appt;
    if (!appt) return;
    const original = absentBtn.innerHTML;
    absentBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...';
    absentBtn.disabled = true;
    try {
      const res = await fetch('/daily-queue/mark-absent', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ appointment_id: appt })
      });
      if (!res.ok) throw new Error('Failed');
      const item = document.querySelector(`[data-appt-id="${appt}"]`);
      if (item) {
        const pill = item.querySelector('.px-3.py-1.rounded-full.text-xs.font-semibold');
        pill.classList.remove('bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-900', 'dark:text-emerald-300');
        pill.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300');
        pill.textContent = 'Absent';
        item.dataset.status = 'absent';
        item.dataset.present = 'false';
        const presentBtn = item.querySelector('.mark-present');
        presentBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        presentBtn.disabled = false;
      }
      const p = document.getElementById('kpi-present');
      const n = document.getElementById('kpi-notarrived');
      p.textContent = parseInt(p.textContent) - 1;
      n.textContent = parseInt(n.textContent) + 1;
      updateChart();
      document.getElementById('queueFilter').dispatchEvent(new Event('change'));
      toast('Marked absent');
    } catch (err) {
      console.error(err);
      toast('Could not mark absent', 'error');
    } finally {
      absentBtn.innerHTML = original;
      absentBtn.disabled = false;
    }
  }
});

// Call Next
document.getElementById('callNextBtn')?.addEventListener('click', () => {
  const first = document.querySelector('#callList > div:not([style*="display: none"]) button');
  if (!first) return toast('No one to call', 'warn');
  first.click();
});

// Refresh
document.getElementById('refreshQueue')?.addEventListener('click', () => {
  toast('Refreshing...');
  location.reload();
});

// Export Modal
document.getElementById('openExportModal')?.addEventListener('click', () => openModal('exportModal'));

// Quick Register Links
document.getElementById('openQuickRegister')?.addEventListener('click', () => window.location.href = '{{ route('appointments.create') }}');
document.getElementById('openQuickRegisterBtn')?.addEventListener('click', () => window.location.href = '{{ route('appointments.create') }}');

// Form Submissions
document.getElementById('callLogForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const submitBtn = form.querySelector('[type="submit"]');
  const original = submitBtn.textContent;
  submitBtn.textContent = 'Saving...';
  const formData = new FormData(form);
  try {
    const res = await fetch(form.action, {
      method: 'POST',
      body: formData
    });
    if (!res.ok) throw new Error('Failed');
    toast('Call logged');
    closeModal('callModal');
    removeFromCallList(formData.get('appointment_id'));
    updateCallCount();
    form.reset();
  } catch (err) {
    toast('Failed to log call', 'error');
  } finally {
    submitBtn.textContent = original;
  }
});

document.getElementById('quickRegisterForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const submitBtn = form.querySelector('[type="submit"]');
  const original = submitBtn.textContent;
  submitBtn.textContent = 'Registering...';
  const formData = new FormData(form);
  try {
    const res = await fetch(form.action, {
      method: 'POST',
      body: formData
    });
    if (!res.ok) throw new Error('Failed');
    toast('Patient registered');
    closeModal('quickRegisterModal');
    form.reset();
    setTimeout(() => location.reload(), 500);
  } catch (err) {
    toast('Failed to register patient', 'error');
  } finally {
    submitBtn.textContent = original;
  }
});

// Close on ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    ['quickRegisterModal', 'callModal', 'confirmBulkModal', 'exportModal'].forEach(id => {
      if (!document.getElementById(id).classList.contains('hidden')) closeModal(id);
    });
    closeSuggestions();
  }
});
</script>

@endpush

@endsection