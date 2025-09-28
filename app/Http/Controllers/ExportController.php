<?php
namespace App\Http\Controllers;

use App\Models\ExportRecord;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\AppointmentsExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Bus;

class ExportController extends Controller
{
    // Admin calls this to queue an export. Returns job id
    public function queueExport(Request $request)
    {
        $this->authorize('manage-exports');

        $data = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);

        $filters = ['date_from' => $data['date_from'], 'date_to' => $data['date_to']];

        // Push a job to queue that will generate excel and encrypt it (job class not included here)
        $jobId = (string) Str::uuid();

        // store a placeholder export record
        $record = ExportRecord::create([
            'exported_by' => $request->user()->id,
            'file_path' => 'exports/pending_' . $jobId . '.enc',
            'filters' => $filters,
            'encrypted' => true
        ]);

        // IMPORTANT: in production dispatch a job to create & encrypt the file and update record.file_path when done.

        return response()->json(['ok' => true, 'export_id' => $record->id, 'message' => 'Export queued']);
    }
}
