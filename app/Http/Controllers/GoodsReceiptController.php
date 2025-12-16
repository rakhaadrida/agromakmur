<?php

namespace App\Http\Controllers;

use App\Exports\GoodsReceiptExport;
use App\Http\Requests\GoodsReceiptCancelRequest;
use App\Http\Requests\GoodsReceiptCreateRequest;
use App\Http\Requests\GoodsReceiptUpdateRequest;
use App\Models\Branch;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Notifications\CancelGoodsReceiptNotification;
use App\Notifications\UpdateGoodsReceiptNotification;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\CommonService;
use App\Utilities\Services\GoodsReceiptService;
use App\Utilities\Services\NumberSettingService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class GoodsReceiptController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderByDesc('goods_receipts.date')
            ->get();

        $goodsReceipts = GoodsReceiptService::mapGoodsReceiptIndex($goodsReceipts);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'goodsReceipts' => $goodsReceipts
        ];

        return view('pages.admin.goods-receipt.index', $data);
    }

    public function detail($id) {
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $goodsReceipt->revision = ApprovalService::getRevisionCountBySubject(GoodsReceipt::class, [$goodsReceipt->id]);
        $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;

        if(isWaitingApproval($goodsReceipt->status) && isApprovalTypeEdit($goodsReceipt->pendingApproval->type)) {
            $goodsReceipt = GoodsReceiptService::mapGoodsReceiptApproval($goodsReceipt);

            $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;
        }

        $data = [
            'id' => $id,
            'goodsReceipt' => $goodsReceipt,
            'goodsReceiptItems' => $goodsReceiptItems,
        ];

        return view('pages.admin.goods-receipt.detail', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');

        $branches = Branch::all();
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::all();

        if($branches->count()) {
            $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_GOODS_RECEIPT, $branches->first()->id);
        }

        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'branches' => $branches,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'products' => $products,
            'number' => $number ?? '',
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.goods-receipt.create', $data);
    }

    public function store(GoodsReceiptCreateRequest $request) {
        try {
            DB::beginTransaction();

            $number = $request->get('number');
            if($request->get('is_generated_number')) {
                $number = NumberSettingService::generateNumber(Constant::NUMBER_SETTING_KEY_GOODS_RECEIPT, $request->get('branch_id'));
            }

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'number' => $number,
                'date' => $date,
                'tempo' => $request->get('tempo') ?? 0,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'payment_amount' => (int) $request->get('payment_amount') ?? 0,
                'outstanding_amount' => 0,
                'status' => Constant::GOODS_RECEIPT_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $goodsReceipt = GoodsReceipt::create($request->all());

            $subtotal = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                if(!empty($productId)) {
                    $unitId = $request->get('unit_id')[$index];
                    $quantity = $request->get('quantity')[$index];
                    $realQuantity = $request->get('real_quantity')[$index];
                    $price = $request->get('price')[$index];
                    $wages = $request->get('wages')[$index];
                    $shippingCost = $request->get('shipping_cost')[$index];

                    $actualQuantity = $quantity * $realQuantity;
                    $totalExpenses = $wages + $shippingCost;
                    $totalCostPrice = $price + $totalExpenses;
                    $total = $quantity * $totalCostPrice;
                    $subtotal += ($price * $quantity);

                    $goodsReceipt->goodsReceiptItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price' => $price,
                        'wages' => $wages,
                        'shipping_cost' => $shippingCost,
                        'cost_price' => $totalCostPrice,
                        'total' => $total
                    ]);

                    ProductService::updateProductPrice($productId, $price);

                    $productStock = ProductService::getProductStockQuery(
                        $productId,
                        $goodsReceipt->warehouse_id
                    );

                    ProductService::updateProductStockIncrement(
                        $productId,
                        $productStock,
                        $actualQuantity,
                        $goodsReceipt->id,
                        $goodsReceipt->date,
                        $goodsReceipt->warehouse_id,
                        $goodsReceipt->supplier_id,
                        $goodsReceipt->branch_id,
                        $total
                    );
                }
            }

            $taxAmount = $subtotal * (10 / 100);
            $grandTotal = $subtotal + $taxAmount;
            $outstandingAmount = $grandTotal - $goodsReceipt->payment_amount;

            $goodsReceipt->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'outstanding_amount' => $outstandingAmount
            ]);

            AccountPayableService::createData($goodsReceipt);

            $parameters = [];
            $route = 'goods-receipts.create';

            if($request->get('is_print')) {
                $route = 'goods-receipts.print';
                $parameters = ['id' => $goodsReceipt->id];
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
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $goodsReceipt->revision = ApprovalService::getRevisionCountBySubject(GoodsReceipt::class, [$goodsReceipt->id]);
        $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;

        if(isWaitingApproval($goodsReceipt->status) && isApprovalTypeEdit($goodsReceipt->pendingApproval->type)) {
            $goodsReceipt = GoodsReceiptService::mapGoodsReceiptApproval($goodsReceipt);
            $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;
        }

        $products = Product::all();
        $rowNumbers = count($goodsReceiptItems);

        $productIds = $goodsReceiptItems->pluck('product_id')->toArray();
        $productConversions = ProductService::findProductConversions($productIds);

        foreach($goodsReceiptItems as $goodsReceiptItem) {
            $units[$goodsReceiptItem->product_id][] = [
                'id' => $goodsReceiptItem->product->unit_id,
                'name' => $goodsReceiptItem->product->unit->name,
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
            'goodsReceipt' => $goodsReceipt,
            'goodsReceiptItems' => $goodsReceiptItems,
            'products' => $products,
            'rowNumbers' => $rowNumbers,
            'units' => $units ?? [],
            'startDate' => $request->start_date ?? null,
            'finalDate' => $request->final_date ?? null,
        ];

        return view('pages.admin.goods-receipt.edit', $data);
    }

    public function update(GoodsReceiptUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
            $goodsReceipt->update([
                'status' => Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($goodsReceipt->pendingApprovals);

            $parentApproval = ApprovalService::createData(
                $goodsReceipt,
                $goodsReceipt->goodsReceiptItems,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            ApprovalService::createData(
                $goodsReceipt,
                $data,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $data['description'],
                $parentApproval->id
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers();

            foreach($users as $user) {
                $user->notify(new UpdateGoodsReceiptNotification($goodsReceipt->number, $parentApproval->id));
            }

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
            ];

            return redirect()->route('goods-receipts.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('goods-receipts.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(GoodsReceiptCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
            $goodsReceipt->update([
                'status' => Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($goodsReceipt->pendingApprovals);
            $approval = ApprovalService::createData(
                $goodsReceipt,
                $goodsReceipt->goodsReceiptItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers();

            foreach($users as $user) {
                $user->notify(new CancelGoodsReceiptNotification($goodsReceipt->number, $approval->id));
            }

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
            ];

            return redirect()->route('goods-receipts.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexPrint() {
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.is_printed', 0)
            ->where('goods_receipts.status', '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->orderBy('goods_receipts.date')
            ->get();

        $data = [
            'goodsReceipts' => $goodsReceipts
        ];

        return view('pages.admin.goods-receipt.index-print', $data);
    }

    public function indexPrintAjax(Request $request) {
        $filter = (object) $request->all();
        $isPrinted = $filter->is_printed;

        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        if($isPrinted) {
            $baseQuery = $baseQuery
                ->where('goods_receipts.is_printed', 1)
                ->orderByDesc('goods_receipts.date')
                ->orderByDesc('goods_receipts.id');
        } else {
            $baseQuery = $baseQuery
                ->where('goods_receipts.is_printed', 0)
                ->orderBy('goods_receipts.date');
        }

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.status', '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->get();

        return response()->json([
            'data' => $goodsReceipts,
        ]);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();

        $isPrinted = $filter->is_printed;
        $startNumber = $isPrinted ? $filter->start_number_printed : $filter->start_number;
        $finalNumber = $isPrinted ? $filter->final_number_printed : $filter->final_number ?? 0;
        $startOperator = $isPrinted ? '<=' : '>=';
        $finalOperator = $isPrinted ? '>=' : '<=';

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('goods_receipts.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('goods_receipts.id', $startOperator, $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('goods_receipts.id', $finalOperator, $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('goods_receipts.id', $finalOperator, $startNumber);
            }
        }

        if($isPrinted) {
            $baseQuery = $baseQuery->where('goods_receipts.is_printed', 1);
        } else {
            $baseQuery = $baseQuery->where('goods_receipts.is_printed', 0);
        }

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.status', '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->get();

        $itemsPerPage = 42;
        foreach($goodsReceipts as $goodsReceipt) {
            CommonService::paginatePrintPages($goodsReceipt, $goodsReceipt->goodsReceiptItems, $itemsPerPage);
        }

        $data = [
            'id' => $id,
            'goodsReceipts' => $goodsReceipts,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'itemsPerPage' => $itemsPerPage
        ];

        return view('pages.admin.goods-receipt.print', $data);
    }

    public function afterPrint(Request $request, $id) {
        try {
            DB::beginTransaction();

            $filter = (object) $request->all();
            $startNumber = $filter->start_number ?? 0;
            $finalNumber = $filter->final_number ?? 0;

            $baseQuery = GoodsReceipt::query();

            if($id) {
                $baseQuery = $baseQuery->where('goods_receipts.id', $id);
            } else {
                if($startNumber) {
                    $baseQuery = $baseQuery->where('goods_receipts.id', '>=', $startNumber);
                }

                if($finalNumber) {
                    $baseQuery = $baseQuery->where('goods_receipts.id', '<=', $finalNumber);
                } else {
                    $baseQuery = $baseQuery->where('goods_receipts.id', '<=', $startNumber);
                }
            }

            $goodsReceipts = $baseQuery
                ->where('goods_receipts.is_printed', 0)
                ->get();

            foreach ($goodsReceipts as $goodsReceipt) {
                $goodsReceipt->update([
                    'is_printed' => 1,
                    'print_count' => $goodsReceipt->print_count + 1
                ]);
            }

            $route = $id ? 'goods-receipts.create' : 'goods-receipts.index-print';

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

        return Excel::download(new GoodsReceiptExport($request), 'Daftar_Barang_Masuk_'.$fileDate.'.xlsx');
    }

    public function pdf(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderByDesc('goods_receipts.date')
            ->get();

        $goodsReceipts = GoodsReceiptService::mapGoodsReceiptIndex($goodsReceipts);

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'goodsReceipts' => $goodsReceipts,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.goods-receipt.pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Daftar_Barang_Masuk_'.$fileDate.'.pdf');
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $product = Product::query()
            ->findOrFail($filter->product_id);

        $units[] = [
            'id' => $product->unit_id,
            'name' => $product->unit->name,
            'quantity' => 1
        ];

        foreach ($product->productConversions as $conversion) {
            $units[] = [
                'id' => $conversion->unit_id,
                'name' => $conversion->unit->name,
                'quantity' => $conversion->quantity
            ];
        }

        return response()->json([
            'data' => $product,
            'units' => $units,
        ]);
    }

    public function indexListAjax(Request $request) {
        $filter = (object) $request->all();

        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        if(!empty($filter->supplier_id)) {
            $baseQuery = $baseQuery
                ->where('goods_receipts.supplier_id', $filter->supplier_id);
        }

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.status', '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();

        return response()->json([
            'data' => $goodsReceipts,
        ]);
    }

    public function indexDataAjax(Request $request) {
        $filter = (object) $request->all();

        $baseQuery = GoodsReceiptService::getBaseQueryIndex();
        $goodsReceipt = $baseQuery->findOrFail($filter->goods_receipt_id);
        $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;

        foreach($goodsReceiptItems as $goodsReceiptItem) {
            $goodsReceiptItem->product_sku = $goodsReceiptItem->product->sku;
            $goodsReceiptItem->product_name = $goodsReceiptItem->product->name;
            $goodsReceiptItem->unit_name = $goodsReceiptItem->unit->name;
        }

        return response()->json([
            'data' => $goodsReceipt,
            'goods_receipt_items' => $goodsReceiptItems,
        ]);
    }

    public function generateNumberAjax(Request $request) {
        $filter = (object) $request->all();

        $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_GOODS_RECEIPT, $filter->branch_id);

        return response()->json([
            'number' => $number
        ]);
    }
}
