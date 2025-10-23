<?php

namespace App\Exports;

use App\Models\GoodsReceiptItem;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductHistoryItemSheet extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    public function view(): View
    {
        $receiptItems = $this->getProductHistoryItemData();

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'receiptItems' => $receiptItems,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.report.product-history.export-detail', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Product_History_Items');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $receiptItems = $this->getProductHistoryItemData();

        $range = 4 + $receiptItems->count();
        $rangeStr = strval($range);
        $rangeTab = 'K'.$rangeStr;

        $header = 'A4:K4';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');

        $title = 'A1:K2';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:K2')->getFont()->setBold(false)->setSize(12);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $rangeTable = 'A4:'.$rangeTab;
        $sheet->getStyle($rangeTable)->applyFromArray($styleArray);

        $rangeIsiTable = 'A5:'.$rangeTab;
        $sheet->getStyle($rangeIsiTable)->getFont()->setSize(12);

        $rangeNumberCell = 'A5:A'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'C5:D'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'C5:C'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

        $rangeNumberCell = 'F5:G'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'H5:H'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'I5:K'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['F', 'G', 'I', 'J', 'K'];
        $dateColumns = ['C'];

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float) $value);
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

    protected function getProductHistoryItemData() {
        return GoodsReceiptItem::query()
            ->select(
                'goods_receipts.id AS receipt_id',
                'goods_receipts.date AS receipt_date',
                'goods_receipts.number AS receipt_number',
                'suppliers.id AS supplier_id',
                'suppliers.name AS supplier_name',
                'products.name AS product_name',
                'units.name AS unit_name',
                'goods_receipt_items.actual_quantity AS quantity',
                'goods_receipt_items.price AS price',
                'goods_receipt_items.wages AS wages',
                'goods_receipt_items.shipping_cost AS shipping_cost',
                'goods_receipt_items.total AS total',
            )
            ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->whereNull('goods_receipt_items.deleted_at')
            ->whereNull('goods_receipts.deleted_at')
            ->orderBy('products.name')
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();
    }
}
