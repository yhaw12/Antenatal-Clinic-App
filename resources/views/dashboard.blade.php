@extends('layouts.app')

@section('title', 'ANC Dashboard')

@section('content')
    
<div class="min-h-screen" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg) 0%, transparent), color-mix(in srgb, var(--brand) 4%, transparent));">

    {{-- HEADER --}}
    <header class="sticky top-0 z-40" style="backdrop-filter: blur(12px); background: color-mix(in srgb, var(--bg) 95%, transparent); border-bottom: 1px solid var(--border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                {{-- 1. LEFT: Logo --}}
                <div class="flex items-center flex-shrink-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(90deg, var(--brand), var(--accent));">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                
                {{-- 2. MIDDLE: Controls (Date, Status, New Button) --}}
                {{-- 2. MIDDLE: Controls (Date, Status, New Button) --}}
                <div class="flex-1 flex items-center justify-end sm:justify-center gap-2">
                    
                    {{-- Mobile Date Picker --}}
                    <div class="relative sm:hidden">
                        <div class="px-3 py-2 rounded-xl text-sm font-bold flex items-center gap-2 bg-transparent" style="color:var(--text)">
                            <span id="dateDisplayMobile">{{ \Carbon\Carbon::parse($date ?? \Carbon\Carbon::today())->format('d/m/Y') }}</span>
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <input type="date" id="dateFilterMobile" 
                               class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                               style="-webkit-appearance: none;"
                               value="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}" 
                               onchange="updateDateFromMobile(this.value)">
                    </div>

                    {{-- Desktop Date Input (Visible only on SM+) --}}
                    <input 
                        type="date" 
                        id="dateFilter" 
                        class="hidden sm:block px-3 py-2 rounded-xl text-sm transition-all focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer"
                        style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--text); border:1px solid var(--border);"
                        value="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                        onchange="updateDateFromDesktop(this.value)"
                    > 

                    {{-- Tomorrow Button (Visible on ALL screens) --}}
                    {{-- On mobile: Icon only. On Desktop: Icon + Text --}}
                    <button 
                        id="tomorrowBtn"
                        class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-sm transition-all hover:opacity-80"
                        title="Tomorrow's Schedule"
                        style="background: color-mix(in srgb, var(--brand) 10%, transparent); color:var(--brand); border:1px solid color-mix(in srgb, var(--brand) 20%, transparent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="ml-2 hidden lg:inline">Tomorrow</span>
                    </button>
                    
                    {{-- Other Desktop Controls (Status, New Button) --}}
                    <div class="hidden sm:flex items-center space-x-2">
                        <select id="statusFilter" class="px-3 py-2 rounded-xl text-sm transition-all outline-none focus:ring-2 focus:ring-brand/20" style="background: color-mix(in srgb, var(--surface) 90%, transparent); color:var(--text); border:1px solid var(--border);">
                            <option value="" style="color:var(--text)">All Status</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="present">Present</option>
                            <option value="missed">Missed</option>
                        </select>

                        <a href="{{ route('appointments.create') }}" 
                            class="text-sm px-4 py-2 rounded-xl shadow-sm transition-all whitespace-nowrap hover:shadow-md"
                            style="background: var(--brand); color: #fff;"
                            aria-label="Create appointment">
                            + New
                        </a>
                    </div>
                </div>

                {{-- 3. RIGHT: Search (Last) --}}
                <div class="flex items-center justify-end flex-shrink-0">
                    
                    {{-- Mobile Search Toggle --}}
                    <button onclick="toggleMobileSearch()" class="sm:hidden p-2 rounded-xl transition-colors hover:bg-black/5 active:scale-95" style="color:var(--muted); border: 1px solid var(--border);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>

                    {{-- Desktop Search Input --}}
                    <div class="hidden sm:block relative w-64 lg:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--muted);" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            id="globalSearch" 
                            class="w-full pl-9 pr-4 py-2 rounded-xl text-sm transition-all focus:ring-2 focus:ring-brand/20 outline-none"
                            style="background: color-mix(in srgb, var(--surface) 95%, transparent); color:var(--text); border:1px solid var(--border);"
                            placeholder="Search..."
                            type="text"
                            autocomplete="off"
                        >
                        <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 rounded-2xl shadow-xl hidden max-h-80 overflow-y-auto z-50" style="background: color-mix(in srgb, var(--surface) 98%, transparent); border:1px solid var(--border);"></div>
                    </div>
                </div>

            </div>

            {{-- Mobile Search Container (Dropdown) --}}
            <div id="mobileSearchContainer" class="hidden absolute left-0 right-0 top-16 p-4 border-b shadow-lg z-50 animate-fade-in" style="background:var(--bg); border-color:var(--border);">
                <div class="relative">
                    <input id="globalSearchMobile" class="w-full pl-9 pr-4 py-3 rounded-xl text-sm outline-none focus:ring-2 focus:ring-brand/20" style="background: var(--surface); color:var(--text); border:1px solid var(--border);" placeholder="Search patients..." type="text" autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
                    <div id="searchResultsMobile" class="absolute top-full left-0 right-0 mt-2 rounded-xl shadow-xl hidden max-h-60 overflow-y-auto z-50" style="background: var(--surface); border:1px solid var(--border);"></div>
                </div>
            </div>

        </div>
    </header>

    {{-- CONTENT BODY --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-8">
        
        {{-- Stats Row --}}
        <div id="statsRow" class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6 mb-2 sm:mb-8 transition-all duration-300">
            
            {{-- Card 1: Total --}}
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

            {{-- Card 2: Present (with Progress) --}}
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

            {{-- Card 3: Missed --}}
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

            {{-- Card 4: New vs Review --}}
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

            {{-- Left Column: Queue --}}
            <div class="lg:col-span-2" id="queueSection">
                <div class="glass-card rounded-2xl p-4 sm:p-6 mb-4" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                    
                    {{-- Future Mode Banner --}}
                    <div id="futureModeBanner" class="hidden mb-4 p-3 rounded-xl flex items-start space-x-3" style="background: color-mix(in srgb, var(--accent) 15%, transparent); border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent); color: var(--accent);">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <h4 class="font-bold text-sm">Future Schedule Mode</h4>
                            <p class="text-xs opacity-90">You are viewing appointments for a future date. Use this list for calling patients only. Do not mark attendance yet.</p>
                        </div>
                    </div>

                    {{-- Queue Header --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold" id="queueTitle" style="color:var(--text)">Today's Queue</h3>
                            <p class="text-sm" id="queueSubtitle" style="color:var(--muted)">Quick controls for marking attendance</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('daily-queue') . '?date=' . ($date ?? \Carbon\Carbon::today()->format('Y-m-d')) }}" class="text-sm px-3 py-2 rounded-xl transition-all" style="background: color-mix(in srgb, var(--surface) 95%, transparent); border:1px solid var(--border); color:var(--text)">Open full queue</a>
                        </div>
                    </div>

                    {{-- Bulk Actions --}}
                    <div id="bulkActions" class="flex items-center justify-between mb-4 mt-2 opacity-0 transition-opacity duration-200" aria-hidden="true">
                        <div class="flex items-center space-x-2">
                            <label class="inline-flex items-center text-sm" style="color:var(--muted);">
                                <input id="selectAll" type="checkbox" class="form-checkbox h-4 w-4" style="accent-color:var(--brand);">
                                <span class="ml-2" id="selectionCount">0 selected</span>
                            </label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button id="bulkMarkPresent" class="px-3 py-1.5 rounded-lg text-sm" style="background:var(--success); color:white;">Present</button>
                            <button id="bulkMarkAbsent" class="px-3 py-1.5 rounded-lg text-sm" style="background:var(--danger); color:white;">Absent</button>
                        </div>
                    </div>

                    {{-- The List --}}
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

                            <div class="queue-item flex flex-wrap sm:flex-nowrap items-center justify-between py-3" data-id="{{ $appt->id }}" data-status="{{ $status }}">
                                <div class="flex items-center space-x-3 w-full sm:w-auto min-w-0 flex-1 pr-4">
                                    <input type="checkbox" class="queue-checkbox" data-id="{{ $appt->id }}">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
                                    style="background:linear-gradient(135deg,var(--brand),color-mix(in srgb,var(--brand) 60%,var(--accent)));">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium truncate" style="color:var(--text)">{{ $patient->first_name ?? 'Unknown' }} {{ $patient->last_name ?? '' }}</div>
                                        <div class="text-xs truncate" style="color:var(--muted)">{{ $patient->phone ?? '—' }} • {{ $patient->address ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="w-full sm:w-auto mt-2 sm:mt-0 flex items-center justify-between sm:block sm:text-right space-y-0 sm:space-y-2">
                                    <div class="text-sm sm:mb-0" style="color:var(--muted)">{{ $time }}</div>
                                    
                                    <div class="flex items-center space-x-2 sm:justify-end">
                                        @if(!$isFuture)
                                            <button class="mark-present mark-btn text-xs px-2 py-1.5 rounded-lg text-white" style="background:var(--success)" data-appt-id="{{ $appt->id }}">Present</button>
                                            <button class="mark-absent mark-btn text-xs px-2 py-1.5 rounded-lg text-white" style="background:var(--danger)" data-appt-id="{{ $appt->id }}">Absent</button>
                                        @endif
                                        <a href="{{ route('patients.show', $patient->id) }}" class="text-xs px-2 py-1.5 rounded-lg transition-colors" style="background: color-mix(in srgb, var(--surface) 95%, transparent); border:1px solid var(--border); color:var(--text)">View</a>
                                    </div>

                                    <div class="hidden sm:block">
                                        <span class="status-badge status-{{ $status }} text-xs" style="background: color-mix(in srgb, {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }} 10%, transparent); color: {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }}; border:1px solid color-mix(in srgb, {{ $status === 'present' ? 'var(--success)' : ($status === 'missed' ? 'var(--danger)' : 'var(--brand)') }} 20%, transparent);">
                                            @if($status === 'present')
                                                <span class="w-2 h-2 rounded-full mr-1 inline-block" style="background:var(--success)"></span> Present
                                            @elseif($status === 'missed')
                                                <span class="w-2 h-2 rounded-full mr-1 inline-block" style="background:var(--danger)"></span> Missed
                                            @else
                                                <span class="w-2 h-2 rounded-full mr-1 inline-block" style="background:var(--brand)"></span> Scheduled
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 flex items-center justify-between pt-4 transition-colors" style="border-top: 1px solid var(--border);">
                      <div class="text-xs font-medium" style="color: var(--muted);">
                          Showing 
                          <span class="font-bold" style="color: var(--text);">{{ $appointments->firstItem() ?? 0 }}</span> 
                          to 
                          <span class="font-bold" style="color: var(--text);">{{ $appointments->lastItem() ?? 0 }}</span> 
                          of 
                          <span class="font-bold" style="color: var(--text);">{{ $appointments->total() }}</span> 
                          results
                      </div>
                      <div class="flex items-center gap-2">
                          @if ($appointments->onFirstPage())
                              <span class="px-3 py-1.5 text-xs font-medium rounded-lg border cursor-not-allowed opacity-50" style="background: transparent; border-color: transparent; color: var(--muted);">Previous</span>
                          @else
                              <a href="{{ $appointments->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-all shadow-sm hover:opacity-80" style="background: var(--surface); border-color: var(--border); color: var(--text);">Previous</a>
                          @endif

                          @if ($appointments->hasMorePages())
                              <a href="{{ $appointments->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-all shadow-sm hover:opacity-80" style="background: var(--surface); border-color: var(--border); color: var(--text);">Next</a>
                          @else
                              <span class="px-3 py-1.5 text-xs font-medium rounded-lg border cursor-not-allowed opacity-50" style="background: transparent; border-color: transparent; color: var(--muted);">Next</span>
                          @endif
                      </div>
                  </div>
                </div>
            </div>

            {{-- Right Column: Sidebar --}}
            <aside id="sidebar" class="space-y-6">
                <div class="glass-card rounded-2xl p-4 sm:p-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="font-bold" style="color:var(--text)">Call Queue</div>
                        <div class="text-xs" style="color:var(--muted)">Priority follow-ups</div>
                    </div>

                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($callList as $c)
                            <div class="p-3 rounded-xl border" style="background: color-mix(in srgb, var(--bg) 60%, transparent); border:1px solid var(--border);">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 mr-2">
                                        <div class="font-medium truncate" style="color:var(--text)">{{ optional($c->patient)->first_name ?? 'Unknown' }} {{ optional($c->patient)->last_name ?? '' }}</div>
                                        <div class="text-xs truncate" style="color:var(--muted)">{{ optional($c->patient)->phone ?? '—' }} • {{ $c->call_time ? \Carbon\Carbon::parse($c->call_time)->format('h:i A') : '' }}</div>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        <button class="p-2 rounded-lg text-sm" data-action="call-now" data-name="{{ optional($c->patient)->first_name ?? 'Patient' }}" style="background: linear-gradient(90deg, var(--danger), color-mix(in srgb, var(--danger) 60%, var(--brand))); color:#fff;">Call</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm" style="color:var(--muted)">No pending calls</div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('call_logs') }}" class="w-full inline-block text-center px-4 py-2 rounded-xl text-sm" style="background:var(--brand); color:#fff;">View all calls</a>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-4 sm:p-6" style="background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="font-bold" style="color:var(--text)">Recent Activity</div>
                        <div class="text-xs" style="color:var(--muted)">Last actions</div>
                    </div>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        @foreach($recentActivities as $act)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: color-mix(in srgb, var(--surface) 90%, transparent);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="color:var(--muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm truncate" style="color:var(--text)">{{ optional($act->user)->name ?? 'System' }} <span class="text-xs" style="color:var(--muted)">{{ $act->action ?? '' }}</span></div>
                                    <div class="text-xs" style="color:var(--muted)">{{ $act->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

        </div>
    </div>

    {{-- Floating Action Button --}}
    <div class="fixed bottom-16 right-6 z-50">
    <a href="{{ route('appointments.create') }}" 
       class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-transform transform hover:scale-105 active:scale-95" 
       aria-label="Create New Appointment"
       style="background: linear-gradient(90deg, var(--brand), var(--accent)); color:#fff; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);">
       <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
       </svg>
    </a>
     </div>

    <div id="toastContainer" class="fixed top-20 right-6 z-50 space-y-3"></div>

    {{-- Call Modal --}}
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
/* Mobile optimization for Status Badge: Hide on small screens, prioritize action buttons */
.queue-item .status-badge {
    display: none;
}
@media (min-width: 640px) {
    .queue-item .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 9999px;
    }
}
/* FIXED: Pagination colors for Light Mode */
.custom-pagination nav[role="navigation"] div:first-child span,
.custom-pagination nav[role="navigation"] div:last-child span,
.custom-pagination nav[role="navigation"] div:first-child a,
.custom-pagination nav[role="navigation"] div:last-child a {
    background-color: transparent !important;
    border-color: var(--border) !important;
    color: var(--text) !important;
}
/* Override SVG arrow color in pagination */
.custom-pagination svg {
    color: var(--text) !important;
}

/* Mobile Optimization for List Length: Hide items 11-15 on screens smaller than 640px */
@media (max-width: 640px) {
    #queueList .queue-item:nth-child(n+11) {
        display: none;
    }
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
<style>
    /* --- CSS Animations --- */
    .progress-bar-animate {
        transition: width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1); /* Bouncy ease-out effect */
    }
    .animate-count {
        font-variant-numeric: tabular-nums;
    }
    /* Fade in for list items */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>

<script>
(() => {
    /* ==========================================================================
       SECTION 1: UTILITIES & HELPERS
       ========================================================================== */
    const $ = (sel, root = document) => (root || document).querySelector(sel);
    const $$ = (sel, root = document) => Array.from((root || document).querySelectorAll(sel));
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
    const CSRF = metaCsrf ? metaCsrf.getAttribute('content') : null;

    /* --- GLOBAL ACTIONS FOR HTML HANDLERS --- */
    // Define these on window so onClick/onChange attributes in HTML can find them
    window.toggleMobileSearch = () => {
        const c = document.getElementById('mobileSearchContainer');
        if(c) {
            c.classList.toggle('hidden');
            if(!c.classList.contains('hidden')) {
                 setTimeout(() => {
                    const inp = document.getElementById('globalSearchMobile');
                    if(inp) inp.focus();
                 }, 50);
            }
        }
    };

    window.updateDateFromMobile = (val) => loadDataForDate(val, $('#statusFilter').value);
    window.updateDateFromDesktop = (val) => loadDataForDate(val, $('#statusFilter').value);

    /* --- Sophisticated Counter Animation --- */
    function animateValue(obj, start, end, duration) {
      if (!obj || start === end) return;
      let startTimestamp = null;
      const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3); // Cubic Ease Out
        
        obj.innerHTML = Math.floor(ease * (end - start) + start);
        if (progress < 1) {
          window.requestAnimationFrame(step);
        } else {
          obj.innerHTML = end;
        }
      };
      window.requestAnimationFrame(step);
    }

    /* --- Visual Card Flash Effect --- */
    function flashCard(elementId, colorClass) {
      const el = document.getElementById(elementId);
      if(!el) return;
      const card = el.closest('.glass-card');
      if(!card) return;
      
      card.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease';
      const originalShadow = card.style.boxShadow;
      const originalBorder = card.style.borderColor;
      
      const color = colorClass.includes('green') ? '16, 185, 129' : '239, 68, 68';
      card.style.boxShadow = `0 0 15px rgba(${color}, 0.4)`;
      card.style.borderColor = `rgba(${color}, 0.6)`;

      setTimeout(() => {
          card.style.boxShadow = originalShadow;
          card.style.borderColor = originalBorder;
      }, 400);
    }

    /* --- AJAX Helpers --- */
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

    /* --- Toast Notifications --- */
    function showToast(message, type = 'info', timeout = 3500) {
      const container = document.getElementById('toastContainer');
      if (!container) return;
      const el = document.createElement('div');
      el.className = 'px-4 py-2 rounded-xl shadow-md max-w-sm text-sm flex items-center justify-between space-x-3 transition-all duration-300 transform translate-y-2 opacity-0';
      el.setAttribute('role', 'status');
      
      let bg = 'bg-white text-gray-800';
      if (type === 'error') bg = 'bg-red-500 text-white';
      if (type === 'success') bg = 'bg-emerald-500 text-white';
      
      el.className += ` ${bg}`;
      el.innerHTML = `<div class="flex-1 font-medium">${message}</div>`;

      container.appendChild(el);
      requestAnimationFrame(() => { el.classList.remove('translate-y-2', 'opacity-0'); });
      
      setTimeout(() => {
          el.classList.add('opacity-0', 'translate-y-2');
          setTimeout(() => el.remove(), 300);
      }, timeout);
    }

    /* ==========================================================================
       SECTION 2: INITIALIZATION & STATE
       ========================================================================== */
    window.__DASHBOARD = window.__DASHBOARD || {};
    const state = window.__DASHBOARD;
    if (!Array.isArray(state.appointments)) state.appointments = [];

    /* --- Initialize Animations (Progress Bars & Counters) --- */
    function initAnimations() {
        // 1. Animate Number Counters
        const elements = [
            { id: 'totalAppointments', val: state.total },
            { id: 'patientsPresent', val: state.present },
            { id: 'missedAppointments', val: state.notArrived },
            { id: 'statNewVisits', val: {{ $newVisits ?? 0 }} },
            { id: 'statReviews', val: {{ $reviews ?? 0 }} }
        ];

        elements.forEach(item => {
            const el = document.getElementById(item.id);
            if(el) animateValue(el, 0, item.val, 1500);
        });

        // 2. Animate Progress Bar
        const presentCard = document.getElementById('patientsPresent');
        if(presentCard) {
            // Find the inner div of the progress bar
            const progressBar = presentCard.closest('.glass-card').querySelector('.h-2.rounded-full > div');
            if(progressBar) {
                // Add the animation class
                progressBar.classList.add('progress-bar-animate');
                // Set initial state to 0
                progressBar.style.width = '0%';
                
                // Calculate target percentage
                const pct = state.total ? Math.round((state.present / state.total) * 100) : 0;
                
                // Trigger animation with a slight delay
                setTimeout(() => {
                    progressBar.style.width = `${pct}%`;
                }, 100);
            }
        }
    }

    // Run animations on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAnimations);
    } else {
        initAnimations();
    }

    // Initialize Dates Header
    const currentDateEl = $('#currentDate');
    if (currentDateEl && state.currentDate) updateDateHeader(state.currentDate);

    function updateDateHeader(dateStr) {
        const dateObj = new Date(dateStr + 'T00:00:00');
        if(currentDateEl) currentDateEl.textContent = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' }) + ' Schedule';
    }

    // Initialize Percentage Change Display
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
    
    function updateMiniChart(present, total) {
      const progressBar = document.querySelector('#patientsPresent').closest('.glass-card').querySelector('.h-2.rounded-full > div');
      const pct = total ? Math.round((present / total) * 100) : 0;
      if (progressBar) progressBar.style.width = pct + '%';
    }

    /* ==========================================================================
       SECTION 3: QUEUE RENDERING
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
      const address = appt.patient?.address ?? '';
      const initials = ((fname[0]||'') + (lname[0]||'')).toUpperCase() || 'P';
      const status = (appt.status || 'scheduled').toLowerCase();
      
      let time = '-';
      if (appt.time) {
        try {
          const t = appt.time.length <= 8 ? `1970-01-01T${appt.time}` : appt.time;
          time = new Date(t).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
        } catch (e) { time = appt.time; }
      }

      let statusBadgeHTML = `
        <span class="status-badge status-${status} text-xs" style="background: color-mix(in srgb, ${status === 'present' ? 'var(--success)' : (status === 'missed' ? 'var(--danger)' : 'var(--brand)')} 10%, transparent); color: ${status === 'present' ? 'var(--success)' : (status === 'missed' ? 'var(--danger)' : 'var(--brand)')}; border:1px solid color-mix(in srgb, ${status === 'present' ? 'var(--success)' : (status === 'missed' ? 'var(--danger)' : 'var(--brand)')} 20%, transparent);">
          ${status === 'present' ? '<span class="w-2 h-2 rounded-full mr-1 inline-block" style="background:var(--success)"></span> Present' : (status === 'missed' ? '<span class="w-2 h-2 rounded-full mr-1 inline-block" style="background:var(--danger)"></span> Missed' : '<span class="w-2 h-2 rounded-full mr-1 inline-block" style="background:var(--brand)"></span> Scheduled')}
        </span>
      `;

      let actionButtons = '';
      if (!state.isFuture) {
          actionButtons = `
              <button class="mark-present mark-btn text-xs px-2 py-1.5 bg-emerald-500 text-white rounded-lg transition-transform active:scale-95 shadow-sm hover:shadow-md" data-appt-id="${appt.id}">Present</button>
              <button class="mark-absent mark-btn text-xs px-2 py-1.5 bg-red-500 text-white rounded-lg transition-transform active:scale-95 shadow-sm hover:shadow-md" data-appt-id="${appt.id}">Absent</button>
          `;
      }

      return `
        <div class="queue-item flex flex-wrap sm:flex-nowrap items-center justify-between py-3 transition-all hover:bg-black/5 rounded-lg px-2 -mx-2 animate-fade-in" 
              data-appointment-id="${appt.id}" 
              data-status="${status}">
          <div class="flex items-center space-x-3 w-full sm:w-auto min-w-0 flex-1 pr-4">
            <input type="checkbox" class="queue-checkbox form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500" data-id="${appt.id}">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0 shadow-md" 
                  style="background:linear-gradient(135deg,#60a5fa 0%,#6366f1 100%)">${initials}</div>
            <div class="min-w-0">
              <div class="font-medium truncate" style="color:var(--text)">${escapeHtml(fname)} ${escapeHtml(lname)}</div>
              <div class="text-xs truncate whitespace-nowrap overflow-hidden text-ellipsis" style="color:var(--muted)">${escapeHtml(phone || '—')} • ${escapeHtml(address || '—')}</div>
            </div>
          </div>
          <div class="w-full sm:w-auto mt-2 sm:mt-0 flex items-center justify-between sm:block sm:text-right space-y-0 sm:space-y-2">
            <div class="text-sm font-mono sm:mb-0" style="color:var(--muted)">${escapeHtml(time)}</div>
            <div class="flex items-center space-x-2 sm:justify-end">
              ${actionButtons}
              <a href="/patients/${pid}" class="text-xs px-2 py-1.5 border rounded-lg transition-colors hover:opacity-80" style="border-color:var(--border); color:var(--text)">View</a>
            </div>
            <div class="hidden sm:block">${statusBadgeHTML}</div>
          </div>
        </div>
      `;
    }

    function renderQueueFromState() {
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
              <h3 class="text-lg font-medium" style="color:var(--text)">All Caught Up!</h3>
              <p class="mt-1" style="color:var(--muted)">No pending appointments for today.</p>
          </div>`;
        return;
      }
      queueList.innerHTML = list.map(makeQueueItem).join('');
    }

    // Initial Render
    renderQueueFromState();

    /* ==========================================================================
       SECTION 4: ACTIONS (Mark Present, Bulk Actions)
       ========================================================================== */
    async function handleMark(ids, endpoint, successMessage) {
      if (!Array.isArray(ids) || ids.length === 0) return;

      // Optimistic UI Update
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
          renderQueueFromState(); // Revert
          showToast('Action failed. Please check connection.', 'error');
          return;
        }

        // Update State
        ids.forEach(id => {
          const idx = (state.appointments || []).findIndex(a => Number(a.id) === Number(id));
          if (idx !== -1) {
              const newStatus = endpoint.includes('mark-present') ? 'present' : 'missed';
              state.appointments[idx].status = newStatus;
          }
        });

        const presentEl = document.getElementById('patientsPresent');
        const missedEl = document.getElementById('missedAppointments');
        const newVisitsEl = document.getElementById('statNewVisits');
        const reviewsEl = document.getElementById('statReviews');
        
        const oldPresent = getIntFromEl(presentEl);
        const oldMissed = getIntFromEl(missedEl);

        let newPresent = oldPresent;
        let newMissed = oldMissed;

        if (res.body && typeof res.body.present === 'number') {
            newPresent = res.body.present;
            newMissed = res.body.notArrived;
            if(res.body.newVisits !== undefined) animateValue(newVisitsEl, getIntFromEl(newVisitsEl), res.body.newVisits, 800);
            if(res.body.reviews !== undefined) animateValue(reviewsEl, getIntFromEl(reviewsEl), res.body.reviews, 800);
        } else {
            const isPresentAction = endpoint.includes('mark-present');
            newPresent = isPresentAction ? oldPresent + ids.length : oldPresent;
            newMissed = Math.max(0, oldMissed - ids.length);
        }

        if (newPresent !== oldPresent) {
            animateValue(presentEl, oldPresent, newPresent, 600);
            flashCard('patientsPresent', 'bg-green-50');
        }
        if (newMissed !== oldMissed) {
            animateValue(missedEl, oldMissed, newMissed, 600);
        }

        updateMiniChart(newPresent, state.total);

        // Refresh List & Cleanup
        setTimeout(() => {
            renderQueueFromState();
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
       SECTION 5: DATE FILTER & NAVIGATION
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

      // Update Global State
      state.currentDate = newDate;

      const diff = getDateDiff(newDate);
      state.isFuture = diff > 0;
      
      if (state.isFuture) {
          if(queueTitle) queueTitle.textContent = "Future Schedule";
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

    /* ==========================================================================
       SECTION 6: SELECTION LOGIC
       ========================================================================== */
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

    /* ==========================================================================
       SECTION 7: MODALS
       ========================================================================== */
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

    /* ==========================================================================
       SECTION 8: DATE-CONFINED SEARCH
       ========================================================================== */
    const searchInput = $('#globalSearch');
    const searchInputMobile = $('#globalSearchMobile');
    const resultsBox = $('#searchResults');
    const resultsBoxMobile = $('#searchResultsMobile');
    let searchTimer = null;

    function handleSearch(e, isMobile) {
        const q = e.target.value.trim();
        const box = isMobile ? resultsBoxMobile : resultsBox;
        
        clearTimeout(searchTimer);
        
        if (q.length < 2) { 
            box.classList.add('hidden'); 
            return; 
        }
        
        box.classList.remove('hidden');
        box.innerHTML = `<div class="p-4 text-center" style="color:var(--muted)">Searching ${state.currentDate}...</div>`;

        searchTimer = setTimeout(async () => {
            try {
                // Pass currently selected date to backend to constrain search
                const dateParam = state.currentDate || '';
                const res = await ajaxGet(`/dashboard/search?term=${encodeURIComponent(q)}&date=${dateParam}`);
                
                if(!res.ok || !res.body.length) {
                    box.innerHTML = `<div class="p-4 text-center" style="color:var(--muted)">No appointments found on this date</div>`;
                    return;
                }
                
                box.innerHTML = `<div class="divide-y divide-gray-100">` + 
                    res.body.map(p => `
                        <a href="/patients/${p.id}" class="block p-3 transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                             <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium" style="color:var(--text)">${escapeHtml(p.label)}</div>
                                    <div class="text-xs" style="color:var(--muted)">${escapeHtml(p.phone || 'No phone')}</div>
                                </div>
                                ${p.appointment_time ? 
                                `<div class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-600 font-bold border border-blue-100">
                                    ${p.appointment_time}
                                 </div>` : ''}
                            </div>
                        </a>
                    `).join('') + `</div>`;
            } catch(e) { 
                box.innerHTML = `<div class="p-4 text-red-500">Error</div>`; 
            }
        }, 300);
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => handleSearch(e, false));
    }

    // Handle Mobile Search if it exists
    if (searchInputMobile) {
        searchInputMobile.addEventListener('input', (e) => handleSearch(e, true));
    }

    // Close search results when clicking outside
    document.addEventListener('click', e => {
        if (!e.target.closest('#globalSearch') && !e.target.closest('#searchResults')) {
            if(resultsBox) resultsBox.classList.add('hidden');
        }
        if (!e.target.closest('#globalSearchMobile') && !e.target.closest('#searchResultsMobile')) {
            if(resultsBoxMobile) resultsBoxMobile.classList.add('hidden');
        }
    });

})();
</script>
@endpush

@endsection