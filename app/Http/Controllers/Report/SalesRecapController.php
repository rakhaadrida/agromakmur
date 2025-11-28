<?php

namespace App\Http\Controllers\Report;

use App\Exports\SalesRecapDetailExport;
use App\Exports\SalesRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Utilities\Services\SalesRecapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SalesRecapController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $salesItems = SalesRecapService::getBaseQueryProductIndex($startDate, $finalDate);

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'salesItems' => $salesItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.sales-recap.index', $data);
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $subject = $filter->subject ?? null;

        switch ($subject) {
            case 'products':
                $salesItems = SalesRecapService::getBaseQueryProductIndex($startDate, $finalDate);
                break;
            case 'customers':
                $salesItems = SalesRecapService::getBaseQueryCustomerIndex($startDate, $finalDate);
                break;
            default:
                return response()->json([
                    'message' => 'Invalid subject type'
                ], 400);
        }

        return response()->json([
            'data' => $salesItems ?? [],
        ]);
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

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new SalesRecapExport($request), 'Rekap_Penjualan_'.$fileDate.'.xlsx');
    }

    public function exportDetail(Request $request, $id) {
        $subjectName = 'Detail';

        if(isSubjectProduct($request->subject)) {
            $product = Product::query()->findOrFail($id);
            $subjectName = preg_replace('/\s+/', '_', $product->name);
        } else if(isSubjectCustomer($request->subject)) {
            $customer = Customer::query()->findOrFail($id);
            $subjectName = preg_replace('/\s+/', '_', $customer->name);
        }

        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new SalesRecapDetailExport($id, $request), 'Rekap_Penjualan_'.$subjectName.'_'.$fileDate.'.xlsx');
    }
}
