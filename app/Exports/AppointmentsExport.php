<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AppointmentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $from;
    protected $to;
    protected $status;

    public function __construct($from, $to, $status = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->status = $status;
    }

    public function query()
    {
        $q = Appointment::with('patient')
            ->whereBetween('date', [$this->from, $this->to])
            ->orderBy('date');

        if (!empty($this->status)) {
            if ($this->status === 'present') {
                $q->whereIn('status', ['queued','in_room','seen','present']);
            } else {
                $q->where('status', $this->status);
            }
        }

        return $q;
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->date,
            $row->time,
            $row->patient_id,
            optional($row->patient)->first_name,
            optional($row->patient)->last_name,
            $row->status,
            $row->notes,
        ];
    }

    public function headings(): array
    {
        return ['id','date','time','patient_id','patient_first_name','patient_last_name','status','notes'];
    }
}
