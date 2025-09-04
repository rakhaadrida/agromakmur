<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use App\Models\SalesOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutgoingItemController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(30)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $orderItems = SalesOrderItem::query()
            ->select(
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'warehouses.name AS warehouse_name',
                'units.name AS unit_name',
                DB::raw('SUM(sales_order_items.actual_quantity) AS total_quantity')
            )
            ->join('warehouses', 'warehouses.id', '=', 'sales_order_items.warehouse_id')
            ->join('products', 'products.id', '=', 'sales_order_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('sales_orders.status', '!=', 'CANCELLED')
            ->groupBy('customers.id', 'products.id', 'warehouses.id')
            ->orderBy('customers.name')
            ->orderBy('products.name')
            ->orderBy('warehouses.name')
            ->get();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'orderItems' => $orderItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.outgoing-item.index', $data);
    }
}
