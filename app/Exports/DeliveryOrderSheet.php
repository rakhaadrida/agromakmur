<?php

namespace App\Exports;

use App\Utilities\Services\DeliveryOrderService;
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
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveryOrderSheet extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $deliveryOrders = $this->getDeliveryData();
        $deliveryOrders = DeliveryOrderService::mapDeliveryOrderIndex($deliveryOrders);

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'deliveryOrders' => $deliveryOrders,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.delivery-order.export-index', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Delivery_Orders');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $deliveryOrders = $this->getDeliveryData();

        $range = 5 + $deliveryOrders->count();
        $rangeStr = strval($range);
        $rangeTab = 'H'.$rangeStr;

        $header = 'A5:H5';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $title = 'A1:H3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:H3')->getFont()->setBold(false)->setSize(12);

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

        $rangeNumberCell = 'C6:C'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

        $rangeNumberCell = 'G6:H'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');
    }

    public function bindValue(Cell $cell, $value)
    {
        $dateColumns = ['C'];

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

    protected function getDeliveryData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;

        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        return $baseQuery
            ->where('delivery_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('delivery_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderByDesc('delivery_orders.date')
            ->get();
    }
}
