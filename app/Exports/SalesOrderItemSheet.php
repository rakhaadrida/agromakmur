<?php

namespace App\Exports;

use App\Utilities\Services\SalesOrderService;
use App\Utilities\Services\WarehouseService;
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

class SalesOrderItemSheet extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $salesOrderItems = $this->getOrderItemData();

        $productWarehouses = [];
        foreach($salesOrderItems as $salesOrderItem) {
            $productWarehouses[$salesOrderItem->sales_order_id][$salesOrderItem->product_id][$salesOrderItem->warehouse_id] = $salesOrderItem->quantity;
        }

        $salesOrderItems = SalesOrderService::mapSalesOrderItemExport($salesOrderItems);
        $warehouses = WarehouseService::getGeneralWarehouse();

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'productWarehouses' => $productWarehouses,
            'salesOrderItems' => $salesOrderItems,
            'warehouses' => $warehouses,
            'totalWarehouses' => $warehouses->count(),
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.sales-order.export-detail', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Item_Sales_Order');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $salesOrderItems = $this->getOrderItemData();
        $salesOrderItems = SalesOrderService::mapSalesOrderItemExport($salesOrderItems);
        $warehouses = WarehouseService::getGeneralWarehouse();

        $range = 6 + $salesOrderItems->count();
        $rangeStr = strval($range);
        $rangeColumn = $this->numberToExcelColumn(11 + $warehouses->count());
        $rangeTab = $rangeColumn.$rangeStr;

        $header = 'A5:'.$rangeColumn.'6';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:'.$rangeColumn.'1');
        $sheet->mergeCells('A2:'.$rangeColumn.'2');
        $sheet->mergeCells('A3:'.$rangeColumn.'3');

        $title = 'A1:'.$rangeColumn.'3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:'.$rangeColumn.'3')->getFont()->setBold(false)->setSize(12);

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

        $rangeIsiTable = 'A7:'.$rangeTab;
        $sheet->getStyle($rangeIsiTable)->getFont()->setSize(12);

        $rangeNumberCell = 'A7:A'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'B7:C'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeQuantityColumn = $this->numberToExcelColumn(5 + $warehouses->count());
        $rangeTab = $rangeQuantityColumn.$rangeStr;

        $rangeNumberCell = 'E7:'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeUnitColumn = $this->numberToExcelColumn(5 + $warehouses->count() + 1);
        $rangeTab = $rangeUnitColumn.$rangeStr;

        $rangeNumberCell = $rangeUnitColumn.'7:'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangePriceColumn = $this->numberToExcelColumn(5 + $warehouses->count() + 2);
        $rangeFinalAmountColumn = $this->numberToExcelColumn(5 + $warehouses->count() + 6);
        $rangeTab = $rangeFinalAmountColumn.$rangeStr;

        $rangeNumberCell = $rangePriceColumn.'7:'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeDiscountColumn = $this->numberToExcelColumn(5 + $warehouses->count() + 4);
        $rangeTab = $rangeDiscountColumn.$rangeStr;

        $rangeNumberCell = $rangeDiscountColumn.'7:'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('@');
    }

    public function bindValue(Cell $cell, $value)
    {
        $warehouses = WarehouseService::getGeneralWarehouse();
        $numbers = range(5, 5 + $warehouses->count());
        $columns = $this->convertNumbersToLetters($numbers);

        $numericalColumns = $columns;

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float) $value);
        }

        $cell->setValueExplicit($value, DataType::TYPE_STRING2);

        return true;
    }

    protected function getOrderItemData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;

        $baseQuery = SalesOrderService::getBaseQueryExportItem();

        return $baseQuery
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('sales_orders.date')
            ->get();
    }

    protected function numberToExcelColumn($num) {
        $result = '';
        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $result = chr(65 + $mod) . $result;
            $num = intval(($num - $mod) / 26);
        }
        return $result;
    }

    protected function convertNumbersToLetters(array $numbers) {
        return array_map('numberToExcelColumn', $numbers);
    }
}
