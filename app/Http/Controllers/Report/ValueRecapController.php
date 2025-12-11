<?php

namespace App\Http\Controllers\Report;

use App\Exports\ValueRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Utilities\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ValueRecapController extends Controller
{
    public function index() {
        $categories = Category::all();

        [$mapStockByProduct, $mapTotalStockByCategory] = ReportService::getValueRecapMapStock([], []);
        [$mapProductByCategory, $mapTotalValueByCategory] = ReportService::getValueRecapMapProduct($mapStockByProduct, [], []);

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'categories' => $categories,
            'mapProductByCategory' => $mapProductByCategory,
            'mapStockByProduct' => $mapStockByProduct,
            'mapTotalStockByCategory' => $mapTotalStockByCategory,
            'mapTotalValueByCategory' => $mapTotalValueByCategory,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.value-recap.index', $data);
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new ValueRecapExport(), 'Rekap_Value_'.$fileDate.'.xlsx');
    }

    public function pdf() {
        $categories = Category::all();

        [$mapStockByProduct, $mapTotalStockByCategory] = ReportService::getValueRecapMapStock([], []);
        [$mapProductByCategory, $mapTotalValueByCategory] = ReportService::getValueRecapMapProduct($mapStockByProduct, [], []);

        $exportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'categories' => $categories,
            'mapProductByCategory' => $mapProductByCategory,
            'mapStockByProduct' => $mapStockByProduct,
            'mapTotalStockByCategory' => $mapTotalStockByCategory,
            'mapTotalValueByCategory' => $mapTotalValueByCategory,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.report.value-recap.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Rekap_Value_'.$fileDate.'.pdf');
    }
}
