<?php

namespace App\Exports;

use App\Models\Supplier;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
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

class AccountPayableDetailExport extends DefaultValueBinder  implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    use Exportable;

    protected $id;
    protected Request $request;

    public function __construct($id, Request $request)
    {
        $this->id = $id;
        $this->request = $request;
    }

    public function view(): View
    {
        $payableItems = $this->getPayableItemsData();

        $supplier = Supplier::query()->find($this->request->id);
        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'payableItems' => $payableItems,
            'supplier' => $supplier,
            'exportDate' => $exportDate,
        ];

        return view('pages.finance.account-payable.export-detail', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Daftar_Hutang');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $payableItems = $this->getPayableItemsData();

        $range = 5 + $payableItems->count();
        $rangeStr = strval($range);
        $rangeTab = 'K'.$rangeStr;

        $header = 'A5:K5';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffddb5');

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        $title = 'A1:K3';
        $sheet->getStyle($title)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:J3')->getFont()->setBold(false)->setSize(12);

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

        $rangeNumberCell = 'A6:E'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'C6:D'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

        $rangeNumberCell = 'G6:J'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'K6:K'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['G', 'H', 'I', 'J'];
        $dateColumns = ['C', 'D'];

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

    protected function getPayableItemsData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;

        $accountPayableStatuses = Constant::ACCOUNT_PAYABLE_STATUSES;
        $status = $accountPayableStatuses;

        if(!empty($this->request->status)) {
            $status = [$this->request->status];
        }

        $baseQuery = AccountPayableService::getBaseQueryDetail();

        $accountPayables = $baseQuery
            ->where('goods_receipts.supplier_id', $this->id)
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereIn('account_payables.status', $status)
            ->orderByDesc('goods_receipts.date')
            ->orderBy('goods_receipts.id')
            ->get();

        foreach($accountPayables as $accountPayable) {
            $paymentAmount = $accountPayable->payment_amount ?? 0;
            $returnAmount = $accountPayable->return_amount ?? 0;
            $outstandingAmount = $accountPayable->grand_total - $paymentAmount - $returnAmount;

            $accountPayable->outstanding_amount = $outstandingAmount;
        }

        return $accountPayables;
    }
}
