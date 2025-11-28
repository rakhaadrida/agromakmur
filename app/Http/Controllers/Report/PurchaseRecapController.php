<?php

namespace App\Http\Controllers\Report;

use App\Exports\PurchaseRecapDetailExport;
use App\Exports\PurchaseRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Services\PurchaseRecapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseRecapController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $purchaseItems = PurchaseRecapService::getBaseQueryProductIndex($startDate, $finalDate);

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'purchaseItems' => $purchaseItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.purchase-recap.index', $data);
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $subject = $filter->subject ?? null;

        switch ($subject) {
            case 'products':
                $purchaseItems = PurchaseRecapService::getBaseQueryProductIndex($startDate, $finalDate);
                break;
            case 'suppliers':
                $purchaseItems = PurchaseRecapService::getBaseQuerySupplierIndex($startDate, $finalDate);
                break;
            default:
                return response()->json([
                    'message' => 'Invalid subject type'
                ], 400);
        }

        return response()->json([
            'data' => $purchaseItems ?? [],
        ]);
    }

    public function show(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $subject = $filter->subject ?? null;
        $supplierId = $filter->supplier_id ?? null;
        $productId = $filter->product_id ?? null;

        $suppliers = Supplier::all();
        $products = Product::all();

        $purchaseItems = [];
        if($subject == 'products') {
            $purchaseItems = PurchaseRecapService::getBaseQueryProductDetail($id, $startDate, $finalDate, $supplierId);
            $item = Product::query()->find($id);
        } elseif ($subject == 'suppliers') {
            $purchaseItems = PurchaseRecapService::getBaseQuerySupplierDetail($id, $startDate, $finalDate, $productId);
            $item = Supplier::query()->find($id);
        }

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'id' => $id,
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'subject' => $subject,
            'supplierId' => $supplierId,
            'productId' => $productId,
            'suppliers' => $suppliers ?? [],
            'products' => $products ?? [],
            'purchaseItems' => $purchaseItems,
            'item' => $item ?? null,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.purchase-recap.detail', $data);
    }

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new PurchaseRecapExport($request), 'Rekap_Pembelian_'.$fileDate.'.xlsx');
    }

    public function exportDetail(Request $request, $id) {
        $subjectName = 'Detail';

        if(isSubjectProduct($request->subject)) {
            $product = Product::query()->findOrFail($id);
            $subjectName = preg_replace('/\s+/', '_', $product->name);
        } else if(isSubjectSupplier($request->subject)) {
            $supplier = Supplier::query()->findOrFail($id);
            $subjectName = preg_replace('/\s+/', '_', $supplier->name);
        }

        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new PurchaseRecapDetailExport($id, $request), 'Rekap_Pembelian_'.$subjectName.'_'.$fileDate.'.xlsx');
    }
}
