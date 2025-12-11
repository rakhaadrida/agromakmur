<?php

namespace App\Http\Controllers\Report;

use App\Exports\StockRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Warehouse;
use App\Utilities\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class StockRecapController extends Controller
{
    public function index() {
        $categories = Category::all();

        $mapProductByCategory = ReportService::getCommonRecapMapProduct([]);
        [$mapStockByProduct, $mapTotalStockByCategory, $mapTotalStockByCategoryWarehouse] = ReportService::getStockRecapMapStock([], [], []);

        $warehouses = Warehouse::all();
        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'categories' => $categories,
            'mapProductByCategory' => $mapProductByCategory,
            'mapStockByProduct' => $mapStockByProduct,
            'mapTotalStockByCategory' => $mapTotalStockByCategory,
            'mapTotalStockByCategoryWarehouse' => $mapTotalStockByCategoryWarehouse,
            'warehouses' => $warehouses,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.stock-recap.index', $data);
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new StockRecapExport(), 'Rekap_Stok_'.$fileDate.'.xlsx');
    }

    public function pdf() {
        $categories = Category::all();

        $mapProductByCategory = ReportService::getCommonRecapMapProduct([]);
        [$mapStockByProduct, $mapTotalStockByCategory, $mapTotalStockByCategoryWarehouse] = ReportService::getStockRecapMapStock([], [], []);

        $warehouses = Warehouse::all();
        $exportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'categories' => $categories,
            'mapProductByCategory' => $mapProductByCategory,
            'mapStockByProduct' => $mapStockByProduct,
            'mapTotalStockByCategory' => $mapTotalStockByCategory,
            'mapTotalStockByCategoryWarehouse' => $mapTotalStockByCategoryWarehouse,
            'warehouses' => $warehouses,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.report.stock-recap.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Rekap_Stok_'.$fileDate.'.pdf');
    }
}
