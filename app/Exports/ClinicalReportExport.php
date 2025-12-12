<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClinicalReportExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;

    // We pass the full dataset (appointments + KPIs) from the controller
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Renders the Blade view into Excel
     */
    public function view(): View
    {
        return view('reports.clinical_report', [
            'appts'   => $this->data['appts'],
            'kpis'    => $this->data['kpis'],
            'filters' => $this->data['filters']
        ]);
    }

    /**
     * Title of the Sheet tab at the bottom
     */
    public function title(): string
    {
        return 'Clinical Report';
    }

    /**
     * Styling Logic (Fonts, Colors, Borders)
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Set Default Font
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);

        // 2. Main Title (Row 1) - Big & Bold
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // 3. KPI Header (Row 5) - Dark Background, White Text
        $sheet->getStyle('A5:F5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A5:F5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('333333'); // Dark Grey

        // 4. Detailed List Header (Row 10) - Blue Background
        // Note: We hardcode Row 10 because our View structure is fixed.
        $sheet->getStyle('A10:G10')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A10:G10')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('3B82F6'); // Brand Blue

        // 5. Center align Status column (Column F)
        $sheet->getStyle('F')->getAlignment()->setHorizontal('center');
        
        return [];
    }
}