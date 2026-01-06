<?php

namespace App\Http\Controllers\Report;

use App\Exports\PriceListExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Price;
use App\Utilities\Constant;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PriceListController extends Controller
{
    public function index() {
        $categories = Category::all();
        $products = ProductService::getBaseQueryIndex();
        $mapPriceByProduct = ReportService::getPriceListMapPrice([]);

        $prices = Price::all();

        if(isUserSales()) {
            $prices = $prices->filter(function($price) {
                return $price->type != Constant::PRICE_TYPE_GENERAL;
            });
        }

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'categories' => $categories,
            'products' => $products,
            'mapPriceByProduct' => $mapPriceByProduct,
            'prices' => $prices,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.price-list.index', $data);
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new PriceListExport(), 'Daftar_Harga_'.$fileDate.'.xlsx');
    }

    public function pdf() {
        $products = ProductService::getBaseQueryIndex();
        $mapPriceByProduct = ReportService::getPriceListMapPrice([]);

        $prices = Price::all();
        if(isUserSales()) {
            $prices = $prices->filter(function($price) {
                return $price->type != Constant::PRICE_TYPE_GENERAL;
            });
        }

        $exportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'products' => $products,
            'mapPriceByProduct' => $mapPriceByProduct,
            'prices' => $prices,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.report.price-list.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Daftar_Harga_'.$fileDate.'.pdf');
    }
}
