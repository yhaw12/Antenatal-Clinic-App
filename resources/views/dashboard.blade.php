@extends('layouts.app')

@section('title', 'ANC Dashboard')

@section('content')
    
<div class="min-h-screen" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    <!-- Enhanced Header -->
    <header class="sticky top-0 z-40" style="backdrop-filter: blur(12px); background: color-mix(in srgb, var(--bg) 95%, transparent); border-bottom: 1px solid var(--border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 flex-wrap gap-2">
                <!-- Left Section -->
                <div class="flex items-center space-x-4 flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--brand), var(--accent));">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        {{-- title intentionally left commented as in original --}}
                        <!-- Add this after your logo SVG (header left) -->
                    <div class="hidden sm:block ml-2">
                      <div id="currentDate" class="text-sm font-medium" style="color:var(--muted)"></div>
                    </div>

                    </div>
                </div>

                
                <!-- Right Section -->
                <div class="order-2 sm:order-3 flex items-center space-x-2 justify-end flex-shrink-0">
                    <!-- Date Filter (hidden on xs) -->
                    <input 
                        type="date" 
                        id="dateFilter" 
                        class="hidden sm:inline-flex px-3 py-2 rounded-xl text-sm transition-all"
                        style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--text); border:1px solid var(--border);"
                        value="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                    >
                    
                    <!-- Status Filter (hidden on xs) -->
                    <select id="statusFilter" class="hidden sm:inline-flex px-3 py-2 rounded-xl text-sm transition-all" style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--text); border:1px solid var(--border);">
                        <option value="" style="color:var(--text)">All</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="present">Present</option>
                        <option value="missed">Missed</option>
                    </select>

                <!-- (search) - will collapse full-width on mobile -->
                <div class="order-3 sm:order-2 flex-1 w-full sm:w-auto sm:max-w-2xl mx-0 sm:mx-8">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--muted);" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            id="globalSearch" 
                            class="w-full pl-10 pr-10 py-2 rounded-2xl text-sm transition-all"
                            style="background: color-mix(in srgb, var(--surface) 95%, transparent); color:var(--text); border:1px solid var(--border);"
                            placeholder="Search patients, appointments... (Press / to focus)"
                            type="text"
                            autocomplete="off"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="searchResults"
                            aria-haspopup="listbox"
                            data-date="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <kbd class="px-2 py-1 text-xs font-medium rounded" style="background: color-mix(in srgb, var(--surface) 85%, transparent); color:var(--muted); border:1px solid var(--border);">/</kbd>
                        </div>
                        
                        <!-- Enhanced Search Results -->
                        <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 rounded-2xl shadow-2xl hidden max-h-96 overflow-y-auto z-50" style="background: color-mix(in srgb, var(--surface) 98%, transparent); border:1px solid var(--border);">
                            <div class="p-4">
                                <div class="animate-pulse text-center" style="color:var(--muted);">
                                    <div class="w-6 h-6 mx-auto mb-2 bg-gray-300 rounded-full"></div>
                                    Searching...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Create appointment (prominent) - visible on md+; on small screens Quick Actions and FAB remain -->
                    <div class="hidden md:block">
                        <a href="{{ route('appointments.create') }}" 
                            class="text-sm px-3 py-2 rounded-xl shadow-sm transition-all"
                            style="background: var(--brand); color: #fff;"
                            aria-label="Create appointment">
                            + New Appointment
                        </a>
                    </div>
                                  

                    <!-- Quick Actions -->
                    <div class="relative">
                        <button id="quickActionsBtn" class="p-2 rounded-xl transition-all" style="background: color-mix(in srgb, var(--surface) 95%, transparent); border:1px solid var(--border); color:var(--muted);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="color:var(--muted);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Enhanced Stats Row -->
        <div id="statsRow" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 transition-all duration-300">
            <!-- Total Appointments -->
            <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--brand) 18%, transparent), color-mix(in srgb, var(--brand) 8%, transparent));"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--brand), color-mix(in srgb, var(--brand) 70%, var(--accent)));">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium" style="background: color-mix(in srgb, var(--brand) 8%, transparent); color:var(--brand);">Today</span>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-2xl font-bold" id="totalAppointments" style="color:var(--text)">{{ $total ?? 0 }}</h3>
                        <p class="text-sm" style="color:var(--muted)">Total Appointments</p>
                        <div class="flex items-center text-xs" id="percentageChange" style="color: {{ $changeDirection === '+' ? 'var(--success)' : 'var(--danger)'}}">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            {{ $changeDirection }}{{ $percentageChange }}% from yesterday
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patients Present -->
            <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--success) 18%, transparent), color-mix(in srgb, var(--success) 8%, transparent));"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--success), color-mix(in srgb, var(--success) 70%, var(--brand)));">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="w-3 h-3 rounded-full animate-pulse" style="background:var(--success)"></div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-2xl font-bold" id="patientsPresent" style="color:var(--text)">{{ $present ?? 0 }}</h3>
                        <p class="text-sm" style="color:var(--muted)">Patients Present</p>
                        <!-- Replace the existing progress bar block with this -->
                          <div class="w-full rounded-full h-2" role="progressbar"
                              aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $total ? round(($present / $total) * 100) : 0 }}" data-progress
                              style="background: color-mix(in srgb, var(--border) 40%, transparent);">
                            <div class="h-2 rounded-full" style="width: {{ $total ? round(($present / $total) * 100) : 0 }}%" data-progress-fill></div>
                          </div>

                    </div>
                </div>
            </div>

            <!-- Missed Appointments -->
            <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--danger) 18%, transparent), color-mix(in srgb, var(--danger) 8%, transparent));"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--danger), color-mix(in srgb, var(--danger) 70%, var(--brand)));">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium" style="background: color-mix(in srgb, var(--danger) 8%, transparent); color:var(--danger);">Alert</span>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-2xl font-bold" id="missedAppointments" style="color:var(--text)">{{ $notArrived ?? 0 }}</h3>
                        <p class="text-sm" style="color:var(--muted)">Follow-ups Needed</p>
                        <div class="flex items-center text-xs" style="color:var(--danger)">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            Requires attention
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-2xl" style="background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 18%, transparent), color-mix(in srgb, var(--accent) 8%, transparent));"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--accent), color-mix(in srgb, var(--accent) 70%, var(--brand)));">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <canvas id="miniChart" width="50" height="30"></canvas>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-2xl font-bold" style="color:var(--text)">{{ $total ? round(($present / $total) * 100) : 0 }}%</h3>
                        <p class="text-sm" style="color:var(--muted)">Attendance Rate</p>
                        <p class="text-xs" style="color:var(--muted)">This week average</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main grid: Queue + Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- Queue Section (left big column) -->
      <div class="lg:col-span-2" id="queueSection">
        <div class="glass-card rounded-2xl p-6 mb-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold" style="color:var(--text)">Today's Queue</h3>
              <p class="text-sm" style="color:var(--muted)">Quick controls for marking attendance</p>
            </div>
            <div class="flex items-center space-x-2">
              <a href="{{ route('daily-queue') . '?date=' . ($date ?? \Carbon\Carbon::today()->format('Y-m-d')) }}" class="text-sm px-3 py-2 rounded-xl transition-all" style="background: color-mix(in srgb, var(--surface) 95%, transparent); border:1px solid var(--border); color:var(--text)">Open full queue</a>
            </div>
          </div>

          <!-- Bulk toolbar (appears when selections exist) -->
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
                   <button class="mark-present ... text-sm" data-id="{{ $appt->id }}">Mark Present</button>
                   <button class="mark-absent ... text-sm" data-id="{{ $appt->id }}">Mark Absent</button>
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

      <!-- Sidebar -->
      <aside id="sidebar" class="space-y-6">
        <!-- Call Queue -->
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

        <!-- Recent Activity -->
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

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button id="fabButton" class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center floating-action" aria-haspopup="true" aria-expanded="false" style="background: linear-gradient(90deg, var(--brand), var(--accent)); color:#fff;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </button>
        
        <!-- FAB Menu -->
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

    <!-- Enhanced Toast Container -->
    <div id="toastContainer" class="fixed top-20 right-6 z-50 space-y-3"></div>

    <!-- Call Modal (cleaned) -->
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
  // Server-provided data for client-side rendering (safe JSON)
  window.__DASHBOARD = {
    appointments: {!! json_encode($appointments->items()) !!}, // current page items as array
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
    changeDirection: "{{ $changeDirection ?? '' }}"
  };
