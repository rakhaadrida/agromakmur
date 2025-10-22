<?php

namespace App\Http\Controllers\Report;

use App\Exports\StockCardExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Utilities\Services\StockCardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new StockCardExport($request), 'Stock_Card_'.$fileDate.'.xlsx');
    }
}
