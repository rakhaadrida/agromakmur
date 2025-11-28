<?php

namespace App\Exports;

use App\Utilities\Constant;
use App\Utilities\Services\SalesRecapService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesRecapSheet extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $salesItems = $this->getSalesRecapData();

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'subject' => $this->request->subject,
            'subjectLabel' => Constant::SALES_RECAP_TYPE_LABELS[$this->request->subject],
            'salesItems' => $salesItems,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.report.sales-recap.export-index', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Rekap_Penjualan');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $salesItems = $this->getSalesRecapData();

        $range = 5 + $salesItems->count();
        $rangeStr = strval($range);
        $rangeTab = 'G'.$rangeStr;

        $header = 'A5:G5';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        $title = 'A1:G3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:G3')->getFont()->setBold(false)->setSize(12);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $rangeTable = 'A5:'.$rangeTab;
        $sheet->getStyle($rangeTable)->applyFromArray($styleArray);

        $rangeIsiTable = 'A6:'.$rangeTab;
        $sheet->getStyle($rangeIsiTable)->getFont()->setSize(12);

        if(!$salesItems->count()) {
            $rangeIsiTable = 'A6:G6';
            $sheet->getStyle($rangeIsiTable)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($rangeIsiTable)->getFont()->setBold(true);
        }

        if(isSubjectProduct($this->request->subject)) {
            $rangeNumberCell = 'A6:B'.$rangeStr;
            $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

            $rangeNumberCell = 'D6:E'.$rangeStr;
            $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
            $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

            $rangeNumberCell = 'F6:F'.$rangeStr;
            $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

            $rangeNumberCell = 'G6:G'.$rangeStr;
            $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
            $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');
        } else {
            $rangeNumberCell = 'A6:A'.$rangeStr;
            $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

            $rangeNumberCell = 'C6:G'.$rangeStr;
            $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
            $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');
        }
    }

    public function bindValue(Cell $cell, $value)
    {
        if(isSubjectProduct($this->request->subject)) {
            $numericalColumns = ['D', 'E', 'G'];
        } else {
            $numericalColumns = ['C', 'D', 'E', 'F', 'G'];
        }

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float) $value);
        }

        $cell->setValueExplicit($value, DataType::TYPE_STRING2);

        return true;
    }

    protected function getSalesRecapData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;
        $subject = $this->request->subject ?? null;

        $salesItems = collect([]);
        if($subject == 'product') {
            $salesItems = SalesRecapService::getBaseQueryProductIndex($startDate, $finalDate);
        }
        else if($subject == 'customer') {
            $salesItems = SalesRecapService::getBaseQueryCustomerIndex($startDate, $finalDate);
        }

        return $salesItems;
    }
}
