<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductConversion;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport extends DefaultValueBinder  implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    use Exportable;

    public function view(): View
    {
        $products = Product::withTrashed()
            ->select(
                'products.*',
                'categories.name AS category_name',
                'units.name AS unit_name'
            )
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->where('products.is_destroy', 0)
            ->get();

        $productConversions = ProductConversion::withTrashed()->get();
        $conversions = $productConversions->mapWithKeys(function($productConversion) {
            $array = [];

            $array[$productConversion->product_id] = [
                'unit_name' => $productConversion->unit->name,
                'quantity' => $productConversion->quantity,
            ];

            return $array;
        });

        $conversions = $conversions->toArray();
        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'products' => $products,
            'conversions' => $conversions,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.product.export', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Product');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $products = Product::withTrashed()->where('is_destroy', 0)->get();

        $range = 4 + $products->count();
        $rangeStr = strval($range);
        $rangeTab = 'G'.$rangeStr;

        $header = 'A4:G4';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $title = 'A1:G2';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:G2')->getFont()->setBold(false)->setSize(12);

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
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['F'];

        if (!in_array($cell->getColumn(), $numericalColumns) || $value == '' || $value == null) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING2);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
