<?php

namespace App\Http\Controllers\Report;

use App\Exports\ProductHistoryDetailExport;
use App\Exports\ProductHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductHistoryController extends Controller
{
    public function index() {
        $products = ReportService::getProductHistoryData();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'products' => $products,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.product-history.index', $data);
    }

    public function show(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $supplierId = $filter->supplier_id ?? null;

        $suppliers = Supplier::all();
        $product = Product::query()->findOrFail($id);

        $receiptItems = ReportService::getProductHistoryDetail($startDate, $finalDate, $product->id, $supplierId);

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'product' => $product,
            'receiptItems' => $receiptItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.product-history.detail', $data);
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new ProductHistoryExport(), 'Product_History_'.$fileDate.'.xlsx');
    }

    public function exportDetail(Request $request, $id) {
        $product = Product::query()->findOrFail($id);
        $productName = preg_replace('/\s+/', '_', $product->name);

        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new ProductHistoryDetailExport($id, $request), 'Product_History_'.$productName.'_'.$fileDate.'.xlsx');
    }
}
