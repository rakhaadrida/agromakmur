<?php

namespace App\Exports;

use App\Models\Price;
use App\Models\Product;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PriceListExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function view(): View
    {
        $products = ProductService::getBaseQueryIndex();
        $mapPriceByProduct = ReportService::getPriceListMapPrice([]);

        $prices = Price::all();
        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'products' => $products,
            'mapPriceByProduct' => $mapPriceByProduct,
            'prices' => $prices,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.report.price-list.export', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Price_List');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $products = Product::all();
        $prices = Price::all();

        $range = 4 + $products->count();
        $rangeStr = strval($range);
        $rangeColumn = $this->numberToExcelColumn(4 + $prices->count());
        $rangeTab = $rangeColumn.$rangeStr;

        $header = 'A4:'.$rangeColumn.'4';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:'.$rangeColumn.'1');
        $sheet->mergeCells('A2:'.$rangeColumn.'2');

        $title = 'A1:'.$rangeColumn.'2';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:'.$rangeColumn.'2')->getFont()->setBold(false)->setSize(12);

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

        $rangeNumberCell = 'D5:D'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeQuantityColumn = $this->numberToExcelColumn(4 + $prices->count());
        $rangeTab = $rangeQuantityColumn.$rangeStr;

        $rangeNumberCell = 'E5:'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function bindValue(Cell $cell, $value)
    {
        $prices = Price::all();
        $numbers = range(4, 4 + $prices->count());
        $columns = $this->convertNumbersToLetters($numbers);

        $numericalColumns = $columns;

        if (in_array($cell->getColumn(), $numericalColumns) && is_numeric($value)) {
            return parent::bindValue($cell, (float) $value);
        }

        $cell->setValueExplicit($value, DataType::TYPE_STRING2);

        return true;
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
