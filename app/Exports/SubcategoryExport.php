<?php

namespace App\Exports;

use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubcategoryExport implements FromView, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function view(): View
    {
        $subcategories = Subcategory::withTrashed()
            ->select(
                'subcategories.*',
                'categories.name AS category_name'
            )
            ->leftJoin('categories', 'categories.id', 'subcategories.category_id')
            ->where('subcategories.is_destroy', 0)
            ->get();
        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'subcategories' => $subcategories,
            'exportDate' => $exportDate,
        ];

        return view('pages.admin.subcategory.export', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Subcategory');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $subcategories = Subcategory::withTrashed()->where('is_destroy', 0)->get();

        $range = 4 + $subcategories->count();
        $rangeStr = strval($range);
        $rangeTab = 'E'.$rangeStr;

        $header = 'A4:E4';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $title = 'A1:E2';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:E2')->getFont()->setBold(false)->setSize(12);

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
}
