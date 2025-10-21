<?php

namespace App\Http\Controllers\Report;

use App\Exports\StockRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Subcategory;
use App\Models\Warehouse;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class StockRecapController extends Controller
{
    public function index() {
        $categories = Category::all();
        $subcategories = Subcategory::all();

        $mapSubcategoryByCategory = [];
        foreach($subcategories as $subcategory) {
            $mapSubcategoryByCategory[$subcategory->category_id][] = $subcategory;
        }

        $products = Product::all();
        $mapProductBySubcategory = [];
        foreach($products as $product) {
            $mapProductBySubcategory[$product->subcategory_id][] = $product;
        }

        $productStocks = ProductStock::query()->whereNull('deleted_at')->get();
        $mapStockByProduct = [];
        foreach($productStocks as $productStock) {
            $mapStockByProduct[$productStock->product_id][$productStock->warehouse_id] = $productStock->stock;
        }

        $warehouses = Warehouse::all();
        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'categories' => $categories,
            'mapSubcategoryByCategory' => $mapSubcategoryByCategory,
            'mapProductBySubcategory' => $mapProductBySubcategory,
            'mapStockByProduct' => $mapStockByProduct,
            'warehouses' => $warehouses,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.stock-recap.index', $data);
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new StockRecapExport(), 'Stock_Recap_'.$fileDate.'.xlsx');
    }
}
