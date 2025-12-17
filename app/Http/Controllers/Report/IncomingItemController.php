<?php

namespace App\Http\Controllers\Report;

use App\Exports\IncomingItemExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Utilities\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IncomingItemController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $productId = $filter->product_id ?? null;

        $receiptItems = ReportService::getIncomingItemsData($startDate, $finalDate, $productId);

        $products = Product::all();
        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'productId' => $productId,
            'receiptItems' => $receiptItems,
            'products' => $products,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.incoming-item.index', $data);
    }

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new IncomingItemExport($request), 'Laporan_Barang_Masuk_'.$fileDate.'.xlsx');
    }
}
