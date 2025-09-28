<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use App\Exports\AppointmentsExport;
use App\Models\ExportRecord;

class GenerateExportJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $filters;
    protected $exportId;

    public function __construct($exportId, $filters)
    {
        $this->exportId = $exportId;
        $this->filters = $filters;
    }

   // In handle() method of app/Jobs/GenerateExportJob.php
        // public function handle()
        // {
        //     $fileName = 'exports/appointments_' . $this->exportId . '.xlsx';
        //     \Maatwebsite\Excel\Facades\Excel::store(new AppointmentsExport($this->filters), $fileName, 'local');

        //     // Create password-protected ZIP for encryption (password: generate and store securely, e.g., in ExportRecord)
        //     $zipPath = 'exports/appointments_' . $this->exportId . '.zip';
        //     $zip = new \ZipArchive();
        //     $zip->open(storage_path('app/' . $zipPath), \ZipArchive::CREATE);
        //     $zip->addFile(storage_path('app/' . $fileName), basename($fileName));
        //     $zip->setEncryptionName(basename($fileName), \ZipArchive::EM_AES_256, 'secure_password'); // Replace with dynamic password
        //     $zip->close();

        //     // Delete original XLSX
        //     Storage::delete($fileName);

        //     $record = ExportRecord::find($this->exportId);
        //     if ($record) {
        //         $record->update(['file_path' => $zipPath]);
        //     }
        // }
}
