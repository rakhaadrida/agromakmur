<?php

namespace App\Exports;

use App\Models\Customer;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
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

class AccountReceivableDetailExport extends DefaultValueBinder  implements FromView, ShouldAutoSize, WithStyles, WithCustomValueBinder
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
        $receivableItems = $this->getReceivableItemsData();

        $customer = Customer::query()->find($this->request->id);
        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $this->request->start_date,
            'finalDate' => $this->request->final_date,
            'receivableItems' => $receivableItems,
            'customer' => $customer,
            'exportDate' => $exportDate,
        ];

        return view('pages.finance.account-receivable.export-detail', $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle('Account_Receivable');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('/assets/img/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

        $receivableItems = $this->getReceivableItemsData();

        $range = 5 + $receivableItems->count();
        $rangeStr = strval($range);
        $rangeTab = 'L'.$rangeStr;

        $header = 'A5:L5';
        $sheet->getStyle($header)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($header)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($header)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffddb5');

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

        $rangeIsiTable = 'A6:'.$rangeTab;
        $sheet->getStyle($rangeIsiTable)->getFont()->setSize(12);

        $rangeNumberCell = 'A6:E'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'C6:D'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('dd-mm-yyyy');

        $rangeNumberCell = 'G6:G'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');

        $rangeNumberCell = 'H6:K'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('right');
        $sheet->getStyle($rangeNumberCell)->getNumberFormat()->setFormatCode('#,##0');

        $rangeNumberCell = 'L6:L'.$rangeStr;
        $sheet->getStyle($rangeNumberCell)->getAlignment()->setHorizontal('center');
    }

    public function bindValue(Cell $cell, $value)
    {
        $numericalColumns = ['H', 'I', 'J', 'K'];
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

    protected function getReceivableItemsData() {
        $startDate = $this->request->start_date;
        $finalDate = $this->request->final_date;

        $accountReceivableStatuses = Constant::ACCOUNT_RECEIVABLE_STATUSES;
        $status = $accountReceivableStatuses;

        if(!empty($this->request->status)) {
            $status = [$this->request->status];
        }

        $baseQuery = AccountReceivableService::getBaseQueryDetail();

        $accountReceivables = $baseQuery
            ->where('sales_orders.customer_id', $this->request->id)
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereIn('account_receivables.status', $status)
            ->orderBy('sales_orders.date')
            ->orderBy('sales_orders.id')
            ->get();

        foreach($accountReceivables as $accountReceivable) {
            $paymentAmount = $accountReceivable->payment_amount ?? 0;
            $returnAmount = $accountReceivable->return_amount ?? 0;
            $outstandingAmount = $accountReceivable->grand_total - $paymentAmount - $returnAmount;

            $accountReceivable->outstanding_amount = $outstandingAmount;
        }

        return $accountReceivables;
    }
}
