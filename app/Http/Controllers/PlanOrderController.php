<?php

namespace App\Http\Controllers;

use App\Exports\PlanOrderExport;
use App\Http\Requests\PlanOrderCancelRequest;
use App\Http\Requests\PlanOrderCreateRequest;
use App\Http\Requests\PlanOrderUpdateRequest;
use App\Models\Branch;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Constant;
use App\Utilities\Services\CommonService;
use App\Utilities\Services\NumberSettingService;
use App\Utilities\Services\PlanOrderService;
use App\Utilities\Services\ProductService;
use Barryvdh\DomPDF\Facade\Pdf;
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

        if($branches->count()) {
            $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_PLAN_ORDER, $branches->first()->id);
        }

        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'branches' => $branches,
            'suppliers' => $suppliers,
            'products' => $products,
            'number' => $number ?? '',
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.plan-order.create', $data);
    }

    public function store(PlanOrderCreateRequest $request) {
        try {
            DB::beginTransaction();

            $number = $request->get('number');
            if($request->get('is_generated_number')) {
                $number = NumberSettingService::generateNumber(Constant::NUMBER_SETTING_KEY_PLAN_ORDER, $request->get('branch_id'));
            }

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'number' => $number,
                'date' => $date,
                'user_id' => Auth::user()->id,
            ]);

            $planOrder = PlanOrder::create($request->all());

            PlanOrderService::createItemData($planOrder, $request);

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

    public function indexEdit(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? null;
        $finalDate = $filter->final_date ?? null;
        $number = $filter->number ?? null;
        $supplierId = $filter->supplier_id ?? null;

        if(!$number && !$supplierId && !$startDate && !$finalDate) {
            $startDate = Carbon::now()->format('d-m-Y');
            $finalDate = Carbon::now()->format('d-m-Y');
        }

        $suppliers = Supplier::all();
        $baseQuery = PlanOrderService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('plan_orders.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('plan_orders.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        if($number) {
            $baseQuery = $baseQuery->where('plan_orders.number', $number);
        }

        if($supplierId) {
            $baseQuery = $baseQuery->where('plan_orders.supplier_id', $supplierId);
        }

        $planOrders = $baseQuery
            ->orderByDesc('plan_orders.date')
            ->orderByDesc('plan_orders.id')
            ->get();

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'number' => $number,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'planOrders' => $planOrders,
        ];

        return view('pages.admin.plan-order.index-edit', $data);
    }

    public function edit(Request $request, $id) {
        $planOrder = PlanOrder::query()->findOrFail($id);
        $planOrderItems = $planOrder->planOrderItems;

        $products = Product::all();
        $rowNumbers = count($planOrderItems);

        $productIds = $planOrderItems->pluck('product_id')->toArray();
        $productConversions = ProductService::findProductConversions($productIds);

        foreach($planOrderItems as $planOrderItem) {
            $units[$planOrderItem->product_id][] = [
                'id' => $planOrderItem->product->unit_id,
                'name' => $planOrderItem->product->unit->name,
                'quantity' => 1
            ];
        }

        foreach($productConversions as $conversion) {
            $units[$conversion->product_id][] = [
                'id' => $conversion->unit_id,
                'name' => $conversion->unit->name,
                'quantity' => $conversion->quantity
            ];
        }

        $data = [
            'id' => $id,
            'planOrder' => $planOrder,
            'planOrderItems' => $planOrderItems,
            'products' => $products,
            'rowNumbers' => $rowNumbers,
            'units' => $units ?? [],
            'startDate' => $request->start_date ?? null,
            'finalDate' => $request->final_date ?? null,
            'number' => $request->number ?? null,
            'supplierId' => $request->supplier_id ?? null,
        ];

        return view('pages.admin.plan-order.edit', $data);
    }

    public function update(PlanOrderUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $planOrder = PlanOrder::query()->findOrFail($id);
            $planOrder->update([
                'status' => Constant::PLAN_ORDER_STATUS_UPDATED
            ]);

            PlanOrderService::createItemData($planOrder, $request, true);

            DB::commit();

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
                'number' => $request->get('order_number', null),
                'supplier_id' => $request->get('supplier_id', null),
            ];

            return redirect()->route('plan-orders.index-edit', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('plan-orders.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(PlanOrderCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $planOrder = PlanOrder::query()->findOrFail($id);
            $planOrder->update([
                'status' => Constant::PLAN_ORDER_STATUS_CANCELLED
            ]);

            DB::commit();

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
                'number' => $request->get('number', null),
                'supplier_id' => $request->get('supplier_id', null),
            ];

            return redirect()->route('plan-orders.index-edit', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
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

    public function indexPrintAjax(Request $request) {
        $filter = (object) $request->all();
        $isPrinted = $filter->is_printed;

        $baseQuery = PlanOrderService::getBaseQueryIndex();
        $baseQuery = $baseQuery
            ->addSelect(DB::raw('COUNT(plan_order_items.id) AS total_items'))
            ->leftJoin('plan_order_items', 'plan_order_items.plan_order_id', 'plan_orders.id');

        if($isPrinted) {
            $baseQuery = $baseQuery
                ->where('plan_orders.is_printed', 1)
                ->orderByDesc('plan_orders.date');
        } else {
            $baseQuery = $baseQuery
                ->where('plan_orders.is_printed', 0)
                ->orderBy('plan_orders.date');
        }

        $planOrders = $baseQuery
            ->groupBy('plan_orders.id')
            ->get();

        return response()->json([
            'data' => $planOrders,
        ]);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();

        $isPrinted = $filter->is_printed;
        $startNumber = $isPrinted ? $filter->start_number_printed : $filter->start_number;
        $finalNumber = $isPrinted ? $filter->final_number_printed : $filter->final_number;

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

        if($isPrinted) {
            $baseQuery = $baseQuery->where('plan_orders.is_printed', 1);
        } else {
            $baseQuery = $baseQuery->where('plan_orders.is_printed', 0);
        }

        $planOrders = $baseQuery->get();

        $itemsPerPage = 15;
        foreach ($planOrders as $planOrder) {
            CommonService::paginatePrintPages($planOrder, $planOrder->planOrderItems, $itemsPerPage);
        }

        $data = [
            'id' => $id,
            'planOrders' => $planOrders,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'itemsPerPage' => $itemsPerPage
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

        return Excel::download(new PlanOrderExport($request), 'Daftar_Plan_Order_'.$fileDate.'.xlsx');
    }

    public function pdf(Request $request) {
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

        $grandTotalItems = 0;
        foreach($planOrders as $planOrder) {
            $planOrder->total_items = $planOrder->planOrderItems->count();
            $grandTotalItems += $planOrder->total_items;
        }

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'planOrders' => $planOrders,
            'grandTotalItems' => $grandTotalItems,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.plan-order.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Daftar_Plan_Order_'.$fileDate.'.pdf');
    }

    public function generateNumberAjax(Request $request) {
        $filter = (object) $request->all();

        $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_PLAN_ORDER, $filter->branch_id);

        return response()->json([
            'number' => $number
        ]);
    }
}
