<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductHistoryController extends Controller
{
    public function index(Request $request) {
        $products = GoodsReceiptItem::query()
            ->select(
                'goods_receipt_items.product_id AS product_id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'goods_receipts.date AS latest_date',
                'goods_receipts.number AS latest_number',
                'suppliers.name AS latest_supplier',
                'units.name AS latest_unit',
                'goods_receipt_items.quantity AS latest_quantity',
                'goods_receipt_items.price AS latest_price',
                'goods_receipt_items.total AS latest_total',
            )
            ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->joinSub(
                DB::table('goods_receipt_items')
                    ->select(
                        'goods_receipt_items.product_id',
                        DB::raw('MAX(goods_receipts.date) AS latest_date')
                    )
                    ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
                    ->where('goods_receipts.status', '!=', 'CANCELLED')
                    ->groupBy('goods_receipt_items.product_id'),
                'latest_items',
                function ($join) {
                    $join->on('goods_receipt_items.product_id', '=', 'latest_items.product_id')
                         ->on('goods_receipts.date', '=', 'latest_items.latest_date');
                })
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->where('goods_receipts.status', '!=', 'CANCELLED')
            ->orderBy('products.name')
            ->get();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'products' => $products,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.product-history.index', $data);
    }
}
