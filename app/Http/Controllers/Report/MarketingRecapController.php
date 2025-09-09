<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GoodsReceiptItem;
use App\Models\Marketing;
use App\Models\SalesOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingRecapController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(30)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $marketingId = $filter->marketing_id ?? null;
        $categoryId = $filter->category_id ?? null;

        $marketings = Marketing::all();
        $categories = Category::all();

        $marketingItems = $marketingId ? Marketing::where('id', $marketingId)->get() : $marketings;

        $baseQuery = SalesOrderItem::query()
            ->select(
                'marketings.id AS marketing_id',
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'units.name AS unit_name',
                'categories.name AS category_name',
                DB::raw('SUM(sales_order_items.actual_quantity) AS total_quantity')
            )
            ->join('products', 'products.id', '=', 'sales_order_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->join('marketings', 'marketings.id', '=', 'sales_orders.marketing_id')
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('sales_orders.status', '!=', 'CANCELLED');

        if($marketingId) {
            $baseQuery = $baseQuery->where('sales_orders.marketing_id', $marketingId);
        }

        if($categoryId) {
            $baseQuery = $baseQuery->where('products.category_id', $categoryId);
        }

        $orderItems = $baseQuery
            ->groupBy( 'marketings.id', 'customers.id', 'products.id')
            ->orderBy('customers.name')
            ->orderBy('products.name')
            ->orderBy('categories.name')
            ->get();

        $mapSalesOrderByMarketing = [];
        $mapTotalQuantityByMarketing = [];
        foreach($orderItems as $orderItem) {
            $mapSalesOrderByMarketing[$orderItem->marketing_id][] = $orderItem;

            if(!isset($mapTotalQuantityByMarketing[$orderItem->marketing_id])) {
                $mapTotalQuantityByMarketing[$orderItem->marketing_id] = 0;
            }

            $mapTotalQuantityByMarketing[$orderItem->marketing_id] += $orderItem->total_quantity;
        }

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'marketingId' => $marketingId,
            'categoryId' => $categoryId,
            'marketingItems' => $marketingItems,
            'marketings' => $marketings,
            'categories' => $categories,
            'mapSalesOrderByMarketing' => $mapSalesOrderByMarketing,
            'mapTotalQuantityByMarketing' => $mapTotalQuantityByMarketing,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.marketing-recap.index', $data);
    }
}
