@extends('layouts.app')

@section('title', 'ANC Dashboard')

@section('content')
    
<div class="min-h-screen" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    <header class="sticky top-0 z-40" style="backdrop-filter: blur(12px); background: color-mix(in srgb, var(--bg) 95%, transparent); border-bottom: 1px solid var(--border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 flex-wrap gap-2">
                
                <div class="flex items-center space-x-4 flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--brand), var(--accent));">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        
                        <div class="hidden sm:block ml-2">
                          <div id="currentDate" class="text-sm font-medium" style="color:var(--muted)"></div>
                        </div>
                    </div>
                </div>
                
                <div class="order-2 sm:order-3 flex items-center space-x-2 justify-end flex-shrink-0">
                    
                    <input 
                        type="date" 
                        id="dateFilter" 
                        class="hidden sm:inline-flex px-3 py-2 rounded-xl text-sm transition-all"
                        style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--text); border:1px solid var(--border);"
                        value="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                    > 

                    <button 
                        id="tomorrowBtn"
                        class="hidden sm:inline-flex items-center justify-center px-3 py-2 rounded-xl text-sm transition-all hover:opacity-80"
                        title="Tomorrow's Schedule"
                        style="background: color-mix(in srgb, var(--brand) 10%, transparent); color:var(--brand); border:1px solid color-mix(in srgb, var(--brand) 20%, transparent);">
                        
                        <svg class="w-5 h-5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        
                        <span class="hidden lg:inline ml-2">Tomorrow's Schedule</span>
                    </button>
                    
                    <select id="statusFilter" class="hidden md:inline-flex px-3 py-2 rounded-xl text-sm transition-all" style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--text); border:1px solid var(--border);">
                        <option value="" style="color:var(--text)">All Status</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="present">Present</option>
                        <option value="missed">Missed</option>
                    </select>

                    <div class="order-3 sm:order-2 flex-1 w-full sm:w-auto sm:max-w-xs lg:max-w-md mx-0 sm:mx-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--muted);" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                id="globalSearch" 
                                class="w-full pl-9 pr-8 py-2 rounded-2xl text-sm transition-all focus:ring-2 focus:ring-brand/20"
                                style="background: color-mix(in srgb, var(--surface) 95%, transparent); color:var(--text); border:1px solid var(--border);"
                                placeholder="Search..."
                                type="text"
                                autocomplete="off"
                                data-date="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                            >
                            
                            <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 rounded-2xl shadow-xl hidden max-h-80 overflow-y-auto z-50" style="background: color-mix(in srgb, var(--surface) 98%, transparent); border:1px solid var(--border);">
                                <div class="p-4">
                                    <div class="animate-pulse text-center" style="color:var(--muted);">
                                        Searching...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <a href="{{ route('appointments.create') }}" 
                            class="text-sm px-3 py-2 rounded-xl shadow-sm transition-all whitespace-nowrap"
                            style="background: var(--brand); color: #fff;"
                            aria-label="Create appointment">
                            + New
                        </a>
                    </div>
                                     
                    <div class="relative">
                        <button id="quickActionsBtn" class="p-2 rounded-xl transition-all hover:bg-surface/50" style="border:1px solid var(--border); color:var(--muted);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div id="statsRow" class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6 mb-4 sm:mb-8 transition-all duration-300">
            
            <div class="card-hover glass-card rounded-xl sm:rounded-2xl p-2 sm:p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="hidden sm:block absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--brand) 18%, transparent), color-mix(in srgb, var(--brand) 8%, transparent));"></div>
                
                <div class="relative flex flex-row sm:flex-col items-center sm:items-start gap-2 sm:gap-0 h-full">
                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex-shrink-0 flex items-center justify-center transition-all" style="background: linear-gradient(90deg, var(--brand), color-mix(in srgb, var(--brand) 70%, var(--accent)));">
                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    
                    <div class="flex flex-col justify-center sm:block w-full sm:w-auto overflow-hidden">
                        <div class="hidden sm:flex items-center justify-between mb-2">
                             <span class="text-xs px-2 py-1 rounded-full font-medium" style="background: color-mix(in srgb, var(--brand) 8%, transparent); color:var(--brand);">Today</span>
                        </div>
                        
                        <h3 class="text-lg sm:text-3xl font-bold leading-none sm:leading-tight" id="totalAppointments" style="color:var(--text)">{{ $total ?? 0 }}</h3>
                        <p class="text-[10px] sm:text-sm font-medium opacity-80 truncate" style="color:var(--muted)">Total Appts</p>
                    </div>
                </div>
            </div>

            <div class="card-hover glass-card rounded-xl sm:rounded-2xl p-2 sm:p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="hidden sm:block absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--success) 18%, transparent), color-mix(in srgb, var(--success) 8%, transparent));"></div>
                
                <div class="relative flex flex-row sm:flex-col items-center sm:items-start gap-2 sm:gap-0 h-full">
                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex-shrink-0 flex items-center justify-center transition-all" style="background: linear-gradient(90deg, var(--success), color-mix(in srgb, var(--success) 70%, var(--brand)));">
                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    
                    <div class="flex flex-col justify-center sm:block w-full sm:w-auto overflow-hidden">
                        <div class="hidden sm:flex items-center justify-between mb-2">
                            <div class="w-3 h-3 rounded-full animate-pulse" style="background:var(--success)"></div>
                        </div>

                        <h3 class="text-lg sm:text-3xl font-bold leading-none sm:leading-tight" id="patientsPresent" style="color:var(--text)">{{ $present ?? 0 }}</h3>
                        <p class="text-[10px] sm:text-sm font-medium opacity-80 truncate" style="color:var(--muted)">Present</p>
                        
                        <div class="hidden sm:block w-full rounded-full h-2 mt-2" style="background: color-mix(in srgb, var(--border) 40%, transparent);">
                            <div class="h-2 rounded-full" style="background:var(--success); width: {{ $total ? round(($present / $total) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-hover glass-card rounded-xl sm:rounded-2xl p-2 sm:p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="hidden sm:block absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--danger) 18%, transparent), color-mix(in srgb, var(--danger) 8%, transparent));"></div>
                
                <div class="relative flex flex-row sm:flex-col items-center sm:items-start gap-2 sm:gap-0 h-full">
                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex-shrink-0 flex items-center justify-center transition-all" style="background: linear-gradient(90deg, var(--danger), color-mix(in srgb, var(--danger) 70%, var(--brand)));">
                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    
                    <div class="flex flex-col justify-center sm:block w-full sm:w-auto overflow-hidden">
                        <div class="hidden sm:flex items-center justify-between mb-2">
                            <span class="text-xs px-2 py-1 rounded-full font-medium" style="background: color-mix(in srgb, var(--danger) 8%, transparent); color:var(--danger);">Alert</span>
                        </div>
                        
                        <h3 class="text-lg sm:text-3xl font-bold leading-none sm:leading-tight" id="missedAppointments" style="color:var(--text)">{{ $notArrived ?? 0 }}</h3>
                        <p class="text-[10px] sm:text-sm font-medium opacity-80 truncate" style="color:var(--muted)">Pending</p>
                    </div>
                </div>
            </div>

            <div class="card-hover glass-card rounded-xl sm:rounded-2xl p-2 sm:p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="hidden sm:block absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 18%, transparent), color-mix(in srgb, var(--accent) 8%, transparent));"></div>
                
                <div class="relative flex flex-row sm:flex-col items-center sm:items-start gap-3 sm:gap-0 h-full">
                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex-shrink-0 flex items-center justify-center transition-all mb-0 sm:mb-3" style="background: linear-gradient(90deg, var(--accent), color-mix(in srgb, var(--accent) 70%, var(--brand)));">
                        <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    
                    <div class="flex items-center w-full gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg sm:text-2xl font-bold leading-none" id="statNewVisits" style="color:var(--text)">{{ $newVisits ?? 0 }}</h3>
                            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider opacity-70" style="color:var(--muted)">New</p>
                        </div>
                        
                        <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>

                        <div class="flex-1">
                            <h3 class="text-lg sm:text-2xl font-bold leading-none" id="statReviews" style="color:var(--text)">{{ $reviews ?? 0 }}</h3>
                            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider opacity-70" style="color:var(--muted)">Review</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

      <div class="lg:col-span-2" id="queueSection">
        <div class="glass-card rounded-2xl p-6 mb-4" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
          
          <div id="futureModeBanner" class="hidden mb-4 p-3 rounded-xl flex items-start space-x-3" style="background: color-mix(in srgb, var(--accent) 15%, transparent); border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent); color: var(--accent);">
              <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
              <div>
                  <h4 class="font-bold text-sm">Future Schedule Mode</h4>
                  <p class="text-xs opacity-90">You are viewing appointments for a future date. Use this list for calling patients only. Do not mark attendance yet.</p>
              </div>
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-bold" id="queueTitle" style="color:var(--text)">Today's Queue</h3>
              <p class="text-sm" id="queueSubtitle" style="color:var(--muted)">Quick controls for marking attendance</p>
            </div>
            <div class="flex items-center space-x-2">
              <a href="{{ route('daily-queue') . '?date=' . ($date ?? \Carbon\Carbon::today()->format('Y-m-d')) }}" class="text-sm px-3 py-2 rounded-xl transition-all" style="background: color-mix(in srgb, var(--surface) 95%, transparent); border:1px solid var(--border); color:var(--text)">Open full queue</a>
            </div>
          </div>

          <div id="bulkActions" class="flex items-center justify-between mb-4 opacity-0 transition-opacity duration-200" aria-hidden="true">
            <div class="flex items-center space-x-2">
              <label class="inline-flex items-center text-sm" style="color:var(--muted);">
                <input id="selectAll" type="checkbox" class="form-checkbox h-4 w-4" style="accent-color:var(--brand);">
                <span class="ml-2" id="selectionCount">0 selected</span>
              </label>
            </div>
            <div class="flex items-center space-x-2">
              <button id="bulkMarkPresent" class="px-3 py-1.5 rounded-lg text-sm" style="background:var(--success); color:white;">Mark Present</button>
              <button id="bulkMarkAbsent" class="px-3 py-1.5 rounded-lg text-sm" style="background:var(--danger); color:white;">Mark Absent</button>
            </div>
          </div>

          @php
             $isFuture = \Carbon\Carbon::parse($date ?? now())->startOfDay()->gt(\Carbon\Carbon::today());
          @endphp
          <div class="divide-y" id="queueList">
            @foreach($appointments as $appt)
              @php
                $patient = $appt->patient;
                $status = $appt->status ?? 'scheduled';
                $time = $appt->time ? \Carbon\Carbon::parse($appt->time)->format('h:i A') : '-';
                $initials = trim((substr($patient->first_name ?? '',0,1) ?? '') . (substr($patient->last_name ?? '',0,1) ?? '')) ?: 'P';
              @endphp

              <div class="queue-item flex items-center justify-between py-3" data-id="{{ $appt->id }}" data-status="{{ $status }}">
                <div class="flex items-center space-x-3">
                  <input type="checkbox" class="queue-checkbox" data-id="{{ $appt->id }}">
                  <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold"
                  style="background:linear-gradient(135deg,var(--brand),color-mix(in srgb,var(--brand) 60%,var(--accent)));">
                {{ strtoupper($initials) }}
              </div><div>
                    <div class="font-medium" style="color:var(--text)">{{ $patient->first_name ?? 'Unknown' }} {{ $patient->last_name ?? '' }}</div>
                    <div class="text-xs" style="color:var(--muted)">{{ $patient->phone ?? '—' }} • {{ $patient->address ?? '—' }}</div>
                  </div>
                </div>

                <div class="text-right space-y-2">
                  <div class="text-sm" style="color:var(--muted)">{{ $time }}</div>
                  <div>
                    <span class="status-badge status-{{ $status }}" style="background: color-mix(in srgb, {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }} 10%, transparent); color: {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }}; border:1px solid color-mix(in srgb, {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }} 20%, transparent);">
                      @if($status === 'present')
                        <span class="w-2 h-2 rounded-full mr-1" style="background:var(--success)"></span> Present
                      @elseif($status === 'missed')
                        <span class="w-2 h-2 rounded-full mr-1" style="background:var(--danger)"></span> Missed
                      @else
                        <span class="w-2 h-2 rounded-full mr-1" style="background:var(--brand)"></span> Scheduled
                      @endif
                    </span>
                  </div>

                  <div class="mt-2 flex items-center justify-end space-x-2">
                   @if(!$isFuture)
                       <button class="mark-present mark-btn text-sm px-3 py-1.5 rounded-lg text-white" style="background:var(--success)" data-appt-id="{{ $appt->id }}">Mark Present</button>
                       <button class="mark-absent mark-btn text-sm px-3 py-1.5 rounded-lg text-white" style="background:var(--danger)" data-appt-id="{{ $appt->id }}">Mark Absent</button>
                   @endif
                    <a href="{{ route('patients.show', $patient->id) }}" class="px-3 py-1.5 rounded-lg text-sm" style="background: color-mix(in srgb, var(--surface) 95%, transparent); border:1px solid var(--border); color:var(--text)">View</a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-4">
            {{ $appointments->appends(request()->query())->links() }}
          </div>
        </div>
      </div>

      <aside id="sidebar" class="space-y-6">
        <div class="glass-card rounded-2xl p-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
          <div class="flex items-center justify-between mb-4">
            <div class="font-bold" style="color:var(--text)">Call Queue</div>
            <div class="text-xs" style="color:var(--muted)">Priority follow-ups</div>
          </div>

          <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse($callList as $c)
              <div class="p-3 rounded-xl border" style="background: color-mix(in srgb, var(--bg) 60%, transparent); border:1px solid var(--border);">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="font-medium" style="color:var(--text)">{{ optional($c->patient)->first_name ?? 'Unknown' }} {{ optional($c->patient)->last_name ?? '' }}</div>
                    <div class="text-xs" style="color:var(--muted)">{{ optional($c->patient)->phone ?? '—' }} • {{ $c->call_time ? \Carbon\Carbon::parse($c->call_time)->format('h:i A') : '' }}</div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button class="p-2 rounded-lg" data-action="call-now" data-name="{{ optional($c->patient)->first_name ?? 'Patient' }}" style="background: linear-gradient(90deg, var(--danger), color-mix(in srgb, var(--danger) 60%, var(--brand))); color:#fff;">Call</button>
                  </div>
                </div>
              </div>
            @empty
              <div class="text-sm" style="color:var(--muted)">No pending calls</div>
            @endforelse
          </div>

          <div class="mt-4">
            <a href="{{ route('call_logs') }}" class="w-full inline-block text-center px-4 py-2 rounded-xl" style="background:var(--brand); color:#fff;">View all calls</a>
          </div>
        </div>

        <div class="glass-card rounded-2xl p-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
          <div class="flex items-center justify-between mb-4">
            <div class="font-bold" style="color:var(--text)">Recent Activity</div>
            <div class="text-xs" style="color:var(--muted)">Last actions</div>
          </div>
          <div class="space-y-3 max-h-48 overflow-y-auto">
            @foreach($recentActivities as $act)
              <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: color-mix(in srgb, var(--surface) 90%, transparent);">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="color:var(--muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                  <div class="text-sm" style="color:var(--text)">{{ optional($act->user)->name ?? 'System' }} <span class="text-xs" style="color:var(--muted)">{{ $act->action ?? '' }}</span></div>
                  <div class="text-xs" style="color:var(--muted)">{{ $act->created_at->diffForHumans() }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </aside>

    </div>
    </div>

    <div class="fixed bottom-6 right-6 z-50">
        <button id="fabButton" class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center floating-action" aria-haspopup="true" aria-expanded="false" style="background: linear-gradient(90deg, var(--brand), var(--accent)); color:#fff;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </button>
        
        <div id="fabMenu" class="absolute bottom-16 right-0 space-y-3 opacity-0 transform scale-95 transition-all duration-200 pointer-events-none" role="menu" aria-hidden="true">
            <button class="flex items-center space-x-3 px-4 py-3 rounded-full shadow-lg floating-action" role="menuitem" style="background: color-mix(in srgb, var(--surface) 96%, transparent); color:var(--text); border:1px solid var(--border);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm font-medium">Add Patient</span>
            </button>
            <button class="flex items-center space-x-3 px-4 py-3 rounded-full shadow-lg floating-action" role="menuitem" style="background: color-mix(in srgb, var(--surface) 96%, transparent); color:var(--text); border:1px solid var(--border);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium">Schedule</span>
            </button>
        </div>
    </div>

    <div id="toastContainer" class="fixed top-20 right-6 z-50 space-y-3"></div>

    <div id="callModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="callModalTitle">
      <div class="absolute inset-0" onclick="closeModal('callModal')" aria-hidden="true" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(6px)"></div>
      <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="rounded-3xl max-w-md w-full overflow-hidden transform transition-all" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
          <div class="p-6" style="border-bottom:1px solid var(--border); background: linear-gradient(90deg, color-mix(in srgb, var(--surface) 90%, transparent), color-mix(in srgb, var(--bg) 90%, transparent));">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--brand), var(--accent));">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                </div>
                <div>
                  <h3 id="callModalTitle" class="text-lg font-bold" style="color:var(--text)">Call Patient</h3>
                  <p class="text-sm" id="callPatientName" style="color:var(--muted)">Kofi Asante</p>
                </div>
              </div>
              <button onclick="closeModal('callModal')" class="p-2 rounded-lg transition-colors" aria-label="Close call modal" style="color:var(--muted);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="p-6">
            <p class="text-sm" style="color:var(--muted)">Call actions for <strong id="callPatientNameBody" style="color:var(--text)">Kofi Asante</strong></p>
            <div class="mt-4 flex gap-3">
              <button class="px-4 py-2 rounded-lg" id="callConfirmBtn" style="background:var(--success); color:#fff;">Call</button>
              <button class="px-4 py-2 rounded-lg" onclick="closeModal('callModal')" style="background: color-mix(in srgb, var(--surface) 96%, transparent); border:1px solid var(--border); color:var(--text)">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>



