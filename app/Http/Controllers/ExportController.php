<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExportWebController extends Controller
{
    /**
     * Queue or immediately generate export.
     * Currently: immediate CSV download for small ranges.
     */
    public function queue(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'format' => 'required|in:excel,csv',
        ]);

        $from = Carbon::parse($data['from'])->toDateString();
        $to   = Carbon::parse($data['to'])->toDateString();
        $format = $data['format'];

        // Small safety guard: for very large ranges prefer queueing
        $count = Appointment::whereBetween('date', [$from, $to])->count();
        if ($count > 5000 && $format === 'excel') {
            // Ideally dispatch a queued job to build the file and persist
            return back()->with('error', 'Large export requested — please use background export (not implemented).');
        }

        if ($format === 'csv') {
            // stream CSV directly
            $filename = 'appointments_' . now()->format('Ymd_His') . '.csv';

            $response = new StreamedResponse(function () use ($from, $to) {
                $handle = fopen('php://output', 'w');
                // header row
                fputcsv($handle, ['id','patient_id','patient_name','date','time','status','notes','created_at']);

                Appointment::with('patient')
                    ->whereBetween('date', [$from, $to])
                    ->orderBy('date')
                    ->orderBy('time')
                    ->chunk(500, function($rows) use ($handle) {
                        foreach ($rows as $row) {
                            $patientName = optional($row->patient)->first_name . ' ' . optional($row->patient)->last_name;
                            fputcsv($handle, [
                                $row->id,
                                $row->patient_id,
                                $patientName,
                                $row->date,
                                $row->time,
                                $row->status,
                                $row->notes,
                                $row->created_at,
                            ]);
                        }
                    });

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
            return $response;
        }

        if ($format === 'excel') {
            // Option A: if you have maatwebsite/excel installed, use it here.
            // Option B: fallback to CSV download and suggest user install the package.
            return back()->with('error', 'Excel export requested but not implemented. Install maatwebsite/excel for XLSX support: composer require maatwebsite/excel');
        }

        return back()->with('error', 'Unsupported export format');
    }

    /**
     * Simple exports history placeholder (improve by storing export jobs)
     */
    public function history()
    {
        // TODO: replace with the ExportJob model or filesystem listing
        $history = collect([]); // placeholder
        return view('exports.history', compact('history'));
    }

    /**
     * Stream a previously stored file from storage (if you implement queued exports).
     */
    public function download($filename)
    {
        if (!Storage::disk('local')->exists("exports/{$filename}")) {
            abort(404);
        }
        return response()->download(storage_path("app/exports/{$filename}"));
    }
}
