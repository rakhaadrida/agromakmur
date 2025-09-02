<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Subcategory;
use Carbon\Carbon;

class PriceListController extends Controller
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

        $productPrices = ProductPrice::query()->whereNull('deleted_at')->get();
        $mapPriceByProduct = [];
        foreach($productPrices as $productPrice) {
            $mapPriceByProduct[$productPrice->product_id][$productPrice->price_id] = $productPrice->price;
        }

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
}
