@extends('layouts.app')

@section('title', 'Reports')

@section('page-title', 'Reports')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Generate Appointment Report</h2>
            
            <!-- Report Generation Form -->
            <form action="{{ route('reports.generate') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                    <input type="date" name="from" value="{{ $from ?? now()->format('Y-m-d') }}" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                    <input type="date" name="to" value="{{ $to ?? now()->format('Y-m-d') }}" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Filter (Optional)</label>
                    <select name="status" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ empty($status) ? 'selected' : '' }}>All Statuses</option>
                        <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="missed" {{ $status === 'missed' ? 'selected' : '' }}>Missed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium">
                        Generate Report
                    </button>
                </div>
            </form>

            <!-- Compare Two Months Form -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Compare Trends Between Two Months</h3>
                <form action="{{ route('reports.generate') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month 1 (From)</label>
                        <input type="month" name="month1" value="{{ $comparison['month1'] ?? '' }}" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-3 flex items-end">
                        <p class="text-sm text-gray-500">Select two months to compare appointment trends (e.g., total count, growth/decline).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month 2 (To)</label>
                        <input type="month" name="month2" value="{{ $comparison['month2'] ?? '' }}" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-medium">
                            Compare Months
                        </button>
                    </div>
                </form>
            </div>

            <!-- Report Display Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Results ({{ $from }} to {{ $to }}{{ $status ? ' - ' . ucfirst($status) : '' }})</h3>
                
                <!-- KPIs Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Appointments -->
                    <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-400/20 to-blue-600/20 rounded-bl-2xl"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpis['total'] }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Appointments</p>
                            </div>
                        </div>
                    </div>

                    <!-- Patients Present -->
                    <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-400/20 to-emerald-600/20 rounded-bl-2xl"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpis['present'] }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Patients Present</p>
                            </div>
                        </div>
                    </div>

                    <!-- Missed Appointments -->
                    <div class="card-hover glass-card rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-400/20 to-red-600/20 rounded-bl-2xl"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpis['notArrived'] }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Missed Appointments</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($comparison)
                <!-- Monthly Comparison KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
                    <div class="md:col-span-3">
                        <h4 class="text-md font-semibold mb-2">{{ \Carbon\Carbon::parse($comparison['month1'])->format('F Y') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                                <p class="text-sm text-gray-600">Total</p>
                                <p class="text-2xl font-bold">{{ $comparison['month1_data']['total'] }}</p>
                            </div>
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl">
                                <p class="text-sm text-gray-600">Present</p>
                                <p class="text-2xl font-bold">{{ $comparison['month1_data']['present'] }}</p>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/30 rounded-xl">
                                <p class="text-sm text-gray-600">Missed</p>
                                <p class="text-2xl font-bold">{{ $comparison['month1_data']['notArrived'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <h4 class="text-md font-semibold mb-2">{{ \Carbon\Carbon::parse($comparison['month2'])->format('F Y') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                                <p class="text-sm text-gray-600">Total</p>
                                <p class="text-2xl font-bold">{{ $comparison['month2_data']['total'] }}</p>
                            </div>
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl">
                                <p class="text-sm text-gray-600">Present</p>
                                <p class="text-2xl font-bold">{{ $comparison['month2_data']['present'] }}</p>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/30 rounded-xl">
                                <p class="text-sm text-gray-600">Missed</p>
                                <p class="text-2xl font-bold">{{ $comparison['month2_data']['notArrived'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Summary -->
                <div class="mb-8 p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-xl">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comparison['change_summary'] }}</p>
                </div>
                @endif

                <!-- Chart -->
                <div class="mb-8">
                    <canvas id="appointmentChart" height="200"></canvas>
                </div>

                <!-- Placeholder for detailed data -->
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Detailed appointment data is available via export below for privacy and security reasons.</p>
                </div>
            </div>

            <!-- Export Section (pre-fill with current dates) -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export to Excel for Analysis</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Export appointment data to Excel for detailed analysis. The export will include columns for patient details, appointment date/time, status, notes, and more. You can queue the export and download it from history once ready.
                </p>
                <form action="{{ route('exports.queue') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                        <input type="date" name="from" value="{{ $from ?? now()->format('Y-m-d') }}" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                        <input type="date" name="to" value="{{ $to ?? now()->format('Y-m-d') }}" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Export Format</label>
                        <select name="format" class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-medium">
                            Queue Export
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export History Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Export History</h2>
                <a href="{{ route('exports.history') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View All Exports</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Range</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Format</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">2025-09-01 to 2025-09-30</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Excel</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded-full text-xs">Completed</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Download</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN and Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('appointmentChart');
            if (ctx) {
                @if($comparison)
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($comparison['chart_labels']) !!},
                        datasets: {!! json_encode($comparison['chart_datasets']) !!}
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true } }
                    }
                });
                @else
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chart['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Appointments',
                            data: {!! json_encode($chart['counts'] ?? []) !!},
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                @endif
            }
        });
    </script>
@endsection