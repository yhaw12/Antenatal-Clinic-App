<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExportRecord;
use App\Jobs\GenerateExportJob;
use Illuminate\Support\Str;

class ExportWebController extends Controller
{
    public function index()
    {
        $exports = ExportRecord::where('exported_by', auth()->id())->latest()->paginate(20);
        return view('exports.index', compact('exports'));
    }

    public function queue(Request $request)
    {
        $this->authorize('manage-exports');

        $data = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);

        $record = ExportRecord::create([
            'exported_by' => $request->user()->id,
            'file_path' => 'exports/pending_'.Str::uuid().'.enc',
            'filters' => $data,
            'encrypted' => true
        ]);

        // Dispatch real job for production:
        GenerateExportJob::dispatch($record->id, $data);

        return redirect()->route('exports.index')->with('success','Export queued');
    }

    public function history()
    {
        $exports = ExportRecord::latest()->paginate(25);
        return view('exports.history', compact('exports'));
    }
}
