<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Utilities\Services\SalesRecapService;
use App\Utilities\Services\StockCardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $productId = $filter->product_id ?? null;

        $products = Product::all();

        if($productId) {
            $product = Product::query()->findOrFail($productId);
            $stockLogs = StockCardService::getBaseQueryProductIndex($startDate, $finalDate, $productId);

            $initialStock = $stockLogs->first() ? $stockLogs->first()->initial_stock : 0;

            $totalIncomingQuantity = $stockLogs->where('quantity', '>=', 0)->sum('quantity');
            $totalOutgoingQuantity = $stockLogs->where('quantity', '<', 0)->sum('quantity');
        }

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'productId' => $productId,
            'products' => $products ?? [],
            'product' => $product ?? null,
            'stockLogs' => $stockLogs ?? collect([]),
            'initialStock' => $initialStock ?? 0,
            'totalIncomingQuantity' => $totalIncomingQuantity ?? 0,
            'totalOutgoingQuantity' => $totalOutgoingQuantity ?? 0,
        ];

        return view('pages.admin.report.stock-card.index', $data);
    }

    public function show(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $subject = $filter->subject ?? null;
        $customerId = $filter->customer_id ?? null;
        $productId = $filter->product_id ?? null;

        $customers = Customer::all();
        $products = Product::all();

        $salesItems = [];
        if($subject == 'products') {
            $salesItems = SalesRecapService::getBaseQueryProductDetail($id, $startDate, $finalDate, $customerId);
            $item = Product::query()->find($id);
        } elseif ($subject == 'customers') {
            $salesItems = SalesRecapService::getBaseQueryCustomerDetail($id, $startDate, $finalDate, $productId);
            $item = Customer::query()->find($id);
        }

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'id' => $id,
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'subject' => $subject,
            'customerId' => $customerId,
            'productId' => $productId,
            'customers' => $customers ?? [],
            'products' => $products ?? [],
            'salesItems' => $salesItems,
            'item' => $item ?? null,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.sales-recap.detail', $data);
    }
}
