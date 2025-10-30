<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\ExportRecord; // optional if you keep export history
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentsExport;
use AppointmentsExport as GlobalAppointmentsExport;
use Illuminate\Support\Facades\Log;

class ExportWebController extends Controller
{
    /**
     * Queue / run export.
     * Supports:
     *  - CSV stream (immediate download)
     *  - Excel (.xlsx) via maatwebsite/excel (immediate download)
     */
    public function queue(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
            'format' => 'required|in:csv,excel',
            'status' => 'nullable|string'
        ]);

        $from = $data['from'];
        $to = $data['to'];
        $format = $data['format'];
        $status = $data['status'] ?? null;

        // If Excel requested and package available -> use maatwebsite
        if ($format === 'excel') {
            try {
                $filename = sprintf('ANC_appointments_%s_%s.xlsx', $from, $to);
                return Excel::download(new AppointmentsExport($from, $to, $status), $filename);
            } catch (\Throwable $e) {
                Log::warning('Excel download failed, falling back to CSV: '.$e->getMessage());
                // fallback to CSV stream below
            }
        }

        // CSV streaming (safe for large exports - uses chunking)
        $filename = sprintf('ANC_appointments_%s_%s.csv', $from, $to);
        $response = new StreamedResponse(function () use ($from, $to, $status) {
            $handle = fopen('php://output', 'w');

            // write BOM for Excel/UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // header row
            fputcsv($handle, [
                'id','date','time','patient_id','patient_first_name','patient_last_name','status','notes'
            ]);

            // chunk to avoid memory blowup
            $query = Appointment::with('patient')
                ->whereBetween('date', [$from, $to])
                ->orderBy('date');

            if (!empty($status)) {
                if ($status === 'present') {
                    $query->whereIn('status', ['queued','in_room','seen','present']);
                } else {
                    $query->where('status', $status);
                }
            }

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->id,
                        $r->date,
                        $r->time,
                        $r->patient_id,
                        optional($r->patient)->first_name,
                        optional($r->patient)->last_name,
                        $r->status,
                        $r->notes,
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Minimal export history view (optional)
     */
    public function history()
    {
        // If you persist export jobs, load them; otherwise return simple view.
        if (class_exists(ExportRecord::class)) {
            $records = ExportRecord::latest()->paginate(20);
            return view('exports.history', compact('records'));
        }

        return view('exports.history'); // create blade with message if no records model
    }
}
