<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingItemController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(30)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $receiptItems = GoodsReceiptItem::query()
            ->select(
                'suppliers.id AS supplier_id',
                'suppliers.name AS supplier_name',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'warehouses.name AS warehouse_name',
                DB::raw('SUM(goods_receipt_items.quantity) AS total_quantity')
            )
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('warehouses', 'warehouses.id', '=', 'goods_receipts.warehouse_id')
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('goods_receipts.status', '!=', 'CANCELLED')
            ->groupBy('suppliers.id', 'products.id', 'warehouses.id')
            ->orderBy('suppliers.name')
            ->orderBy('products.name')
            ->orderBy('warehouses.name')
            ->get();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'receiptItems' => $receiptItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.incoming-item.index', $data);
    }
}
