<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Services\PurchaseRecapService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseRecapDetailExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    use Exportable;

    protected $id;
    protected $request;

    public function __construct($id, Request $request)
    {
        $this->id = $id;
        $this->request = $request;
    }

    public function view(): View
    {
        $purchaseItems = $this->getPurchaseRecapItemsData();

        if($this->request->subject == 'products') {
            $item = Product::query()->find($this->id);
        } elseif ($this->request->subject == 'suppliers') {
            $item = Supplier::query()->find($this->id);
        }

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'subject' => $this->request->subject,
            'purchaseItems' => $purchaseItems,
            'item' => $item ?? null,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.report.purchase-recap.export-detail', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Rekap_Pembelian');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $purchaseItems = $this->getPurchaseRecapItemsData();

        $range = 5 + $purchaseItems->count();
        $rangeStr = strval($range);
        $rangeTab = 'J' . $rangeStr;

        $header = 'A5:J5';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        $title = 'A1:J3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:J3')->getFont()->setBold(false)->setSize(12);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $rangeTable = 'A5:' . $rangeTab;
        $sheet->getStyle($rangeTable)->applyFromArray($styleArray);

        $rangeIsiTable = 'A6:' . $rangeTab;
        $sheet->getStyle($rangeIsiTable)->getFont()->setSize(12);

        $rangeNumberCell = 'A6:C' . $rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'B6:B' . $rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

        $rangeNumberCell = 'E5:E' . $rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'F5:F' . $rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'G5:J' . $rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['E', 'G', 'H', 'I', 'J'];
        $dateColumns = ['B'];

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float)$value);
        }

        if (in_array($cell->getColumn(), $dateColumns) && !empty($value)) {
            try {
                $excelDate = Date::PHPToExcel(\Carbon\Carbon::parse($value));
                $cell->setValueExplicit($excelDate, DataType::TYPE_NUMERIC);

                return true;
            } catch (\Exception $e) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING2);

                return true;
            }
        }

        $cell->setValueExplicit($value, DataType::TYPE_STRING2);

        return true;
    }

    protected function getPurchaseRecapItemsData()
    {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;
        $subject = $this->request->subject ?? null;
        $supplierId = $this->request->supplier_id ?? null;
        $productId = $this->request->product_id ?? null;

        $purchaseItems = collect([]);
        if($subject == 'products') {
            $purchaseItems = PurchaseRecapService::getBaseQueryProductDetail($this->id, $startDate, $finalDate, $supplierId);
        } elseif ($subject == 'suppliers') {
            $purchaseItems = PurchaseRecapService::getBaseQuerySupplierDetail($this->id, $startDate, $finalDate, $productId);
        }

        return $purchaseItems;
    }
}
