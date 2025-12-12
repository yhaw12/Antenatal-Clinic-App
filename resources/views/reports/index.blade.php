@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
@php
  // 1. PHP Setup: Ensure default variables exist to prevent errors.
  // Note: HTML5 Date Inputs (<input type="date">) REQUIRE 'Y-m-d' format for the value attribute.
  $from = $from ?? now()->format('Y-m-d');
  $to = $to ?? now()->format('Y-m-d');
  
  $status = $status ?? '';
  $kpis = $kpis ?? ['total' => 0, 'present' => 0, 'notArrived' => 0, 'new' => 0, 'review' => 0, 'referrals' => 0, 'cancelled' => 0];
  $chart = $chart ?? ['labels' => [], 'counts' => []];
  $chartTitle = $chartTitle ?? 'Daily Appointments Trend'; // Default fallback
  $callStats = $callStats ?? [];
  $comparison = $comparison ?? null;
  $appts = $appts ?? collect();
@endphp

<div class="min-h-screen py-4 sm:py-8 px-4 sm:px-6 bg-app text-body">
  
  <div class="max-w-7xl mx-auto space-y-6">

    {{-- Header & Toolbar --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
      <div>
        <h1 class="text-2xl font-extrabold text-body">Appointment Reports</h1>
        <p class="mt-1 text-sm text-muted">Analyze clinic performance, attendance, and workload.</p>
      </div>

      <div class="flex flex-col items-end gap-3 w-full lg:w-auto">
        
        {{-- Quick Filters --}}
        <div class="w-full overflow-x-auto pb-2 lg:pb-0 lg:w-auto no-scrollbar">
            <div class="flex gap-2 min-w-max">
                <button type="button" 
                        onclick="quickFilter('{{ now()->startOfMonth()->format('Y-m-d') }}', '{{ now()->endOfMonth()->format('Y-m-d') }}')"
                        class="text-xs px-3 py-1.5 rounded border border-border text-muted bg-surface hover:opacity-80 transition cursor-pointer">
                    This Month
                </button>
                
                <button type="button" 
                        onclick="quickFilter('{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}', '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')"
                        class="text-xs px-3 py-1.5 rounded border border-border text-muted bg-surface hover:opacity-80 transition cursor-pointer">
                    Last Month
                </button>
                
                <button type="button" 
                        onclick="quickFilter('{{ now()->subDays(7)->format('Y-m-d') }}', '{{ now()->format('Y-m-d') }}')"
                        class="text-xs px-3 py-1.5 rounded border border-border text-muted bg-surface hover:opacity-80 transition cursor-pointer">
                    Last 7 Days
                </button>

                <button type="button" 
                        onclick="quickFilter('{{ now()->subMonths(6)->format('Y-m-d') }}', '{{ now()->format('Y-m-d') }}')"
                        class="text-xs px-3 py-1.5 rounded border border-border text-muted bg-surface hover:opacity-80 transition cursor-pointer">
                    6 Months
                </button>

                <button type="button" 
                        onclick="quickFilter('{{ now()->startOfYear()->format('Y-m-d') }}', '{{ now()->endOfYear()->format('Y-m-d') }}')"
                        class="text-xs px-3 py-1.5 rounded border border-border text-muted bg-surface hover:opacity-80 transition cursor-pointer">
                    This Year
                </button>
            </div>
        </div>

        <div class="w-full lg:w-auto">
          {{-- Main Filter Form --}}
          <form id="reportFilters" action="{{ route('reports.generate') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <div class="flex items-center gap-2 flex-1">
                {{-- ID 'dateFrom' and 'dateTo' are crucial for the JS to work --}}
                <input type="date" id="dateFrom" name="from" value="{{ $from }}" class="w-full sm:w-auto h-10 px-3 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-brand border border-border bg-surface text-body"/>
                <span class="text-muted text-xs">to</span>
                <input type="date" id="dateTo" name="to" value="{{ $to }}" class="w-full sm:w-auto h-10 px-3 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-brand border border-border bg-surface text-body"/>
            </div>
            
            <select name="status" class="h-10 px-3 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-brand border border-border bg-surface text-body cursor-pointer">
              <option value="" {{ $status === '' ? 'selected' : '' }}>All Status</option>
              <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
              <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
              <option value="missed" {{ $status === 'missed' ? 'selected' : '' }}>Missed</option>
              <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 px-4 h-10 rounded-lg text-sm font-bold shadow hover:brightness-110 transition-all bg-brand text-white">
                  Generate
                </button>

                {{-- Export Dropdown --}}
                <div class="relative">
                  <button id="exportToggle" type="button" aria-expanded="false" class="h-10 px-3 rounded-lg text-sm font-bold shadow hover:brightness-110 transition-all bg-success text-white flex items-center justify-center">
                    <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span class="hidden sm:inline">Export</span>
                  </button>
                  
                  {{-- Export Menu --}}
                  <div id="exportMenu" class="hidden absolute right-0 mt-2 w-44 rounded-xl shadow-xl z-20 overflow-hidden bg-surface border border-border">
                    <div class="w-full text-left px-4 py-3 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition-colors text-body cursor-pointer" 
                         onclick="submitExport('csv')">CSV (.csv)</div>
                    <div class="w-full text-left px-4 py-3 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition-colors border-t border-border text-body cursor-pointer" 
                         onclick="submitExport('excel')">Excel (.xlsx)</div>
                  </div>
                </div>
            </div>
          </form>

          {{-- Hidden Form for Exports --}}
          <form id="exportForm" action="{{ route('exports.queue') }}" method="POST" class="hidden">
              @csrf
              <input type="hidden" name="from" id="expFrom">
              <input type="hidden" name="to" id="expTo">
              <input type="hidden" name="format" id="expFormat">
          </form>
        </div>
      </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1 bg-surface border border-border">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: color-mix(in srgb, var(--brand) 10%, transparent);">
          <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <div class="text-xs uppercase font-bold tracking-wide text-muted">Total Appts</div>
          <div class="text-2xl font-black text-body">{{ number_format($kpis['total']) }}</div>
        </div>
      </div>

      <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1 bg-surface border border-border">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: color-mix(in srgb, var(--success) 10%, transparent);">
          <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <div class="text-xs uppercase font-bold tracking-wide text-muted">Present</div>
          <div class="text-2xl font-black text-body">{{ number_format($kpis['present']) }}</div>
        </div>
      </div>

      <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1 bg-surface border border-border">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: color-mix(in srgb, var(--accent) 10%, transparent);">
          <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
          <div class="text-xs uppercase font-bold tracking-wide text-muted">Attendance Rate</div>
          @php $rate = ($kpis['total'] > 0) ? round(($kpis['present'] / $kpis['total']) * 100, 1) : 0; @endphp
          <div class="text-2xl font-black text-body">{{ $rate }}<span class="text-sm font-medium text-muted">%</span></div>
        </div>
      </div>

      <div class="p-5 rounded-2xl shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1 bg-surface border border-border">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: color-mix(in srgb, var(--danger) 10%, transparent);">
          <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <div class="text-xs uppercase font-bold tracking-wide text-muted">Missed</div>
          <div class="text-2xl font-black text-body">{{ number_format($kpis['notArrived']) }}</div>
        </div>
      </div>
    </div>

    {{-- Trends Chart --}}
    <div class="rounded-2xl p-4 sm:p-6 shadow-sm bg-surface border border-border">
      <div class="flex justify-between items-center mb-4">
          <h3 class="text-sm font-bold text-body">{{ $chartTitle }}</h3>
          @if(str_contains($chartTitle, 'Monthly'))
            <span class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-brand/10 text-brand">Monthly View</span>
          @else
            <span class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-brand/10 text-brand">Daily View</span>
          @endif
      </div>

      @if(empty($chart['labels']) && !$comparison)
        <div class="text-center py-12 text-muted">No data available for this range.</div>
      @else
        <div class="relative h-64 w-full">
            <canvas id="appointmentChart"></canvas>
        </div>
      @endif
    </div>

    {{-- Insights Row --}}
    @if(!$comparison)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Clinical Flow --}}
        <div class="rounded-2xl p-4 sm:p-6 shadow-sm bg-surface border border-border">
            <h3 class="text-sm font-bold mb-6 flex items-center gap-2 text-body">
                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Clinical Flow
            </h3>
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8">
                <div class="w-32 h-32 relative shrink-0">
                    <canvas id="workloadChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
                        <span class="text-2xl font-bold text-body">{{ $kpis['total'] }}</span>
                        <span class="text-[10px] uppercase text-muted">Patients</span>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-2 gap-x-8 gap-y-4 w-full">
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-muted">New Cases</span>
                            <span class="font-bold text-brand">{{ $kpis['new'] ?? 0 }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-brand" style="width: {{ $kpis['total'] ? ($kpis['new']/$kpis['total'])*100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-muted">Reviews</span>
                            <span class="font-bold text-success">{{ $kpis['review'] ?? 0 }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-success" style="width: {{ $kpis['total'] ? ($kpis['review']/$kpis['total'])*100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-muted">Referrals Sent</span>
                            <span class="font-bold text-accent">{{ $kpis['referrals'] ?? 0 }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-accent" style="width: {{ $kpis['present'] ? ($kpis['referrals']/$kpis['present'])*100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-muted">Cancelled</span>
                            <span class="font-bold text-danger">{{ $kpis['cancelled'] ?? 0 }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-danger" style="width: {{ $kpis['total'] ? ($kpis['cancelled']/$kpis['total'])*100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Call Outcomes --}}
        <div class="rounded-2xl p-4 sm:p-6 shadow-sm bg-surface border border-border">
            <h3 class="text-sm font-bold mb-6 flex items-center gap-2 text-body">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Call Outcomes
            </h3>
            @if(empty($callStats))
                <div class="h-32 flex flex-col items-center justify-center text-sm text-muted">
                    <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    No calls logged in this period
                </div>
            @else
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <div class="w-32 h-32 relative shrink-0">
                        <canvas id="callChart"></canvas>
                    </div>
                    <div class="flex-1 space-y-2 text-sm w-full">
                        @foreach($callStats as $res => $count)
                            <div class="flex justify-between items-center p-2 rounded hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                                <span class="capitalize text-muted">{{ str_replace('_', ' ', $res) }}</span>
                                <span class="font-bold px-2 py-0.5 rounded text-xs bg-bg border border-border text-body">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Detailed List / Table --}}
    <div class="rounded-2xl shadow-sm overflow-hidden bg-surface border border-border mt-6">
      <div class="px-6 py-5 border-b border-border flex items-center justify-between bg-gray-50/50 dark:bg-white/5">
          <div>
              <h3 class="font-bold text-base text-body">Appointment Log</h3>
              <p class="text-xs text-muted mt-0.5">Detailed record of patient visits and statuses.</p>
          </div>
          <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-BLACK border border-border text-body shadow-sm">
              {{ $appts->total() }} Records
          </span>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
          <thead class="uppercase tracking-wider border-b border-border text-muted" style="background: color-mix(in srgb, var(--bg) 50%, transparent); font-size:11px;">
            <tr>
              <th class="px-6 py-3 font-bold">Date & Time</th>
              <th class="px-6 py-3 font-bold">Patient Details</th>
              <th class="px-6 py-3 font-bold">Visit Type</th>
              <th class="px-6 py-3 font-bold">Status</th>
              <th class="px-6 py-3 font-bold text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            @forelse($appts as $appt)
              <tr class="group hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                
                {{-- Date & Time: Stacked for readability --}}
                <td class="px-6 py-4">
                  <div class="flex flex-col">
                      <span class="font-bold text-body text-sm">
                          {{ \Carbon\Carbon::parse($appt->date)->format('d M, Y') }}
                      </span>
                      <span class="text-xs text-muted flex items-center gap-1 mt-0.5">
                          <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                          {{ $appt->time ? \Carbon\Carbon::parse($appt->time)->format('h:i A') : 'TBD' }}
                      </span>
                  </div>
                </td>

                {{-- Patient Details --}}
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0" 
                           style="background: color-mix(in srgb, var(--brand) 80%, black);">
                          {{ substr(optional($appt->patient)->first_name ?? 'U', 0, 1) }}
                      </div>
                      <div>
                          <div class="font-bold text-body text-sm">
                              {{ optional($appt->patient)->first_name ?? 'Unknown' }} {{ optional($appt->patient)->last_name }}
                          </div>
                          <div class="text-xs text-muted mt-0.5">
                              {{ optional($appt->patient)->phone ?? 'No Contact Info' }}
                          </div>
                      </div>
                  </div>
                </td>

                {{-- Visit Type (New vs Review) --}}
                <td class="px-6 py-4">
                    @php
                        // Logic: If patient creation date is same as appointment date, it's New. Else Review.
                        $isNew = optional($appt->patient)->created_at?->isSameDay($appt->date);
                    @endphp
                    @if($isNew)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            New Patient
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-gray-50 text-gray-600 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Review
                        </span>
                    @endif
                </td>

                {{-- Status Pill --}}
                <td class="px-6 py-4">
                  @php $s = $appt->status ?? 'scheduled' @endphp
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold capitalize shadow-sm border" style="
                    background-color: {{ $s === 'missed' ? '#FEF2F2' : ($s === 'seen' ? '#ECFDF5' : '#EFF6FF') }};
                    color: {{ $s === 'missed' ? '#991B1B' : ($s === 'seen' ? '#065F46' : '#1E40AF') }};
                    border-color: {{ $s === 'missed' ? '#FECACA' : ($s === 'seen' ? '#A7F3D0' : '#BFDBFE') }};
                  ">
                    @if($s === 'seen') ✓ @endif
                    {{ $s }}
                  </span>
                </td>

                {{-- Action --}}
                <td class="px-6 py-4 text-right">
                  <a href="{{ route('patients.show', optional($appt->patient)->id) }}" 
                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-muted hover:text-brand transition-all"
                     title="View Profile">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-muted">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="font-medium">No appointments found for this period.</span>
                        <span class="text-xs mt-1">Try adjusting your date filters.</span>
                    </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
    {{-- Pagination Footer --}}
      @if($appts->hasPages())
      <div class="px-6 py-4 border-t flex items-center justify-between bg-gray-50/50 dark:bg-white/5" style="border-color:var(--border)">
          <div class="text-xs text-muted">
              Showing <span class="font-bold text-body">{{ $appts->firstItem() ?? 0 }}</span> 
              - <span class="font-bold text-body">{{ $appts->lastItem() ?? 0 }}</span> 
              of <span class="font-bold text-body">{{ $appts->total() }}</span>
          </div>
          <div class="flex items-center gap-2">
              {{-- PREVIOUS BUTTON --}}
              @if ($appts->onFirstPage())
                  <span class="px-3 py-1.5 text-xs font-medium rounded-lg border cursor-not-allowed bg-gray-100 border-gray-200 text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500">
                      Previous
                  </span>
              @else
                  <a href="{{ $appts->appends(request()->query())->previousPageUrl() }}" 
                     class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors shadow-sm bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                      Previous
                  </a>
              @endif

              {{-- NEXT BUTTON --}}
              @if ($appts->hasMorePages())
                  <a href="{{ $appts->appends(request()->query())->nextPageUrl() }}" 
                     class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors shadow-sm bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                      Next
                  </a>
              @else
                  <span class="px-3 py-1.5 text-xs font-medium rounded-lg border cursor-not-allowed bg-gray-100 border-gray-200 text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500">
                      Next
                  </span>
              @endif
          </div>
      </div>
      @endif
    </div>

  </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@push('scripts')
<script>
  // 1. GLOBAL FILTER FUNCTION
  // Attached to window to ensure visibility to inline onclick events
  window.quickFilter = function(startDate, endDate) {
      const inputFrom = document.getElementById('dateFrom');
      const inputTo = document.getElementById('dateTo');
      const form = document.getElementById('reportFilters');

      if (inputFrom && inputTo && form) {
          inputFrom.value = startDate;
          inputTo.value = endDate;
          form.submit();
      } else {
          console.error("Filter form elements not found.");
      }
  };

  // 2. EXPORT FUNCTION
  window.submitExport = function(format) {
      document.getElementById('expFrom').value = document.getElementById('dateFrom').value;
      document.getElementById('expTo').value = document.getElementById('dateTo').value;
      document.getElementById('expFormat').value = format;
      document.getElementById('exportForm').submit();
      
      // Hide menu after click
      document.getElementById('exportMenu').classList.add('hidden');
  };

  // 3. UI Toggle Logic
  document.addEventListener('DOMContentLoaded', () => {
    const exportToggle = document.getElementById('exportToggle');
    const exportMenu = document.getElementById('exportMenu');
    
    if (exportToggle && exportMenu) {
      exportToggle.addEventListener('click', (e) => {
          e.stopPropagation();
          exportMenu.classList.toggle('hidden');
      });
      document.addEventListener('click', (e) => {
        if (!exportMenu.contains(e.target) && !exportToggle.contains(e.target)) {
          exportMenu.classList.add('hidden');
        }
      });
    }
  });

  // 4. CHART RENDERING
  (function () {
    const cssVar = (name, fallback) => {
      const val = getComputedStyle(document.documentElement).getPropertyValue(name);
      return val ? val.trim() : fallback;
    };

    // Main Line Chart
    const lineCtx = document.getElementById('appointmentChart')?.getContext('2d');
    const lineData = {!! json_encode($chart) !!};
    if (lineCtx && lineData.labels && lineData.labels.length) {
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

    // Workload Doughnut
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

    // Call Stats Doughnut
    const callCtx = document.getElementById('callChart')?.getContext('2d');
    if (callCtx) {
        const callData = {!! json_encode($callStats) !!};
        if(Object.keys(callData).length > 0) {
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
    }
  })();
</script>
@endpush
@endsection