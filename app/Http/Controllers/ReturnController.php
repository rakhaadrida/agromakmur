<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Utilities\Constant;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index() {
        $products = Product::query()
            ->select(
                'products.*',
                DB::raw('SUM(product_stocks.stock) AS stock')
            )
            ->leftJoin('product_stocks', 'product_stocks.product_id', 'products.id')
            ->leftJoin('warehouses', 'warehouses.id', 'product_stocks.warehouse_id')
            ->where('warehouses.type', Constant::WAREHOUSE_TYPE_RETURN)
            ->whereNull('product_stocks.deleted_at')
            ->whereNull('warehouses.deleted_at')
            ->groupBy('products.id')
            ->get();

        $data = [
            'products' => $products
        ];

        return view('pages.admin.return.index', $data);
    }
}
