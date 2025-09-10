<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptItem;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductHistoryController extends Controller
{
    public function index(Request $request) {
        $products = Product::query()
            ->select(
                'products.id AS product_id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'goods_receipts.id AS latest_id',
                'goods_receipts.date AS latest_date',
                'goods_receipts.number AS latest_number',
                'suppliers.name AS latest_supplier',
                'units.name AS latest_unit',
                'goods_receipt_items.quantity AS latest_quantity',
                'goods_receipt_items.price AS latest_price'
            )
            ->joinSub(
                DB::table('goods_receipt_items')
                    ->select(
                        'goods_receipt_items.product_id',
                        DB::raw('MAX(goods_receipts.date) AS latest_date'),
                        DB::raw('MAX(goods_receipts.id) AS latest_id')
                    )
                    ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
                    ->where('goods_receipts.status', '!=', 'CANCELLED')
                    ->whereNull('goods_receipt_items.deleted_at')
                    ->whereNull('goods_receipts.deleted_at')
                    ->groupBy('goods_receipt_items.product_id'),
                'latest_items',
                'products.id',
                'latest_items.product_id'
                )
            ->join('goods_receipt_items', function ($join) {
                $join->on('goods_receipt_items.product_id', '=', 'products.id')
                     ->on('goods_receipt_items.goods_receipt_id', '=', 'latest_items.latest_id')
                     ->whereNull('goods_receipt_items.deleted_at');
            })
            ->join('goods_receipts', 'goods_receipts.id', '=', 'latest_items.latest_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->orderBy('products.name')
            ->get();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'products' => $products,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.product-history.index', $data);
    }

    public function show(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $supplierId = $filter->supplier_id ?? null;

        $suppliers = Supplier::all();
        $product = Product::query()->findOrFail($id);

        $baseQuery = GoodsReceiptItem::query()
            ->select(
                'goods_receipts.id AS receipt_id',
                'goods_receipts.date AS receipt_date',
                'goods_receipts.number AS receipt_number',
                'suppliers.id AS supplier_id',
                'suppliers.name AS supplier_name',
                'units.name AS unit_name',
                'goods_receipt_items.quantity AS quantity',
                'goods_receipt_items.price AS price',
                'goods_receipt_items.wages AS wages',
                'goods_receipt_items.shipping_cost AS shipping_cost',
                'goods_receipt_items.total AS total',
            )
            ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->join('units', 'units.id', '=', 'goods_receipt_items.unit_id')
            ->where('products.id', $product->id)
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereNull('goods_receipt_items.deleted_at')
            ->whereNull('goods_receipts.deleted_at');


        if($supplierId) {
            $baseQuery = $baseQuery->where('goods_receipts.supplier_id', $supplierId);
        }

        $receiptItems = $baseQuery
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'product' => $product,
            'receiptItems' => $receiptItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.product-history.detail', $data);
    }
}
