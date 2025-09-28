<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppointmentsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Appointment::with('patient')
            ->whereBetween('scheduled_date', [$this->filters['date_from'], $this->filters['date_to']])
            ->get()
            ->map(function ($appt) {
                return [
                    'id' => $appt->id,
                    'patient_name' => $appt->patient->first_name . ' ' . $appt->patient->last_name,
                    'scheduled_date' => $appt->scheduled_date,
                    'status' => $appt->status,
                    // Add more fields as needed
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Patient Name', 'Scheduled Date', 'Status'];
    }
}