</script>

@push('scripts')
<script>
(() => {
  /* ---------- Utilities & CSRF (unchanged) ---------- */
  const $ = (sel, root = document) => (root || document).querySelector(sel);
  const $$ = (sel, root = document) => Array.from((root || document).querySelectorAll(sel));
  const metaCsrf = document.querySelector('meta[name="csrf-token"]');
  const CSRF = metaCsrf ? metaCsrf.getAttribute('content') : null;

  function ajaxPost(url, body = {}) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {})
      },
      body: JSON.stringify(body)
    }).then(async r => {
      let j = null;
      try { j = await r.json(); } catch(e){/* ignore parse errors */ }
      return { ok: r.ok, status: r.status, body: j };
    });
  }

  function ajaxGet(url) {
    return fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {})
      }
    }).then(async r => {
      let j = null;
      try { j = await r.json(); } catch(e){/* ignore parse errors */ }
      return { ok: r.ok, status: r.status, body: j };
    });
  }

  /* ---------- Accessible Toasts (replacement) ---------- */
  function showToast(message, type = 'info', timeout = 3500) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const el = document.createElement('div');
    el.className = 'px-4 py-2 rounded-xl shadow-md max-w-sm text-sm flex items-center justify-between space-x-3 transition-opacity';
    el.style.transition = 'opacity 300ms';
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
    el.tabIndex = 0;
    el.innerHTML = `<div class="flex-1">${message}</div><button aria-label="Dismiss toast" class="ml-3 text-xs">✕</button>`;

    if (type === 'error') el.classList.add('bg-red-50','text-red-700');
    else if (type === 'success') el.classList.add('bg-green-50','text-green-700');
    else el.classList.add('bg-white','text-gray-800');

    container.appendChild(el);
    // Dismiss handlers
    const removeFn = () => { el.style.opacity = '0'; el.addEventListener('transitionend', () => el.remove()); };
    const dismissBtn = el.querySelector('button');
    let timer = setTimeout(removeFn, timeout);
    el.addEventListener('click', (ev) => { if (ev.target === dismissBtn) { clearTimeout(timer); removeFn(); } });
    el.addEventListener('keydown', (ev) => { if (ev.key === 'Escape') { clearTimeout(timer); removeFn(); } });
  }

  /* ---------- Dashboard state (single source of truth) ---------- */
  window.__DASHBOARD = window.__DASHBOARD || {};
  const state = window.__DASHBOARD;

  // Ensure appointments is an array (may be paginated object earlier)
  if (!Array.isArray(state.appointments)) {
    try {
      state.appointments = state.appointments?.data || state.appointments || [];
    } catch (e) {
      state.appointments = [];
    }
  }

  // Set initial current date display (if element exists)
  const currentDateEl = $('#currentDate');
  if (currentDateEl && state.currentDate) {
    const dateObj = new Date(state.currentDate + 'T00:00:00');
    currentDateEl.textContent = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' }) + ' Schedule';
  }

  // Update percentage change display (unchanged behavior but safer)
  const percentageChangeEl = $('#percentageChange');
  if (percentageChangeEl && state.changeDirection && state.percentageChange !== undefined) {
    const directionClass = state.changeDirection === '+' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
    percentageChangeEl.className = `flex items-center text-xs ${directionClass}`;
    percentageChangeEl.innerHTML = `
      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
      </svg>
      ${state.changeDirection}${state.percentageChange}% from yesterday
    `;
  }

  /* ---------- helpers for counters & chart (updated) ---------- */
  function getIntFromEl(el) {
    if (!el) return 0;
    const v = parseInt(el.textContent.trim().replace(/[^\d-]/g, ''), 10);
    return Number.isFinite(v) ? v : 0;
  }
  function setIntToEl(el, n) {
    if (!el) return;
    el.textContent = String(n);
  }

  function updateMiniChart(present, total) {
    const c = document.getElementById('miniChart');
    if (!c) return;
    const ctx = c.getContext('2d');
    const w = c.width, h = c.height;
    ctx.clearRect(0,0,w,h);
    const pct = total ? Math.round((present / total) * 100) : 0;
    ctx.fillStyle = 'rgba(0,0,0,0.05)'; ctx.fillRect(0,0,w,h);
    ctx.fillStyle = 'rgba(0,0,0,0.18)'; ctx.fillRect(0, Math.round(h*0.25), Math.round(w * (pct/100)), Math.round(h*0.5));

    // Update progress fill element if present (robust selector)
    const progressContainer = document.querySelector('[data-progress]');
    if (progressContainer) {
      const fill = progressContainer.querySelector('[data-progress-fill]');
      if (fill) {
        fill.style.width = `${pct}%`;
        progressContainer.setAttribute('aria-valuenow', String(pct));
      }
    } else {
      // fallback: try to find the progress bar by previous DOM heuristics
      const altProgress = document.querySelector('#patientsPresent')?.closest('.glass-card')?.querySelector('div[style*="width"]');
      if (altProgress) altProgress.style.width = `${pct}%`;
    }
    // Update attendance rate text
    const rateEl = c.closest('.glass-card')?.querySelector('h3');
    if (rateEl) rateEl.textContent = `${pct}%`;
  }

  /* ---------- Queue rendering (MAX visible) ---------- */
  const VISIBLE_LIMIT = 5;
  const queueList = $('#queueList');

  function escapeHtml(s) {
    if (!s && s !== 0) return '';
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,"&#039;");
  }

  function makeQueueItem(appt) {
    const pid = (appt.patient && (appt.patient.id || appt.patient_id)) || (appt.patient_id || 'unknown');
    const fname = appt.patient?.first_name ?? appt.first_name ?? 'Unknown';
    const lname = appt.patient?.last_name ?? appt.last_name ?? '';
    const phone = appt.patient?.phone ?? '';
    const address = appt.patient?.address ?? '';
    const initials = ((fname[0]||'') + (lname[0]||'')).toUpperCase() || 'P';
    const status = (appt.status || 'scheduled').toLowerCase();
    // Normalize time parsing: accept 'HH:MM:SS' or 'HH:MM'
    let time = '-';
    if (appt.time) {
      try {
        // If the time contains seconds or is already full time string, build a Date on epoch for consistent formatting
        const t = appt.time.length <= 8 ? `1970-01-01T${appt.time}` : appt.time;
        time = new Date(t).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      } catch (e) {
        time = appt.time;
      }
    } else if (appt.time === '-') time = '-';

    const badgeHtml = status === 'present'
      ? `<span class="w-2 h-2 bg-emerald-400 rounded-full mr-1"></span> Present`
      : status === 'missed'
        ? `<span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span> Missed`
        : `<span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span> Scheduled`;

    return `
      <div class="queue-item flex items-center justify-between py-3" data-appointment-id="${appt.id}" data-status="${status}" tabindex="0">
        <div class="flex items-center space-x-3">
          <input type="checkbox" class="queue-checkbox" data-id="${appt.id}">
          <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold" style="background:linear-gradient(135deg,#60a5fa 0%,#6366f1 100%)">${initials}</div>
          <div>
            <div class="font-medium">${escapeHtml(fname)} ${escapeHtml(lname)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(phone || '—')} • ${escapeHtml(address || '—')}</div>
          </div>
        </div>

        <div class="text-right space-y-2">
          <div class="text-sm text-gray-500">${escapeHtml(time)}</div>
          <div><span class="inline-flex status-badge items-center px-2 py-0.5 rounded-full text-xs font-medium ${status}">${badgeHtml}</span></div>

          <div class="mt-2 flex items-center justify-end space-x-2">
            <button class="mark-present px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-sm" data-appt-id="${appt.id}">Mark Present</button>
            <button class="mark-absent px-3 py-1.5 bg-red-500 text-white rounded-lg text-sm" data-appt-id="${appt.id}">Mark Absent</button>
            <a href="/patients/${pid}" class="px-3 py-1.5 border rounded-lg text-sm">View</a>
          </div>
        </div>
      </div>
    `;
  }

  function renderQueueFromState() {
    const list = Array.isArray(state.appointments) ? state.appointments.slice(0, VISIBLE_LIMIT) : [];
    if (!queueList) return;
    if (!list || list.length === 0) {
      queueList.innerHTML = `<div class="p-4 text-sm text-gray-500">No appointments found</div>`;
      return;
    }
    queueList.innerHTML = list.map(makeQueueItem).join('');
  }

  // initial render
  renderQueueFromState();
  updateMiniChart(state.present, state.total);

  /* ---------- selection & bulk actions ---------- */
  const selectAll = $('#selectAll');
  const bulkActions = $('#bulkActions');
  const selectionCountEl = $('#selectionCount');

  function setBulkVisible(visible) {
    if (!bulkActions) return;
    bulkActions.style.opacity = visible ? '1' : '0';
    bulkActions.setAttribute('aria-hidden', visible ? 'false' : 'true');
  }

  function updateSelectionCount() {
    const checked = $$('.queue-checkbox', queueList).filter(c => c.checked).length;
    if (selectionCountEl) selectionCountEl.textContent = `${checked} selected`;
    setBulkVisible(checked > 0);
  }

  if (queueList) {
    queueList.addEventListener('change', e => {
      if (e.target && e.target.classList.contains('queue-checkbox')) updateSelectionCount();
    });
  }
  if (selectAll) {
    selectAll.addEventListener('change', (e) => {
      const checked = e.target.checked;
      $$('.queue-checkbox', queueList).forEach(cb => cb.checked = checked);
      updateSelectionCount();
    });
  }

  /* ---------- button loading helpers ---------- */
  function setButtonsLoading(ids = [], isLoading = true) {
    ids.forEach(id => {
      const dom = document.querySelector(`[data-appointment-id="${id}"]`);
      if (!dom) return;
      dom.querySelectorAll('button').forEach(btn => {
        if (isLoading) {
          if (!btn.dataset._orig) btn.dataset._orig = btn.innerHTML;
          btn.disabled = true;
          btn.classList.add('opacity-70','cursor-not-allowed');
          btn.innerHTML = `<span class="inline-block animate-spin w-4 h-4 border-b-2 rounded-full mr-2"></span> ${btn.innerText.trim()}`;
        } else {
          btn.disabled = false;
          btn.classList.remove('opacity-70','cursor-not-allowed');
          if (btn.dataset._orig) { btn.innerHTML = btn.dataset._orig; delete btn.dataset._orig; }
        }
      });
    });
  }

  /* ---------- handleMark: persistent updates, remove present card, cap list (updated to use loading helper) ---------- */
  async function handleMark(ids, endpoint, successMessage) {
    if (!Array.isArray(ids) || ids.length === 0) return showToast('No appointments selected', 'error');

    // Capture previous states from DOM (fall back to state.appointments)
    const prevStates = ids.map(id => {
      const domItem = queueList.querySelector(`[data-appointment-id="${id}"]`);
      if (domItem) return domItem.dataset.status || 'scheduled';
      const ap = (state.appointments || []).find(a => Number(a.id) === Number(id));
      return ap ? (ap.status || 'scheduled') : 'scheduled';
    });

    // set loading state on buttons for affected items
    setButtonsLoading(ids, true);

    try {
      const payload = { appointment_ids: ids };
      const res = await ajaxPost(endpoint, payload);

      if (!res.ok) {
        showToast(res.body?.message || 'Action failed', 'error');
        return;
      }

      // PERMANENTLY update in-memory state and DOM
      ids.forEach((id, idx) => {
        const prev = prevStates[idx] || 'scheduled';
        const idxInState = (state.appointments || []).findIndex(a => Number(a.id) === Number(id));
        if (endpoint.includes('mark-present')) {
          if (idxInState !== -1) state.appointments.splice(idxInState, 1);
        } else {
          if (idxInState !== -1) {
            state.appointments[idxInState].status = 'missed';
          }
        }
      });

      // Recompute counters
      const totalEl = $('#totalAppointments'), presentEl = $('#patientsPresent'), missedEl = $('#missedAppointments');
      let totalVal = state._meta && state._meta.total !== undefined ? Number(state._meta.total) : (getIntFromEl(totalEl) || 0);
      if (res.body && typeof res.body.total === 'number') totalVal = res.body.total;

      let presentVal = getIntFromEl(presentEl);
      let missedVal = getIntFromEl(missedEl);

      ids.forEach((id, idx) => {
        const prev = prevStates[idx] || 'scheduled';
        if (endpoint.includes('mark-present')) {
          if (prev !== 'present') presentVal = presentVal + 1;
          if (prev === 'missed' || prev === 'absent') missedVal = Math.max(0, missedVal - 1);
        } else {
          if (prev === 'present') { presentVal = Math.max(0, presentVal - 1); missedVal = missedVal + 1; }
          else if (prev !== 'missed' && prev !== 'absent') missedVal = missedVal + 1;
        }
      });

      state.present = presentVal;
      state.notArrived = missedVal;
      if (!state._meta) state._meta = {};
      state._meta.total = totalVal;

      if (presentEl) presentEl.textContent = String(presentVal);
      if (missedEl) missedEl.textContent = String(missedVal);
      if (totalEl) totalEl.textContent = String(totalVal);
      updateMiniChart(presentVal, totalVal);

      // Re-render visible queue (top VISIBLE_LIMIT items)
      renderQueueFromState();

      // Reset selection UI
      $$('.queue-checkbox', queueList).forEach(cb => cb.checked = false);
      if (selectAll) selectAll.checked = false;
      updateSelectionCount();

      showToast(successMessage, 'success');

      // If backend returned authoritative counts, prefer them
      if (res.body && typeof res.body.present === 'number') {
        state.present = res.body.present;
        state.notArrived = res.body.notArrived ?? state.notArrived;
        state._meta.total = res.body.total ?? state._meta.total;
        if (presentEl) presentEl.textContent = String(state.present);
        if (missedEl) missedEl.textContent = String(state.notArrived);
        if (totalEl) totalEl.textContent = String(state._meta.total);
        updateMiniChart(Number(state.present), Number(state._meta.total));
      }

    } catch (err) {
      console.error(err);
      showToast('Network error', 'error');
    } finally {
      // ensure loading state removed
      setButtonsLoading(ids, false);
    }
  }

  // delegated click handlers for single action
  document.addEventListener('click', (e) => {
    const presentBtn = e.target.closest && e.target.closest('.mark-present');
    if (presentBtn) {
      const id = presentBtn.dataset.apptId;
      if (id) handleMark([parseInt(id,10)], '/daily-queue/mark-present', 'Marked present');
    }
    const absentBtn = e.target.closest && e.target.closest('.mark-absent');
    if (absentBtn) {
      const id = absentBtn.dataset.apptId;
      if (id) handleMark([parseInt(id,10)], '/daily-queue/mark-absent', 'Marked absent');
    }
  });

  // bulk handlers
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

  /* ---------- Dynamic Date Filter (unchanged but robust) ---------- */
  const dateFilter = $('#dateFilter');
  const statusFilter = $('#statusFilter');
  const dashboardUrl = '{{ route("dashboard") }}';

  async function loadDataForDate(newDate, status = '') {
    showToast('Loading data...', 'info');

    try {
      const statsUrl = `/dashboard/stats?date=${newDate}${status ? '&status=' + status : ''}`;
      const statsRes = await ajaxGet(statsUrl);
      if (statsRes.ok && statsRes.body) {
        state.total = statsRes.body.total || 0;
        state.present = statsRes.body.present || 0;
        state.notArrived = statsRes.body.notArrived || 0;
        state._meta = { ...state._meta, total: state.total };

        setIntToEl($('#totalAppointments'), state.total);
        setIntToEl($('#patientsPresent'), state.present);
        setIntToEl($('#missedAppointments'), state.notArrived);
        updateMiniChart(state.present, state.total);
      }

      const apptsUrl = `/dashboard/appointments?date=${newDate}${status ? '&status=' + status : ''}`;
      const apptsRes = await ajaxGet(apptsUrl);
      if (apptsRes.ok && Array.isArray(apptsRes.body)) {
        state.appointments = apptsRes.body;
        renderQueueFromState();
      } else {
        window.location.href = `${dashboardUrl}?date=${newDate}${status ? '&status=' + status : ''}`;
        return;
      }

      if (currentDateEl) {
        const dateObj = new Date(newDate + 'T00:00:00');
        currentDateEl.textContent = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' }) + ' Schedule';
      }

      showToast('Data updated for selected date', 'success');
    } catch (err) {
      console.error(err);
      showToast('Failed to load data. Reloading page...', 'error');
      window.location.href = `${dashboardUrl}?date=${newDate}${status ? '&status=' + status : ''}`;
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
      const status = e.target.value;
      loadDataForDate(dateFilter.value, status);
    });
  }

  /* ---------- Theme Toggle (unchanged) ---------- */
  const themeToggle = $('#themeToggle');
  const html = document.documentElement;
  const moonIcon = $('.moon-icon');
  const sunIcon = $('.sun-icon');

  const savedTheme = localStorage.getItem('theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

  if (isDark) { html.classList.add('dark'); } else { html.classList.remove('dark'); }
  if (moonIcon) moonIcon.classList.toggle('hidden', !isDark);
  if (moonIcon) moonIcon.classList.toggle('block', isDark);
  if (sunIcon) sunIcon.classList.toggle('hidden', isDark);
  if (sunIcon) sunIcon.classList.toggle('block', !isDark);

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const currentIsDark = html.classList.contains('dark');
      const newIsDark = !currentIsDark;
      html.classList.toggle('dark', newIsDark);
      localStorage.setItem('theme', newIsDark ? 'dark' : 'light');
      if (moonIcon) { moonIcon.classList.toggle('hidden', !newIsDark); moonIcon.classList.toggle('block', newIsDark); }
      if (sunIcon) { sunIcon.classList.toggle('hidden', newIsDark); sunIcon.classList.toggle('block', !newIsDark); }
      showToast(newIsDark ? 'Switched to dark mode' : 'Switched to light mode', 'success', 2000);
    });
  }

  /* ---------- Search (debounced + keyboard nav & roles) ---------- */
  (function attachSearch() {
    const searchInput = document.getElementById('globalSearch');
    const resultsBox = document.getElementById('searchResults');
    const dateFilterEl = document.getElementById('dateFilter');
    if (!searchInput || !resultsBox) return;

    const searchUrl = '/dashboard/search';
    let timer = null;
    let activeIndex = -1;
    let currentItems = [];

    function escapeHtmlLocal(s) {
      if (!s && s !== 0) return '';
      return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,"&#039;");
    }

    function renderResults(items) {
      currentItems = items || [];
      activeIndex = -1;
      resultsBox.setAttribute('role', 'listbox');
      if (!items || !items.length) {
        resultsBox.innerHTML = `<div class="p-4 text-sm text-gray-600 dark:text-gray-300">No results found</div>`;
        return;
      }

      resultsBox.innerHTML = `<div class="divide-y dark:divide-gray-700 text-md">` + items.map((item, idx) => {
        const name = (item.first_name || item.last_name) ? `${item.first_name || ''} ${item.last_name || ''}`.trim() : (item.label || 'Unknown Patient');
        const href = item.id ? `/patients/${item.id}` : '#';
        const phoneInfo = item.phone ? `<span class="flex items-center text-xs">${escapeHtmlLocal(item.phone)}</span>` : '';
        return `
          <a href="${href}" role="option" id="search-option-${idx}" data-index="${idx}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" tabindex="-1">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center space-x-2">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtmlLocal(name)}</div>
                </div>
                <div class="mt-1 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                  ${phoneInfo}
                </div>
              </div>
            </div>
          </a>
        `;
      }).join('') + `</div>`;
    }

    function focusResult(idx) {
      const option = resultsBox.querySelector(`#search-option-${idx}`);
      if (!option) return;
      $$('.active-result', resultsBox).forEach(el => el.classList.remove('active-result'));
      option.classList.add('active-result');
      option.focus();
      resultsBox.setAttribute('aria-activedescendant', option.id);
      activeIndex = idx;
    }

    searchInput.addEventListener('input', (ev) => {
      const q = String(ev.target.value || '').trim();
      clearTimeout(timer);
      if (!q || q.length < 2) { resultsBox.classList.add('hidden'); return; }
      resultsBox.classList.remove('hidden');
      resultsBox.innerHTML = `<div class="p-4 flex items-center justify-center"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div><span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Searching...</span></div>`;

      timer = setTimeout(async () => {
        try {
          const dateParam = dateFilterEl && dateFilterEl.value ? `&date=${encodeURIComponent(dateFilterEl.value)}` : '';
          const url = `${searchUrl}?term=${encodeURIComponent(q)}${dateParam}`;
          const res = await fetch(url, { headers: { 'Accept': 'application/json','X-Requested-With': 'XMLHttpRequest' }});
          if (!res.ok) { resultsBox.innerHTML = `<div class="p-4 text-sm text-red-600">Search failed. Please try again.</div>`; return; }
          const json = await res.json();
          renderResults(Array.isArray(json) ? json : []);
        } catch (err) {
          console.error('Search error:', err);
          resultsBox.innerHTML = `<div class="p-4 text-sm text-red-600">Network error. Please check your connection.</div>`;
        }
      }, 300);
    });

    // keyboard navigation
    searchInput.addEventListener('keydown', (e) => {
      if (resultsBox.classList.contains('hidden')) return;
      const len = currentItems.length;
      if (e.key === 'ArrowDown') { e.preventDefault(); focusResult(Math.min(len-1, (activeIndex === -1 ? 0 : activeIndex + 1))); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); focusResult(Math.max(0, (activeIndex === -1 ? 0 : activeIndex - 1))); }
      else if (e.key === 'Enter') {
        if (activeIndex >= 0) {
          const link = resultsBox.querySelector(`#search-option-${activeIndex}`);
          if (link) { window.location.href = link.getAttribute('href'); }
        }
      } else if (e.key === 'Escape') {
        resultsBox.classList.add('hidden'); searchInput.blur();
      }
    });

    // click result -> close box
    resultsBox.addEventListener('click', (ev) => {
      const anchor = ev.target.closest('a[role="option"]');
      if (anchor) resultsBox.classList.add('hidden');
    });

    // click outside to close
    document.addEventListener('click', (ev) => {
      if (!ev.target.closest('#searchResults') && !ev.target.closest('#globalSearch')) {
        resultsBox.classList.add('hidden');
      }
    });
  })();

  /* ---------- FAB behavior improvements ---------- */
  const fabButton = document.getElementById('fabButton');
  const fabMenu = document.getElementById('fabMenu');
  if (fabButton && fabMenu) {
    // ensure initial a11y attributes
    fabButton.setAttribute('aria-expanded', 'false');
    fabMenu.classList.add('pointer-events-none','opacity-0','scale-95');

    fabButton.addEventListener('click', () => {
      const isOpen = fabMenu.classList.contains('pointer-events-auto');
      fabMenu.classList.toggle('pointer-events-none', isOpen);
      fabMenu.classList.toggle('pointer-events-auto', !isOpen);
      fabMenu.classList.toggle('opacity-100', !isOpen);
      fabMenu.classList.toggle('opacity-0', isOpen);
      fabMenu.classList.toggle('scale-100', !isOpen);
      fabMenu.classList.toggle('scale-95', isOpen);
      fabButton.setAttribute('aria-expanded', String(!isOpen));
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        fabMenu.classList.add('opacity-0','pointer-events-none','scale-95');
        fabButton.setAttribute('aria-expanded','false');
      }
    });
    document.addEventListener('click', (e) => {
      if (!e.target.closest('#fabButton') && !e.target.closest('#fabMenu')) {
        fabMenu.classList.add('opacity-0','pointer-events-none','scale-95');
        fabButton.setAttribute('aria-expanded','false');
      }
    });
  }

  /* ---------- Search keyboard shortcut (/) and ESC behavior already in attachSearch; keep global shortcuts ---------- */
  document.addEventListener('keydown', (e) => {
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
      e.preventDefault();
      const s = document.getElementById('globalSearch');
      if (s) { s.focus(); s.select(); }
    }
  });

  // Click outside to close search results already handled inside attachSearch
})();
</script>
@endpush

@endsection



















