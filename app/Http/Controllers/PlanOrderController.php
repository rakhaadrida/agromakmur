<?php

namespace App\Http\Controllers;

use App\Exports\PlanOrderExport;
use App\Http\Requests\PlanOrderCreateRequest;
use App\Models\Branch;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Services\PlanOrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PlanOrderController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = PlanOrderService::getBaseQueryIndex();

        $planOrders = $baseQuery
            ->where('plan_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('plan_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderByDesc('plan_orders.date')
            ->get();

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'planOrders' => $planOrders
        ];

        return view('pages.admin.plan-order.index', $data);
    }

    public function detail($id) {
        $planOrder = PlanOrder::query()->findOrFail($id);
        $planOrderItems = $planOrder->planOrderItems;

        $data = [
            'id' => $id,
            'planOrder' => $planOrder,
            'planOrderItems' => $planOrderItems,
        ];

        return view('pages.admin.plan-order.detail', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');

        $branches = Branch::all();
        $suppliers = Supplier::all();
        $products = Product::all();

        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'branches' => $branches,
            'suppliers' => $suppliers,
            'products' => $products,
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.plan-order.create', $data);
    }

    public function store(PlanOrderCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'date' => $date,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'user_id' => Auth::user()->id,
            ]);

            $planOrder = PlanOrder::create($request->all());

            $subtotal = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                if(!empty($productId)) {
                    $unitId = $request->get('unit_id')[$index];
                    $quantity = $request->get('quantity')[$index];
                    $realQuantity = $request->get('real_quantity')[$index];
                    $price = $request->get('price')[$index];

                    $actualQuantity = $quantity * $realQuantity;
                    $total = $quantity * $price;
                    $subtotal += $total;

                    $planOrder->planOrderItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price' => $price,
                        'total' => $total
                    ]);
                }
            }

            $taxAmount = $subtotal * (10 / 100);
            $grandTotal = $subtotal + $taxAmount;

            $planOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal
            ]);

            $parameters = [];
            $route = 'plan-orders.create';

            if($request->get('is_print')) {
                $route = 'plan-orders.print';
                $parameters = ['id' => $planOrder->id];
            }

            DB::commit();

            return redirect()->route($route, $parameters);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function indexPrint() {
        $baseQuery = PlanOrderService::getBaseQueryIndex();

        $planOrders = $baseQuery
            ->where('plan_orders.is_printed', 0)
            ->orderBy('plan_orders.date')
            ->get();

        $data = [
            'planOrders' => $planOrders
        ];

        return view('pages.admin.plan-order.index-print', $data);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();
        $startNumber = $filter->start_number ?? 0;
        $finalNumber = $filter->final_number ?? 0;

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = PlanOrderService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('plan_orders.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('plan_orders.id', '>=', $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('plan_orders.id', '<=', $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('plan_orders.id', '<=', $startNumber);
            }
        }

        $planOrders = $baseQuery
            ->where('plan_orders.is_printed', 0)
            ->get();

        foreach ($planOrders as $planOrder) {
            $totalPage = ceil(($planOrder->planOrderItems->count()) / 15);
            $planOrder->total_page = $totalPage;
            $planOrder->total_rows = $planOrder->planOrderItems->count();
        }

        $data = [
            'id' => $id,
            'planOrders' => $planOrders,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'rowNumbers' => 35
        ];

        return view('pages.admin.plan-order.print', $data);
    }

    public function afterPrint(Request $request, $id) {
        try {
            DB::beginTransaction();

            $filter = (object) $request->all();
            $startNumber = $filter->start_number ?? 0;
            $finalNumber = $filter->final_number ?? 0;

            $baseQuery = PlanOrder::query();

            if($id) {
                $baseQuery = $baseQuery->where('plan_orders.id', $id);
            } else {
                if($startNumber) {
                    $baseQuery = $baseQuery->where('plan_orders.id', '>=', $startNumber);
                }

                if($finalNumber) {
                    $baseQuery = $baseQuery->where('plan_orders.id', '<=', $finalNumber);
                } else {
                    $baseQuery = $baseQuery->where('plan_orders.id', '<=', $startNumber);
                }
            }

            $planOrders = $baseQuery
                ->where('plan_orders.is_printed', 0)
                ->get();

            foreach ($planOrders as $planOrder) {
                $planOrder->update([
                    'is_printed' => 1,
                    'print_count' => $planOrder->print_count + 1
                ]);
            }

            $route = $id ? 'plan-orders.create' : 'plan-orders.index-print';

            DB::commit();

            return redirect()->route($route);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new PlanOrderExport($request), 'Plan_Order_Data_'.$fileDate.'.xlsx');
    }
}
