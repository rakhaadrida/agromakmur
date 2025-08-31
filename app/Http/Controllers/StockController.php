<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Models\Warehouse;

class StockController extends Controller
{
    public function index() {
        $products = Product::query()
            ->select(
                'products.*',
                'categories.name AS category_name',
                'subcategories.name AS subcategory_name'
            )
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('subcategories', 'subcategories.id', 'products.subcategory_id')
            ->get();

        $warehouses = Warehouse::all();
        $productStocks = ProductStock::query()
            ->whereNull('deleted_at')
            ->get();

        $mapStockByProductWarehouse = [];
        foreach($productStocks as $productStock) {
            $mapStockByProductWarehouse[$productStock->product_id][$productStock->warehouse_id] = $productStock->stock;
        }

        $data = [
            'products' => $products,
            'warehouses' => $warehouses,
            'mapStockByProductWarehouse' => $mapStockByProductWarehouse
        ];

        return view('pages.warehouse.stock.index', $data);
    }

    public function show($id) {
        $product = Product::query()->findOrFail($id);

        $warehouses = Warehouse::all();
        $productStocks = $product->productStocks->mapWithKeys(function($productStock) {
            $array = [];

            $array[$productStock->warehouse_id] = [
                'product_id' => $productStock->product_id,
                'stock' => $productStock->stock,
            ];

            return $array;
        });

        $productStocks = $productStocks->toArray();

        $data = [
            'id' => $id,
            'product' => $product,
            'warehouses' => $warehouses,
            'productStocks' => $productStocks
        ];

        return view('pages.warehouse.stock.detail', $data);
    }
}
