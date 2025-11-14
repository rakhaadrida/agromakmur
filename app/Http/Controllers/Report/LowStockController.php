<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Services\BranchService;
use App\Utilities\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LowStockController extends Controller
{
    public function index(Request $request) {
        $branchIds = UserService::findBranchIdsByUserId(Auth::id());
        $warehouseIds = BranchService::findWarehouseIdsByBranchIds($branchIds);

        $products = Product::query()
            ->select(
                'products.id AS id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'categories.name AS category_name',
                'subcategories.name AS subcategory_name',
                'subcategories.reminder_limit AS stock_limit',
                'units.name AS unit_name',
                'product_stocks.current_stock AS current_stock'
            )
            ->joinSub(
                DB::table('product_stocks')
                    ->select(
                        'product_stocks.product_id',
                        DB::raw('SUM(product_stocks.stock) AS current_stock')
                    )
                    ->when(!isUserSuperAdmin(), function ($q) use ($warehouseIds) {
                        $q->whereIn('product_stocks.warehouse_id', $warehouseIds);
                    })
                    ->whereNull('product_stocks.deleted_at')
                    ->groupBy('product_stocks.product_id'),
                'product_stocks',
                'products.id',
                'product_stocks.product_id'
                )
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('subcategories', 'subcategories.id', '=', 'products.subcategory_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->whereColumn('product_stocks.current_stock', '<=', 'subcategories.reminder_limit')
            ->orderBy('product_stocks.current_stock')
            ->get();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'products' => $products,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.low-stock.index', $data);
    }
}
