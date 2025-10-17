<?php

namespace App\Exports;

use App\Utilities\Services\DeliveryOrderService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveryOrderSheet implements FromView, ShouldAutoSize, WithStyles
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

        $rangeNumberCell = 'A6:D'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'F6:G'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');
    }

    protected function getDeliveryData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;

        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        return $baseQuery
            ->where('delivery_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('delivery_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('delivery_orders.date')
            ->get();
    }
}
