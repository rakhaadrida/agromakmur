<?php

namespace App\Exports;

use App\Models\Product;
use App\Utilities\Services\StockCardService;
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

class StockCardExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $filter = (object) $this->request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(30)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $productId = $filter->product_id ?? null;

        if($productId) {
            $product = Product::query()->findOrFail($productId);
            $stockLogs = StockCardService::getBaseQueryProductIndex($startDate, $finalDate, $productId);

            $currentStock = $product->productStocks()->sum('stock');

            $totalIncomingQuantity = $stockLogs->where('quantity', '>=', 0)->sum('quantity');
            $totalOutgoingQuantity = $stockLogs->where('quantity', '<', 0)->sum('quantity');

            $initialStock = $currentStock - $totalIncomingQuantity + ($totalOutgoingQuantity * -1);
        }

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'product' => $product ?? null,
            'stockLogs' => $stockLogs ?? collect([]),
            'initialStock' => $initialStock ?? 0,
            'totalIncomingQuantity' => $totalIncomingQuantity ?? 0,
            'totalOutgoingQuantity' => $totalOutgoingQuantity ?? 0,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.report.stock-card.export', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Stock-Card');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $stockLogs = collect([]);

        if($this->request->product_id) {
            $stockLogs = StockCardService::getBaseQueryProductIndex($this->request->start_date, $this->request->final_date, $this->request->product_id);
        }

        $range = 6 + $stockLogs->count() + 3;
        $rangeStr = strval($range);
        $rangeTab = 'L'.$rangeStr;

        $header = 'A5:L6';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle($header)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00');

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $title = 'A1:L3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:L3')->getFont()->setBold(false)->setSize(12);

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

        $rangeNumberCell = 'A7:L7';
        $sheet->getStyle($rangeNumberCell)->getFont()->setBold(true);

        $rangeNumberCell = 'A7:C'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'F7:F'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'H7:I'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'K7:K'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'B8:B'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

        $rangeNumberCell = 'L8:L'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('hh:mm:ss');
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $range = 8 + $stockLogs->count();
        $rangeStr = strval($range);
        $rangeTab = 'L'.$rangeStr;

        $rangeNumberCell = 'A'.$rangeStr.':'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getFont()->setBold(true);

        $range += 1;
        $rangeStr = strval($range);
        $rangeTab = 'L'.$rangeStr;

        $rangeNumberCell = 'A'.$rangeStr.':'.$rangeTab;
        $sheet->getStyle($rangeNumberCell)->getFont()->setBold(true);
        $sheet->getStyle($rangeNumberCell)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['F', 'H', 'I', 'K'];
        $dateColumns = ['B', 'L'];

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
}
