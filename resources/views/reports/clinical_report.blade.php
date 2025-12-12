<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    {{-- 
        EXCEL EXPORT TEMPLATE
        Design: Corporate/Clean
        Note: Inline CSS is required for Excel to render styles.
    --}}

    {{-- 1. REPORT HEADER --}}
    <table>
        <tr>
            <td colspan="6" style="font-size: 20px; font-weight: bold; text-align: center; height: 35px; color: #1e40af;">
                CLINIC PERFORMANCE REPORT
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; color: #6b7280; font-size: 11px;">
                Generated on: {{ now()->format('d M Y, h:i A') }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 12px; border-bottom: 2px solid #1e40af;">
                Reporting Period: 
                <strong>{{ \Carbon\Carbon::parse($filters['from'])->format('d M Y') }}</strong> 
                &ndash; 
                <strong>{{ \Carbon\Carbon::parse($filters['to'])->format('d M Y') }}</strong>
            </td>
        </tr>
    </table>

    {{-- Spacer --}}
    <table><tr><td height="15"></td></tr></table>

    {{-- 2. EXECUTIVE SUMMARY (KPIs) --}}
    <table>
        <thead>
            <tr>
                <th colspan="6" style="background-color: #f3f4f6; font-weight: bold; border-bottom: 1px solid #000000; height: 25px;">
                    EXECUTIVE SUMMARY
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold; width: 20px; border: 1px solid #d1d5db; background-color: #e5e7eb;">Total Appts</th>
                <th style="font-weight: bold; width: 15px; border: 1px solid #d1d5db; background-color: #e5e7eb;">Attended</th>
                <th style="font-weight: bold; width: 15px; border: 1px solid #d1d5db; background-color: #e5e7eb;">Missed</th>
                <th style="font-weight: bold; width: 15px; border: 1px solid #d1d5db; background-color: #e5e7eb;">Attendance %</th>
                <th style="font-weight: bold; width: 15px; border: 1px solid #d1d5db; background-color: #e5e7eb;">New Patients</th>
                <th style="font-weight: bold; width: 15px; border: 1px solid #d1d5db; background-color: #e5e7eb;">Referrals</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; border: 1px solid #d1d5db; height: 25px;">{{ $kpis['total'] }}</td>
                <td style="text-align: center; border: 1px solid #d1d5db; color: #15803d; font-weight: bold;">{{ $kpis['present'] }}</td>
                <td style="text-align: center; border: 1px solid #d1d5db; color: #b91c1c;">{{ $kpis['notArrived'] }}</td>
                <td style="text-align: center; border: 1px solid #d1d5db;">{{ $kpis['rate'] }}%</td>
                <td style="text-align: center; border: 1px solid #d1d5db;">{{ $kpis['new'] }}</td>
                <td style="text-align: center; border: 1px solid #d1d5db;">{{ $kpis['referrals'] }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Spacer --}}
    <table><tr><td height="20"></td></tr></table>

    {{-- 3. DETAILED DATA TABLE --}}
    <table>
        <thead>
            <tr>
                {{-- Dark Blue Header with White Text --}}
                <th width="18" style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e40af; text-align: left;">Date</th>
                <th width="35" style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e40af; text-align: left;">Patient Name</th>
                <th width="20" style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e40af; text-align: left;">Phone</th>
                <th width="15" style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e40af; text-align: center;">Type</th>
                <th width="15" style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e40af; text-align: center;">Status</th>
                <th width="50" style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e40af; text-align: left;">Clinical Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appts as $index => $appt)
                @php
                    // Zebra Striping: Light grey background for even rows
                    $bg = ($index % 2 == 0) ? '#ffffff' : '#f9fafb';
                    
                    // Status Color Logic
                    $statusColor = '#111827'; // Default Dark Grey
                    $statusText = ucfirst($appt->status);
                    
                    if(in_array($appt->status, ['seen', 'present', 'completed'])) {
                        $statusColor = '#15803d'; // Green
                    } elseif(in_array($appt->status, ['missed', 'absent', 'cancelled'])) {
                        $statusColor = '#b91c1c'; // Red
                    } elseif($appt->status == 'referred') {
                        $statusColor = '#7e22ce'; // Purple
                    }

                    // Visit Type Logic
                    $isNew = optional($appt->patient)->created_at?->isSameDay($appt->date);
                    $visitType = $isNew ? 'New' : 'Review';
                @endphp

                <tr>
                    <td style="background-color: {{ $bg }}; border: 1px solid #e5e7eb; vertical-align: top;">
                        {{ \Carbon\Carbon::parse($appt->date)->format('d-M-Y') }}
                    </td>
                    <td style="background-color: {{ $bg }}; border: 1px solid #e5e7eb; vertical-align: top; font-weight: bold;">
                        {{ optional($appt->patient)->first_name }} {{ optional($appt->patient)->last_name }}
                    </td>
                    <td style="background-color: {{ $bg }}; border: 1px solid #e5e7eb; vertical-align: top;">
                        {{ optional($appt->patient)->phone }}
                    </td>
                    <td style="background-color: {{ $bg }}; border: 1px solid #e5e7eb; vertical-align: top; text-align: center;">
                        {{ $visitType }}
                    </td>
                    <td style="background-color: {{ $bg }}; border: 1px solid #e5e7eb; vertical-align: top; text-align: center; color: {{ $statusColor }}; font-weight: bold;">
                        {{ $statusText }}
                    </td>
                    <td style="background-color: {{ $bg }}; border: 1px solid #e5e7eb; vertical-align: top; color: #4b5563; font-style: italic;">
                        {{ $appt->notes }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>