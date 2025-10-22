<?php

namespace App\Http\Controllers\Report;

use App\Exports\IncomingItemExport;
use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use App\Utilities\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class IncomingItemController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(30)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $receiptItems = ReportService::getIncomingItemsData($startDate, $finalDate);

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'receiptItems' => $receiptItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.incoming-item.index', $data);
    }

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new IncomingItemExport($request), 'Incoming_Items_'.$fileDate.'.xlsx');
    }
}
