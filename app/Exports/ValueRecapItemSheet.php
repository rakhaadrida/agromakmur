<?php

namespace App\Exports;

use App\Models\Category;
use App\Utilities\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ValueRecapItemSheet extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function view(): View
    {
        $products = ProductService::findExportProductsByCategoryId($this->category->id);

        $productStocks = ProductService::getTotalProductStock();
        $mapStockByProduct = [];
        foreach($productStocks as $productStock) {
            $mapStockByProduct[$productStock->product_id] = $productStock->total_stock;
        }

        foreach($products as $product) {
            $productPrice = $product->mainPrice ? $product->mainPrice->price : 0;
            $totalValue = $productPrice * ($mapStockByProduct[$product->id] ?? 0);

            $product->price = $productPrice;
            $product->total_value = $totalValue;
        }

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'category' => $this->category,
            'products' => $products,
            'mapStockByProduct' => $mapStockByProduct,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.report.value-recap.export', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Value_'.$this->category->name);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $products = ProductService::findExportProductsByCategoryId($this->category->id);

        $range = 4 + $products->count();
        $rangeStr = strval($range);
        $rangeTab = 'F'.$rangeStr;

        $header = 'A4:F4';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        $title = 'A1:F2';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:F2')->getFont()->setBold(false)->setSize(12);

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

        $rangeNumberCell = 'A5:B'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'D5:F'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['D', 'E', 'F'];

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float) $value);
        }

        $cell->setValueExplicit($value, DataType::TYPE_STRING2);

        return true;
    }
}