<style>
/* Small UX CSS additions */
.active-result {
  background-color: rgba(99,102,241,0.06);
  outline: 2px solid rgba(99,102,241,0.12);
}
</style>

<script>
  window.__DASHBOARD = {
    appointments: {!! json_encode($appointments->items()) !!}, 
    appointments_meta: {!! json_encode([
      'current_page' => $appointments->currentPage(),
      'last_page' => $appointments->lastPage(),
      'per_page' => $appointments->perPage(),
      'total' => $appointments->total()
    ]) !!},
    total: {{ (int) ($total ?? 0) }},
    present: {{ (int) ($present ?? 0) }},
    notArrived: {{ (int) ($notArrived ?? 0) }},
    callList: {!! json_encode($callList->map->only(['id','call_time','patient'])->values()) !!},
    recentActivities: {!! json_encode($recentActivities->map->only(['id','action','created_at','user'])->values()) !!},
    currentDate: "{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}",
    percentageChange: {{ $percentageChange ?? 0 }},
    changeDirection: "{{ $changeDirection ?? '' }}",
    isFuture: {{ \Carbon\Carbon::parse($date ?? now())->startOfDay()->gt(\Carbon\Carbon::today()) ? 'true' : 'false' }}
  };
</script>

@push('scripts')
<script>
(() => {
  /* ==========================================================================
     UTILITIES & HELPERS
     ========================================================================== */
  const $ = (sel, root = document) => (root || document).querySelector(sel);
  const $$ = (sel, root = document) => Array.from((root || document).querySelectorAll(sel));
  const metaCsrf = document.querySelector('meta[name="csrf-token"]');
  const CSRF = metaCsrf ? metaCsrf.getAttribute('content') : null;

  /* --- Sophisticated Animation Helper --- */
  function animateValue(obj, start, end, duration) {
    if (!obj || start === end) return;
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      // Easing function for smoother feel (easeOutQuad)
      const ease = 1 - (1 - progress) * (1 - progress); 
      obj.innerHTML = Math.floor(ease * (end - start) + start);
      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        obj.innerHTML = end;
      }
    };
    window.requestAnimationFrame(step);
  }

  /* --- Visual Flash Helper --- */
  function flashCard(elementId, colorClass) {
    const el = document.getElementById(elementId);
    if(!el) return;
    const card = el.closest('.glass-card');
    if(!card) return;
    
    // Add a temporary glow/flash
    card.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease';
    const originalShadow = card.style.boxShadow;
    const originalBorder = card.style.borderColor;
    
    if(colorClass.includes('green')) {
        card.style.boxShadow = '0 0 15px rgba(16, 185, 129, 0.4)'; // Green glow
        card.style.borderColor = 'rgba(16, 185, 129, 0.6)';
    } else {
        card.style.boxShadow = '0 0 15px rgba(239, 68, 68, 0.4)'; // Red glow
        card.style.borderColor = 'rgba(239, 68, 68, 0.6)';
    }

    setTimeout(() => {
        card.style.boxShadow = originalShadow;
        card.style.borderColor = originalBorder;
    }, 400);
  }

  function ajaxPost(url, body = {}) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}) },
      body: JSON.stringify(body)
    }).then(async r => ({ ok: r.ok, status: r.status, body: await r.json().catch(()=>({})) }));
  }

  function ajaxGet(url) {
    return fetch(url, {
      method: 'GET',
      headers: { 'Accept': 'application/json', ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}) }
    }).then(async r => ({ ok: r.ok, status: r.status, body: await r.json().catch(()=>({})) }));
  }

  function showToast(message, type = 'info', timeout = 3500) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const el = document.createElement('div');
    el.className = 'px-4 py-2 rounded-xl shadow-md max-w-sm text-sm flex items-center justify-between space-x-3 transition-all duration-300 transform translate-y-2 opacity-0';
    el.setAttribute('role', 'status');
    el.innerHTML = `<div class="flex-1 font-medium">${message}</div>`;

    if (type === 'error') el.classList.add('bg-red-500','text-white');
    else if (type === 'success') el.classList.add('bg-emerald-500','text-white');
    else el.classList.add('bg-white','text-gray-800');

    container.appendChild(el);
    requestAnimationFrame(() => { el.classList.remove('translate-y-2', 'opacity-0'); });
    
    setTimeout(() => {
        el.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => el.remove(), 300);
    }, timeout);
  }

  /* ==========================================================================
     DASHBOARD LOGIC
     ========================================================================== */
  window.__DASHBOARD = window.__DASHBOARD || {};
  const state = window.__DASHBOARD;
  if (!Array.isArray(state.appointments)) state.appointments = [];

  // Initialize Dates
  const currentDateEl = $('#currentDate');
  if (currentDateEl && state.currentDate) updateDateHeader(state.currentDate);

  function updateDateHeader(dateStr) {
      const dateObj = new Date(dateStr + 'T00:00:00');
      if(currentDateEl) currentDateEl.textContent = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' }) + ' Schedule';
  }

  // Visual Percentage Update
  const percentageChangeEl = $('#percentageChange');
  if (percentageChangeEl && state.changeDirection && state.percentageChange !== undefined) {
    const directionClass = state.changeDirection === '+' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
    percentageChangeEl.className = `flex items-center text-xs ${directionClass}`;
    percentageChangeEl.innerHTML = `
      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
      ${state.changeDirection}${state.percentageChange}% from yesterday
    `;
  }

  function getIntFromEl(el) { return el ? (parseInt(el.textContent.replace(/[^\d]/g, ''), 10) || 0) : 0; }
  function setIntToEl(el, n) { if (el) el.textContent = String(n); }

  function updateMiniChart(present, total) {
    const c = document.getElementById('miniChart');
    if (!c) return;
    const ctx = c.getContext('2d');
    const w = c.width, h = c.height;
    ctx.clearRect(0,0,w,h);
    const pct = total ? Math.round((present / total) * 100) : 0;
    ctx.fillStyle = 'rgba(0,0,0,0.05)'; ctx.fillRect(0,0,w,h);
    ctx.fillStyle = 'rgba(0,0,0,0.18)'; ctx.fillRect(0, Math.round(h*0.25), Math.round(w * (pct/100)), Math.round(h*0.5));
    
    // Animate the text percentage
    const rateEl = c.closest('.glass-card')?.querySelector('h3');
    if (rateEl) {
        const currentRate = parseInt(rateEl.textContent) || 0;
        animateValue(rateEl, currentRate, pct, 800);
        setTimeout(() => rateEl.textContent = pct + '%', 810);
    }
  }

  /* ==========================================================================
     QUEUE RENDERING (THE CORE LIST)
     ========================================================================== */
  const VISIBLE_LIMIT = 200;
  const queueList = $('#queueList');

  function escapeHtml(s) {
    if (!s && s !== 0) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,"&#039;");
  }

  function makeQueueItem(appt) {
    const pid = (appt.patient && (appt.patient.id || appt.patient_id)) || (appt.patient_id || 'unknown');
    const fname = appt.patient?.first_name ?? appt.first_name ?? 'Unknown';
    const lname = appt.patient?.last_name ?? appt.last_name ?? '';
    const phone = appt.patient?.phone ?? '';
    const initials = ((fname[0]||'') + (lname[0]||'')).toUpperCase() || 'P';
    const status = (appt.status || 'scheduled').toLowerCase();
    
    let time = '-';
    if (appt.time) {
      try {
        const t = appt.time.length <= 8 ? `1970-01-01T${appt.time}` : appt.time;
        time = new Date(t).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      } catch (e) { time = appt.time; }
    }

    let actionButtons = '';
    if (!state.isFuture) {
        actionButtons = `
            <button class="mark-present px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-sm transition-transform active:scale-95 shadow-sm hover:shadow-md" data-appt-id="${appt.id}">Mark Present</button>
            <button class="mark-absent px-3 py-1.5 bg-red-500 text-white rounded-lg text-sm transition-transform active:scale-95 shadow-sm hover:shadow-md" data-appt-id="${appt.id}">Mark Absent</button>
        `;
    }

    return `
      <div class="queue-item flex items-center justify-between py-3 transition-all hover:bg-black/5 rounded-lg px-2 -mx-2 animate-fade-in" 
           data-appointment-id="${appt.id}" 
           data-status="${status}">
        <div class="flex items-center space-x-3">
          <input type="checkbox" class="queue-checkbox form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500" data-id="${appt.id}">
          <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0 shadow-md" 
               style="background:linear-gradient(135deg,#60a5fa 0%,#6366f1 100%)">${initials}</div>
          <div>
            <div class="font-medium text-gray-900 dark:text-gray-100">${escapeHtml(fname)} ${escapeHtml(lname)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(phone || '—')}</div>
          </div>
        </div>
        <div class="text-right space-y-2">
          <div class="text-sm font-mono text-gray-500">${escapeHtml(time)}</div>
          <div class="mt-2 flex items-center justify-end space-x-2">
            ${actionButtons}
            <a href="/patients/${pid}" class="px-3 py-1.5 border rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">View</a>
          </div>
        </div>
      </div>
    `;
  }

  function renderQueueFromState() {
    // === CRITICAL FIX: The Filter ===
    // This ensures that items marked present/missed NEVER show up in this list,
    // even if the server sent them on page load.
    const activeItems = (state.appointments || []).filter(a => 
        ['scheduled', 'queued'].includes(a.status || 'scheduled')
    );

    const list = activeItems.slice(0, VISIBLE_LIMIT);
    
    if (!queueList) return;
    if (list.length === 0) {
      queueList.innerHTML = `
        <div class="p-12 text-center flex flex-col items-center animate-fade-in">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">All Caught Up!</h3>
            <p class="text-gray-500 mt-1">No pending appointments for today.</p>
        </div>`;
      return;
    }
    queueList.innerHTML = list.map(makeQueueItem).join('');
  }

  // Initial Render
  renderQueueFromState();
  updateMiniChart(state.present, state.total);

  /* ==========================================================================
     ACTION HANDLER (Mark Present/Absent)
     ========================================================================== */
  async function handleMark(ids, endpoint, successMessage) {
    if (!Array.isArray(ids) || ids.length === 0) return;

    // 1. Optimistic UI Update: Animate removal immediately
    ids.forEach(id => {
        const dom = document.querySelector(`[data-appointment-id="${id}"]`);
        if(dom) {
            dom.style.transition = "all 0.3s ease";
            dom.style.opacity = "0";
            dom.style.transform = "translateX(20px)";
        }
    });

    try {
      const payload = { appointment_ids: ids };
      const res = await ajaxPost(endpoint, payload);

      if (!res.ok) {
        renderQueueFromState(); // Revert visual changes if failed
        alert('Action failed. Please check connection.');
        return;
      }

      // 2. Update Local State (Remove processed items)
      ids.forEach(id => {
        const idx = (state.appointments || []).findIndex(a => Number(a.id) === Number(id));
        if (idx !== -1) {
            // Update status so filter removes it permanently
            const newStatus = endpoint.includes('mark-present') ? 'present' : 'missed';
            state.appointments[idx].status = newStatus;
        }
      });

      // 3. Sophisticated Stats Update
      const presentEl = document.getElementById('patientsPresent');
      const missedEl = document.getElementById('missedAppointments');
      const newVisitsEl = document.getElementById('statNewVisits');
      const reviewsEl = document.getElementById('statReviews');
      
      const oldPresent = getIntFromEl(presentEl);
      const oldMissed = getIntFromEl(missedEl);

      let newPresent = oldPresent;
      let newMissed = oldMissed;

      // Use server numbers if reliable
      if (res.body && typeof res.body.present === 'number') {
         newPresent = res.body.present;
         newMissed = res.body.notArrived;
         
         // Update new cards if server sent them
         if(res.body.newVisits !== undefined) animateValue(newVisitsEl, getIntFromEl(newVisitsEl), res.body.newVisits, 800);
         if(res.body.reviews !== undefined) animateValue(reviewsEl, getIntFromEl(reviewsEl), res.body.reviews, 800);
         
      } else {
         // Fallback local calculation
         const isPresentAction = endpoint.includes('mark-present');
         newPresent = isPresentAction ? oldPresent + ids.length : oldPresent;
         newMissed = Math.max(0, oldMissed - ids.length);
      }

      // 4. Trigger Animations
      if (newPresent !== oldPresent) {
          animateValue(presentEl, oldPresent, newPresent, 600);
          flashCard('patientsPresent', 'bg-green-50');
      }
      if (newMissed !== oldMissed) {
          animateValue(missedEl, oldMissed, newMissed, 600);
      }

      updateMiniChart(newPresent, state.total);

      // 5. Hard Re-render list after delay to clean up DOM
      setTimeout(() => {
          renderQueueFromState();
          // Clear selections
          const selectAll = $('#selectAll');
          if(selectAll) selectAll.checked = false;
          $$('.queue-checkbox').forEach(cb => cb.checked = false);
          $('#selectionCount').textContent = '0 selected';
          $('#bulkActions').style.opacity = '0';
          $('#bulkActions').style.pointerEvents = 'none';
      }, 350);

      showToast(successMessage, 'success');

    } catch (err) {
      console.error(err);
      renderQueueFromState();
    }
  }

  // Delegated Clicks
  document.addEventListener('click', (e) => {
    const presentBtn = e.target.closest('.mark-present');
    if (presentBtn) {
      const id = presentBtn.dataset.apptId;
      handleMark([parseInt(id,10)], '/daily-queue/mark-present', 'Marked present');
    }
    const absentBtn = e.target.closest('.mark-absent');
    if (absentBtn) {
      const id = absentBtn.dataset.apptId;
      handleMark([parseInt(id,10)], '/daily-queue/mark-absent', 'Marked absent');
    }
  });

  const bulkMarkPresent = $('#bulkMarkPresent');
  const bulkMarkAbsent = $('#bulkMarkAbsent');
  
  if (bulkMarkPresent) {
    bulkMarkPresent.addEventListener('click', () => {
      const ids = $$('.queue-checkbox', queueList).filter(c=>c.checked).map(c => parseInt(c.dataset.id,10));
      handleMark(ids, '/daily-queue/mark-present', 'Marked selected present');
    });
  }
  if (bulkMarkAbsent) {
    bulkMarkAbsent.addEventListener('click', () => {
      const ids = $$('.queue-checkbox', queueList).filter(c=>c.checked).map(c => parseInt(c.dataset.id,10));
      handleMark(ids, '/daily-queue/mark-absent', 'Marked selected absent');
    });
  }

  /* ==========================================================================
     DATE & SEARCH LOGIC
     ========================================================================== */
  const dateFilter = $('#dateFilter');
  const statusFilter = $('#statusFilter');
  
  const queueTitle = $('#queueTitle');
  const queueSubtitle = $('#queueSubtitle');
  const futureBanner = $('#futureModeBanner');

  function getDateDiff(dateString) {
      const today = new Date();
      today.setHours(0,0,0,0);
      const target = new Date(dateString + 'T00:00:00');
      target.setHours(0,0,0,0);
      return Math.ceil((target - today) / (1000 * 60 * 60 * 24)); 
  }

  async function loadDataForDate(newDate, status = '') {
    showToast('Updating schedule...', 'info');

    const newUrl = new URL(window.location);
    newUrl.searchParams.set('date', newDate);
    if(status) newUrl.searchParams.set('status', status);
    else newUrl.searchParams.delete('status');
    window.history.pushState({path: newUrl.href}, '', newUrl.href);

    const diff = getDateDiff(newDate);
    state.isFuture = diff > 0;
    
    // UI Mode Update
    if (state.isFuture) {
        if(queueTitle) queueTitle.textContent = "Tomorrow's Schedule";
        if(queueSubtitle) queueSubtitle.textContent = "Call List Mode • Preparing for upcoming appointments";
        if(futureBanner) futureBanner.classList.remove('hidden');
        if(currentDateEl) currentDateEl.style.color = "var(--accent)";
    } else {
        if(queueTitle) queueTitle.textContent = "Today's Queue";
        if(queueSubtitle) queueSubtitle.textContent = "Quick controls for marking attendance";
        if(futureBanner) futureBanner.classList.add('hidden');
        if(currentDateEl) currentDateEl.style.color = "var(--muted)";
    }

    if(dateFilter) dateFilter.value = newDate;
    updateDateHeader(newDate);

    try {
      const [statsRes, apptsRes] = await Promise.all([
        ajaxGet(`/dashboard/stats?date=${newDate}${status ? '&status=' + status : ''}`),
        ajaxGet(`/dashboard/appointments?date=${newDate}${status ? '&status=' + status : ''}`)
      ]);

      if (statsRes.ok && statsRes.body) {
        const oldTotal = state.total || 0;
        const oldPresent = state.present || 0;
        const oldMissed = state.notArrived || 0;
        const oldNew = state.newVisits || 0;
        const oldRev = state.reviews || 0;

        state.total = statsRes.body.total || 0;
        state.present = statsRes.body.present || 0;
        state.notArrived = statsRes.body.notArrived || 0;
        state.newVisits = statsRes.body.newVisits || 0;
        state.reviews = statsRes.body.reviews || 0;
        
        // Animate stats change
        const totalEl = $('#totalAppointments');
        const presentEl = $('#patientsPresent');
        const missedEl = $('#missedAppointments');
        const newVisitsEl = document.getElementById('statNewVisits');
        const reviewsEl = document.getElementById('statReviews');

        if(totalEl) animateValue(totalEl, oldTotal, state.total, 800);
        if(presentEl) animateValue(presentEl, oldPresent, state.present, 800);
        if(missedEl) animateValue(missedEl, oldMissed, state.notArrived, 800);
        if(newVisitsEl) animateValue(newVisitsEl, oldNew, state.newVisits, 800);
        if(reviewsEl) animateValue(reviewsEl, oldRev, state.reviews, 800);
        
        updateMiniChart(state.present, state.total);
      }

      if (apptsRes.ok && Array.isArray(apptsRes.body)) {
        state.appointments = apptsRes.body;
        renderQueueFromState();
        
        // Clear selections
        const selectAll = $('#selectAll');
        if(selectAll) selectAll.checked = false;
        $$('.queue-checkbox').forEach(cb => cb.checked = false);
        $('#selectionCount').textContent = '0 selected';
        $('#bulkActions').style.opacity = '0';
        $('#bulkActions').style.pointerEvents = 'none';
      }

    } catch (err) {
      console.error(err);
      showToast('Failed to load data', 'error');
    }
  }

  if (dateFilter) {
    dateFilter.addEventListener('change', (e) => {
      const newDate = e.target.value;
      if (!newDate) return;
      loadDataForDate(newDate, statusFilter.value);
    });
  }

  if (statusFilter) {
    statusFilter.addEventListener('change', (e) => {
      loadDataForDate(dateFilter.value, e.target.value);
    });
  }

  const tomorrowBtn = $('#tomorrowBtn');
  if (tomorrowBtn) {
      tomorrowBtn.addEventListener('click', () => {
          const tomorrow = new Date();
          tomorrow.setDate(tomorrow.getDate() + 1);
          const yyyy = tomorrow.getFullYear();
          const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
          const dd = String(tomorrow.getDate()).padStart(2, '0');
          loadDataForDate(`${yyyy}-${mm}-${dd}`, '');
      });
  }

  // Selection Logic
  const selectAll = $('#selectAll');
  const selectionCountEl = $('#selectionCount');
  
  if (queueList) {
    queueList.addEventListener('change', e => {
      if (e.target.classList.contains('queue-checkbox')) updateSelectionCount();
    });
  }
  if (selectAll) {
    selectAll.addEventListener('change', e => {
      $$('.queue-checkbox', queueList).forEach(cb => cb.checked = e.target.checked);
      updateSelectionCount();
    });
  }

  function updateSelectionCount() {
      const checked = $$('.queue-checkbox:checked', queueList).length;
      if (selectionCountEl) selectionCountEl.textContent = `${checked} selected`;
      const bulkActions = $('#bulkActions');
      if(bulkActions) {
          bulkActions.style.opacity = checked > 0 ? '1' : '0';
          bulkActions.style.pointerEvents = checked > 0 ? 'auto' : 'none';
      }
  }

  /* ---------- Modals ---------- */
  window.closeModal = (id) => {
    const m = document.getElementById(id);
    if(m) m.classList.add('hidden');
  };

  document.addEventListener('click', (e) => {
      if(e.target.closest('[data-action="call-now"]')) {
          const btn = e.target.closest('[data-action="call-now"]');
          const name = btn.dataset.name;
          $('#callPatientName').textContent = name;
          $('#callPatientNameBody').textContent = name;
          const modal = document.getElementById('callModal');
          if(modal) {
              modal.classList.remove('hidden');
              const select = modal.querySelector('select[name="result"]');
              if(select) setTimeout(() => select.focus(), 100);
          }
      }
  });

  /* ---------- Search (Basic) ---------- */
  const searchInput = $('#globalSearch');
  const resultsBox = $('#searchResults');
  let searchTimer = null;

  if (searchInput && resultsBox) {
      searchInput.addEventListener('input', (e) => {
          const q = e.target.value.trim();
          clearTimeout(searchTimer);
          if (q.length < 2) { resultsBox.classList.add('hidden'); return; }
          
          resultsBox.classList.remove('hidden');
          resultsBox.innerHTML = `<div class="p-4 text-center text-gray-500">Searching...</div>`;

          searchTimer = setTimeout(async () => {
              try {
                  const res = await ajaxGet(`/dashboard/search?term=${encodeURIComponent(q)}`);
                  if(!res.ok || !res.body.length) {
                      resultsBox.innerHTML = `<div class="p-4 text-center text-gray-500">No results found</div>`;
                      return;
                  }
                  resultsBox.innerHTML = `<div class="divide-y divide-gray-100">` + 
                      res.body.map(p => `
                          <a href="/patients/${p.id}" class="block p-3 hover:bg-gray-50 transition-colors">
                              <div class="font-medium text-gray-900">${escapeHtml(p.label)}</div>
                              <div class="text-xs text-gray-500">${escapeHtml(p.phone || 'No phone')}</div>
                          </a>
                      `).join('') + `</div>`;
              } catch(e) { resultsBox.innerHTML = `<div class="p-4 text-red-500">Error</div>`; }
          }, 300);
      });

      document.addEventListener('click', e => {
          if (!e.target.closest('#globalSearch') && !e.target.closest('#searchResults')) {
              resultsBox.classList.add('hidden');
          }
      });
  }

})();
</script>
@endpush

@endsection