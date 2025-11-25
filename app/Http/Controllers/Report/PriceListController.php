<?php

namespace App\Http\Controllers\Report;

use App\Exports\PriceListExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Price;
use App\Models\Subcategory;
use App\Utilities\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PriceListController extends Controller
{
    public function index() {
        $categories = Category::all();
        $subcategories = Subcategory::all();

        $mapSubcategoryByCategory = ReportService::getCommonRecapMapSubcategory($subcategories, []);
        $mapProductBySubcategory = ReportService::getPriceListMapProduct([]);
        $mapPriceByProduct = ReportService::getPriceListMapPrice([]);

        $prices = Price::all();
        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'categories' => $categories,
            'mapSubcategoryByCategory' => $mapSubcategoryByCategory,
            'mapProductBySubcategory' => $mapProductBySubcategory,
            'mapPriceByProduct' => $mapPriceByProduct,
            'prices' => $prices,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.price-list.index', $data);
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new PriceListExport(), 'Price_List_'.$fileDate.'.xlsx');
    }

    public function pdf() {
        $categories = Category::all();
        $subcategories = Subcategory::all();

        $mapSubcategoryByCategory = ReportService::getCommonRecapMapSubcategory($subcategories, []);
        $mapProductBySubcategory = ReportService::getPriceListMapProduct([]);
        $mapPriceByProduct = ReportService::getPriceListMapPrice([]);

        $prices = Price::all();
        $exportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'categories' => $categories,
            'mapSubcategoryByCategory' => $mapSubcategoryByCategory,
            'mapProductBySubcategory' => $mapProductBySubcategory,
            'mapPriceByProduct' => $mapPriceByProduct,
            'prices' => $prices,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.report.price-list.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Value_Recap'.$fileDate.'.pdf');
    }
}
