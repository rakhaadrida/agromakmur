<?php

namespace App\Http\Controllers;

use App\Exports\SalesOrderExport;
use App\Http\Requests\SalesOrderCancelRequest;
use App\Http\Requests\SalesOrderCreateRequest;
use App\Http\Requests\SalesOrderUpdateRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Marketing;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Notifications\CancelSalesOrderNotification;
use App\Notifications\UpdateSalesOrderNotification;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\CommonService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\NumberSettingService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\SalesOrderService;
use App\Utilities\Services\UserService;
use App\Utilities\Services\WarehouseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class SalesOrderController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = SalesOrderService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('sales_orders.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        $salesOrders = $baseQuery
            ->orderBy('sales_orders.date')
            ->get();

        $salesOrders = SalesOrderService::mapSalesOrderIndex($salesOrders, true);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'salesOrders' => $salesOrders
        ];

        return view('pages.admin.sales-order.index', $data);
    }

    public function detail($id) {
        $salesOrder = SalesOrder::query()->findOrFail($id);
        $salesOrder->revision = ApprovalService::getRevisionCountBySubject(SalesOrder::class, [$salesOrder->id]);
        $salesOrderItems = $salesOrder->salesOrderItems;

        if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)) {
            $salesOrder = SalesOrderService::mapSalesOrderApproval($salesOrder);
            $salesOrderItems = $salesOrder->salesOrderItems;
        }

        $productWarehouses = [];
        foreach($salesOrderItems as $salesOrderItem) {
            $productWarehouses[$salesOrderItem->product_id][$salesOrderItem->warehouse_id] = $salesOrderItem->quantity;
        }

        $salesOrderItems = SalesOrderService::mapSalesOrderItemDetail($salesOrderItems);

        $warehouses = WarehouseService::getGeneralWarehouse();

        $data = [
            'id' => $id,
            'salesOrder' => $salesOrder,
            'salesOrderItems' => $salesOrderItems,
            'productWarehouses' => $productWarehouses,
            'warehouses' => $warehouses,
            'totalWarehouses' => $warehouses->count(),
        ];

        return view('pages.admin.sales-order.detail', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');

        $branches = Branch::all();
        $customers = Customer::all();
        $marketings = Marketing::all();
        $products = Product::all();

        if($branches->count()) {
            $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_SALES_ORDER, $branches->first()->id);
        }

        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'branches' => $branches,
            'customers' => $customers,
            'marketings' => $marketings,
            'products' => $products,
            'number' => $number ?? '',
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.sales-order.create', $data);
    }

    public function store(SalesOrderCreateRequest $request) {
        try {
            DB::beginTransaction();

            $number = $request->get('number');
            if($request->get('is_generated_number')) {
                $number = NumberSettingService::generateNumber(Constant::NUMBER_SETTING_KEY_SALES_ORDER, $request->get('branch_id'));
            }

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $deliveryDate = $request->get('delivery_date');
            $deliveryDate = Carbon::createFromFormat('d-m-Y', $deliveryDate)->format('Y-m-d');

            $isTaxable = $request->get('is_taxable', 1);

            $request->merge([
                'number' => $number,
                'date' => $date,
                'delivery_date' => $deliveryDate,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'payment_amount' => (int) $request->get('payment_amount') ?? 0,
                'outstanding_amount' => 0,
                'status' => Constant::SALES_ORDER_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $salesOrder = SalesOrder::create($request->all());
            $deliveryOrder = DeliveryOrderService::createData($salesOrder);

            $productIds = $request->get('product_id', []);
            $unitIds = $request->get('unit_id', []);
            $realQuantities = $request->get('real_quantity', []);
            $prices = $request->get('price', []);
            $priceIds = $request->get('price_id', []);
            $warehouseIdsList = $request->get('warehouse_ids', []);
            $warehouseStocksList = $request->get('warehouse_stocks', []);

            $itemsData = collect($productIds)
                ->map(function ($productId, $index) use (
                    $unitIds, $realQuantities, $prices, $priceIds, $warehouseIdsList, $warehouseStocksList
                ) {
                    if (empty($productId)) return null;

                    return [
                        'product_id' => $productId,
                        'unit_id' => $unitIds[$index],
                        'real_quantity' => $realQuantities[$index],
                        'price' => $prices[$index],
                        'price_id' => $priceIds[$index],
                        'warehouse_ids' => explode(',', $warehouseIdsList[$index] ?? ''),
                        'warehouse_stocks' => explode(',', $warehouseStocksList[$index] ?? ''),
                    ];
                })
                ->filter();

            $subtotal = 0;
            foreach ($itemsData as $item) {
                foreach ($item['warehouse_ids'] as $key => $warehouseId) {
                    $quantity = $item['warehouse_stocks'][$key] ?? 0;
                    $actualQuantity = $quantity * $item['real_quantity'];
                    $total = $quantity * $item['price'];
                    $subtotal += $total;

                    $salesOrderItem = $salesOrder->salesOrderItems()->create([
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $warehouseId,
                        'unit_id' => $item['unit_id'],
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price_id' => $item['price_id'],
                        'price' => $item['price'],
                        'total' => $total,
                    ]);

                    $productStock = ProductService::getProductStockQuery($item['product_id'], $warehouseId);

                    ProductService::createProductStockLog(
                        $salesOrder->id,
                        $salesOrder->date,
                        $item['product_id'],
                        $warehouseId,
                        $productStock ? $productStock->stock : 0,
                        -$actualQuantity,
                        $salesOrder->branch_id,
                        null,
                        $total,
                        $salesOrder->customer_id
                    );

                    $productStock->decrement('stock', $actualQuantity);

                    DeliveryOrderService::createItemData($deliveryOrder, $salesOrderItem);
                }
            }

            $taxAmount = 0;
            if($isTaxable) {
                $taxAmount = round($subtotal * (10 / 100));
            }

            $grandTotal = (int) $subtotal + $taxAmount;
            $outstandingAmount = $grandTotal - $salesOrder->payment_amount;

            $salesOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'outstanding_amount' => $outstandingAmount,
                'delivery_status' => Constant::SALES_ORDER_DELIVERY_STATUS_COMPLETED
            ]);

            AccountReceivableService::createData($salesOrder);

            $parameters = [];
            $route = 'sales-orders.create';

            if($request->get('is_print')) {
                $route = 'sales-orders.print';
                $parameters = ['id' => $salesOrder->id];
            } else if($request->get('is_print_bill')) {
                $route = 'sales-orders.print-bill';
                $parameters = ['id' => $salesOrder->id];
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

    public function edit(Request $request, $id) {
        $salesOrder = SalesOrder::query()->findOrFail($id);
        $salesOrder->revision = ApprovalService::getRevisionCountBySubject(SalesOrder::class, [$salesOrder->id]);
        $salesOrderItems = $salesOrder->salesOrderItems;

        if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)) {
            $salesOrder = SalesOrderService::mapSalesOrderApproval($salesOrder);
            $salesOrderItems = $salesOrder->salesOrderItems;
        }

        $salesOrderItems = SalesOrderService::mapSalesOrderItemDetail($salesOrderItems);

        $branches = Branch::all();
        $customers = Customer::all();
        $marketings = Marketing::all();
        $products = Product::all();
        $rowNumbers = count($salesOrderItems);

        $productIds = $salesOrderItems->pluck('product_id')->toArray();
        $productConversions = ProductService::findProductConversions($productIds);
        $productPrices = ProductService::findProductPrices($productIds);

        foreach($salesOrderItems as $salesOrderItem) {
            $units[$salesOrderItem->product_id][] = [
                'id' => $salesOrderItem->product_unit_id,
                'name' => $salesOrderItem->product_unit_name,
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

        foreach($productPrices as $productPrice) {
            $prices[$productPrice->product_id][] = [
                'id' => $productPrice->price_id,
                'code' => $productPrice->pricing->code,
                'price' => $productPrice->price
            ];
        }

        $data = [
            'id' => $id,
            'salesOrder' => $salesOrder,
            'salesOrderItems' => $salesOrderItems,
            'branches' => $branches,
            'customers' => $customers,
            'marketings' => $marketings,
            'products' => $products,
            'rowNumbers' => $rowNumbers,
            'units' => $units ?? [],
            'prices' => $prices ?? [],
            'startDate' => $request->start_date ?? null,
            'finalDate' => $request->final_date ?? null,
        ];

        return view('pages.admin.sales-order.edit', $data);
    }

    public function update(SalesOrderUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $salesOrder = SalesOrder::query()->findOrFail($id);
            $salesOrder->update([
                'status' => Constant::SALES_ORDER_STATUS_WAITING_APPROVAL
            ]);

            $productIds = $request->get('product_id', []);
            $unitIds = $request->get('unit_id', []);
            $realQuantities = $request->get('real_quantity', []);
            $prices = $request->get('price', []);
            $priceIds = $request->get('price_id', []);
            $warehouseIdsList = $request->get('warehouse_ids', []);
            $warehouseStocksList = $request->get('warehouse_stocks', []);

            $itemsData = collect($productIds)
                ->map(function ($productId, $index) use (
                    $unitIds, $realQuantities, $prices, $priceIds, $warehouseIdsList, $warehouseStocksList
                ) {
                    if (empty($productId)) return null;

                    return [
                        'product_id' => $productId,
                        'unit_id' => $unitIds[$index],
                        'real_quantity' => $realQuantities[$index],
                        'price' => $prices[$index],
                        'price_id' => $priceIds[$index],
                        'warehouse_ids' => explode(',', $warehouseIdsList[$index] ?? ''),
                        'warehouse_stocks' => explode(',', $warehouseStocksList[$index] ?? ''),
                    ];
                })
                ->filter();

            ApprovalService::deleteData($salesOrder->pendingApprovals);

            $parentApproval = ApprovalService::createData(
                $salesOrder,
                $salesOrder->salesOrderItems,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            ApprovalService::createDataSalesOrder(
                $salesOrder,
                $itemsData,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $data['description'],
                $parentApproval->id,
                $data
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers($salesOrder->branch_id);

            foreach($users as $user) {
                $user->notify(new UpdateSalesOrderNotification($salesOrder->number, $parentApproval->id));
            }

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
            ];

            return redirect()->route('sales-orders.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('sales-orders.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(SalesOrderCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $salesOrder = SalesOrder::query()->findOrFail($id);
            $salesOrder->update([
                'status' => Constant::SALES_ORDER_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($salesOrder->pendingApprovals);
            $approval = ApprovalService::createData(
                $salesOrder,
                $salesOrder->salesOrderItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers($salesOrder->branch_id);

            foreach($users as $user) {
                $user->notify(new CancelSalesOrderNotification($salesOrder->number, $approval->id));
            }

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
            ];

            return redirect()->route('sales-orders.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexPrint() {
        $baseQuery = SalesOrderService::getBaseQueryIndex();

        $salesOrders = $baseQuery
            ->where('sales_orders.is_printed', 0)
            ->where('sales_orders.status', '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->orderBy('sales_orders.date')
            ->get();

        $data = [
            'salesOrders' => $salesOrders
        ];

        return view('pages.admin.sales-order.index-print', $data);
    }

    public function indexPrintAjax(Request $request) {
        $filter = (object) $request->all();
        $isPrinted = $filter->is_printed;

        $baseQuery = SalesOrderService::getBaseQueryIndex();

        if($isPrinted) {
            $baseQuery = $baseQuery
                ->where('sales_orders.is_printed', 1)
                ->orderByDesc('sales_orders.date')
                ->orderByDesc('sales_orders.id');
        } else {
            $baseQuery = $baseQuery
                ->where('sales_orders.is_printed', 0)
                ->orderBy('sales_orders.date');
        }

        $salesOrders = $baseQuery
            ->where('sales_orders.status', '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->get();

        return response()->json([
            'data' => $salesOrders,
        ]);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();

        $isPrinted = $filter->is_printed ?? 0;
        $startNumber = $isPrinted ? $filter->start_number_printed ?? 0 : $filter->start_number ?? 0;
        $finalNumber = $isPrinted ? $filter->final_number_printed ?? 0 : $filter->final_number ?? 0;
        $startOperator = $isPrinted ? '<=' : '>=';
        $finalOperator = $isPrinted ? '>=' : '<=';

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = SalesOrderService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('sales_orders.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('sales_orders.id', $startOperator, $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('sales_orders.id', $finalOperator, $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('sales_orders.id', $finalOperator, $startNumber);
            }
        }

        if($isPrinted) {
            $baseQuery = $baseQuery->where('sales_orders.is_printed', 1);
        } else {
            $baseQuery = $baseQuery->where('sales_orders.is_printed', 0);
        }

        $salesOrders = $baseQuery
            ->where('sales_orders.status', '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->get();

        $itemsPerPage = 15;
        foreach ($salesOrders as $salesOrder) {
            $salesOrder->salesOrderItems = SalesOrderService::mapSalesOrderItemDetail($salesOrder->salesOrderItems);

            CommonService::paginatePrintPages($salesOrder, $salesOrder->salesOrderItems, $itemsPerPage);
        }

        $data = [
            'id' => $id,
            'salesOrders' => $salesOrders,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'itemsPerPage' => $itemsPerPage
        ];

        return view('pages.admin.sales-order.print', $data);
    }

    public function printBill(Request $request, $id) {
        $salesOrder = SalesOrder::query()->findOrFail($id);
        $salesOrder->change_amount = $salesOrder->payment_amount - $salesOrder->grand_total;
        $salesOrder->total_quantity = $salesOrder->salesOrderItems->sum('quantity');

        if($salesOrder->change_amount < 0) {
            $salesOrder->change_amount = 0;
        }

        $printDate = Carbon::parse()->isoFormat('DD MMM Y');
        $printTime = Carbon::now()->format('H:i:s');

        $data = [
            'id' => $id,
            'salesOrder' => $salesOrder,
            'printDate' => $printDate,
            'printTime' => $printTime,
        ];

        return view('pages.admin.sales-order.print-bill', $data);
    }

    public function afterPrint(Request $request, $id) {
        try {
            DB::beginTransaction();

            $filter = (object) $request->all();
            $startNumber = $filter->start_number ?? 0;
            $finalNumber = $filter->final_number ?? 0;

            $baseQuery = SalesOrder::query();

            if($id) {
                $baseQuery = $baseQuery->where('sales_orders.id', $id);
            } else {
                if($startNumber) {
                    $baseQuery = $baseQuery->where('sales_orders.id', '>=', $startNumber);
                }

                if($finalNumber) {
                    $baseQuery = $baseQuery->where('sales_orders.id', '<=', $finalNumber);
                } else {
                    $baseQuery = $baseQuery->where('sales_orders.id', '<=', $startNumber);
                }
            }

            $salesOrders = $baseQuery
                ->where('sales_orders.is_printed', 0)
                ->where('sales_orders.status', '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
                ->get();

            foreach ($salesOrders as $salesOrder) {
                $salesOrder->update([
                    'is_printed' => 1,
                    'print_count' => $salesOrder->print_count + 1
                ]);
            }

            $route = $id ? 'sales-orders.create' : 'sales-orders.index-print';

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

        return Excel::download(new SalesOrderExport($request), 'Daftar_Sales_Order_'.$fileDate.'.xlsx');
    }

    public function pdf(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = SalesOrderService::getBaseQueryIndex();

        $salesOrders = $baseQuery
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderByDesc('sales_orders.date')
            ->get();

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'salesOrders' => $salesOrders,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.sales-order.pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Daftar_Sales_Order_'.$fileDate.'.pdf');
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $baseQuery = SalesOrderService::getBaseQueryIndex();
        $salesOrder = $baseQuery->findOrFail($filter->sales_order_id);
        $salesOrderItems = $salesOrder->salesOrderItems;

        if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)) {
            $salesOrder = SalesOrderService::mapSalesOrderApproval($salesOrder);
            $salesOrderItems = $salesOrder->salesOrderItems;
        }

        $salesOrderItems = SalesOrderService::mapSalesOrderItemDetail($salesOrderItems);
        $productIds = $salesOrderItems->pluck('product_id')->toArray();

        $deliveredQuantities = DeliveryOrderService::getDeliveryQuantityBySalesOrderProductIds($salesOrder->id, $productIds);
        $mapDeliveredQuantityByProductId = [];
        foreach($deliveredQuantities as $deliveredQuantity) {
            $mapDeliveredQuantityByProductId[$deliveredQuantity->product_id] = $deliveredQuantity->quantity;
        }

        foreach($salesOrderItems as $salesOrderItem) {
            $deliveredQuantity = $mapDeliveredQuantityByProductId[$salesOrderItem->product_id] ?? 0;
            $remainingQuantity = $salesOrderItem->quantity - $deliveredQuantity;

            $salesOrderItem->delivered_quantity = $deliveredQuantity;
            $salesOrderItem->remaining_quantity = $remainingQuantity;
        }

        return response()->json([
            'data' => $salesOrder,
            'sales_order_items' => $salesOrderItems,
        ]);
    }

    public function indexListAjax(Request $request) {
        $filter = (object) $request->all();

        $baseQuery = SalesOrderService::getBaseQueryIndex();

        if(!empty($filter->customer_id)) {
            $baseQuery = $baseQuery
                ->where('sales_orders.customer_id', $filter->customer_id);
        }

        $salesOrders = $baseQuery
            ->where('sales_orders.status', '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->orderByDesc('sales_orders.date')
            ->orderByDesc('sales_orders.id')
            ->get();

        return response()->json([
            'data' => $salesOrders,
        ]);
    }

    public function generateNumberAjax(Request $request) {
        $filter = (object) $request->all();

        $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_SALES_ORDER, $filter->branch_id);

        return response()->json([
            'number' => $number
        ]);
    }

    private function autoPrintBill($salesOrder) {
        $salesOrder->change_amount = $salesOrder->payment_amount - $salesOrder->grand_total;
        $salesOrder->total_quantity = $salesOrder->salesOrderItems->sum('quantity');
        $printDate = Carbon::parse()->isoFormat('DD MMM Y');
        $printTime = Carbon::now()->format('H:i:s');

        $connector = new WindowsPrintConnector("Microsoft Print to PDF");
        $printer   = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text("AGRO MAKMUR\n");
        $printer->setEmphasis(false);

        $printer->text($salesOrder->branch->address . "\n");
        $printer->text("No. Telp: " . $salesOrder->branch->phone_number . "\n");

        $printer->text("-------------------------------------------\n");

        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $this->printLeftRight(
            $printer,
            Carbon::now()->format($printDate . "\n" . $printTime),
            $salesOrder->user->username . "\n" . $salesOrder->customer->name . "\n" . $salesOrder->customer->address
        );

        $printer->text($salesOrder->number . "n");

        $printer->text("-------------------------------------------\n");

        foreach ($salesOrder->salesOrderItems as $index => $salesOrderItem) {
            $maxName = 40;
            $name = wordwrap($salesOrderItem->product->name, $maxName, "\n", true);
            $lines = explode("\n", $name);

            $printer->setEmphasis(true);
            $printer->text(($index + 1).". " . $lines[0] . "\n");
            $printer->setEmphasis(false);

            for ($i = 1; $i < count($lines); $i++) {
                $printer->text("   " . $lines[$i] . "\n");
            }

            $left  = $salesOrderItem->quantity . " " . $salesOrderItem->unit->name . " x " . formatPrice($salesOrderItem->price);
            $right = "Rp " . formatPrice($salesOrderItem->total);

            $printer->text(
                str_pad($left, 26) .
                str_pad($right, 16, ' ', STR_PAD_LEFT) . "\n"
            );
        }

        $printer->text("-------------------------------------------\n");

        $printer->text("Total QTY : " . $salesOrder->total_quantity ."\n");

        $this->printTotalLine($printer, 'Sub Total', 'Rp ' . $salesOrder->subtotal);

        if($salesOrder->is_taxable) {
            $this->printTotalLine($printer, 'PPN', 'Rp ' . $salesOrder->tax_amount);
        }

        $this->printTotalLine($printer, 'TOTAL', 'Rp ' . $salesOrder->grand_total, 42, true);
        $this->printTotalLine($printer, 'Pembayaran', 'Rp ' . $salesOrder->payment_amount);
        $this->printTotalLine($printer, 'Kembali', 'Rp ' . $salesOrder->change_amount);

        $printer->feed();

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Terimakasih Telah Berbelanja\n");
        $printer->feed();

        $printer->cut();
        $printer->close();
    }

    private function printLeftRight($printer, $left, $right, $width = 42)
    {
        $leftWidth  = floor($width / 2);
        $rightWidth = $width - $leftWidth;

        $leftLines  = explode("\n", wordwrap($left,  $leftWidth, "\n", true));
        $rightLines = explode("\n", wordwrap($right, $rightWidth, "\n", true));

        $max = max(count($leftLines), count($rightLines));

        for ($i = 0; $i < $max; $i++) {
            $l = $leftLines[$i]  ?? '';
            $r = $rightLines[$i] ?? '';

            $printer->text(
                str_pad($l, $leftWidth) .
                str_pad($r, $rightWidth, ' ', STR_PAD_LEFT) . "\n"
            );
        }
    }

    private function printTotalLine($printer, $label, $value, $width = 42, $bold = false)
    {
        if ($bold) {
            $printer->setEmphasis(true);
        }

        $printer->text(
            str_pad($label, 21) .
            str_pad($value, 21, ' ', STR_PAD_LEFT) . "\n"
        );

        if ($bold) {
            $printer->setEmphasis(false);
        }
    }
}
