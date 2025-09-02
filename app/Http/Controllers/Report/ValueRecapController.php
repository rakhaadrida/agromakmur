<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Warehouse;
use App\Utilities\Services\ProductService;
use Carbon\Carbon;

class ValueRecapController extends Controller
{
    public function index() {
        $categories = Category::all();
        $subcategories = Subcategory::all();

        $mapSubcategoryByCategory = [];
        foreach($subcategories as $subcategory) {
            $mapSubcategoryByCategory[$subcategory->category_id][] = $subcategory;
        }

        $productStocks = ProductService::getTotalProductStock();
        $mapStockByProduct = [];
        foreach($productStocks as $productStock) {
            $mapStockByProduct[$productStock->product_id] = $productStock->total_stock;
        }

        $products = Product::all();
        $mapProductBySubcategory = [];
        foreach($products as $product) {
            $productPrice = $product->mainPrice ? $product->mainPrice->price : 0;
            $totalValue = $productPrice * ($mapStockByProduct[$product->id] ?? 0);

            $product->price = $productPrice;
            $product->total_value = $totalValue;

            $mapProductBySubcategory[$product->subcategory_id][] = $product;
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

        return view('pages.admin.report.value-recap.index', $data);
    }
}
