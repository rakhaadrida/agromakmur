<?php

namespace App\Exports;

use App\Utilities\Services\DeliveryOrderService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveryOrderItemSheet extends DefaultValueBinder  implements FromView, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $deliveryOrderItems = $this->getDeliveryItemData();

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'deliveryOrderItems' => $deliveryOrderItems,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.delivery-order.export-detail', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Delivery_Order_Items');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $deliveryOrderItems = $this->getDeliveryItemData();

        $range = 5 + $deliveryOrderItems->count();
        $rangeStr = strval($range);
        $rangeTab = 'F'.$rangeStr;

        $header = 'A5:F5';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');

        $title = 'A1:F3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:F3')->getFont()->setBold(false)->setSize(12);

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

        $rangeNumberCell = 'A6:D'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'E6:E'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'F6:F'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['E'];

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float) $value);
        }

        $cell->setValueExplicit($value, DataType::TYPE_STRING2);

        return true;
    }

    protected function getDeliveryItemData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;

        $baseQuery = DeliveryOrderService::getBaseQueryExportItem();

        return $baseQuery
            ->where('delivery_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('delivery_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('delivery_orders.date')
            ->get();
    }
}
