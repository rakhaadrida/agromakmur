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
                'tempo' => $request->get('tempo') || 0,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
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
                    $total = ($quantity * $price) + $totalExpenses;
                    $subtotal += $total;

                    $goodsReceipt->goodsReceiptItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price' => $price,
                        'wages' => $wages,
                        'shipping_cost' => $shippingCost,
                        'total' => $total
                    ]);

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

            $goodsReceipt->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal
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
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('goods_receipts.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        if($number) {
            $baseQuery = $baseQuery->where('goods_receipts.number', $number);
        }

        if($supplierId) {
            $baseQuery = $baseQuery->where('goods_receipts.supplier_id', $supplierId);
        }

        $goodsReceipts = $baseQuery
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();

        $goodsReceipts = GoodsReceiptService::mapGoodsReceiptIndex($goodsReceipts);

        $goodsReceiptIds = $goodsReceipts->pluck('id')->toArray();
        $goodsReceiptRevisions = ApprovalService::getRevisionCountBySubject(GoodsReceipt::class, $goodsReceiptIds, true);

        $mapRevisionByGoodsReceiptId = [];
        foreach ($goodsReceiptRevisions as $revision) {
            $mapRevisionByGoodsReceiptId[$revision->subject_id] = $revision->revision_count;
        }

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'number' => $number,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'goodsReceipts' => $goodsReceipts,
            'mapRevisionByGoodsReceiptId' => $mapRevisionByGoodsReceiptId
        ];

        return view('pages.admin.goods-receipt.index-edit', $data);
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
            'number' => $request->number ?? null,
            'supplierId' => $request->supplier_id ?? null,
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
                'number' => $request->get('receipt_number', null),
                'supplier_id' => $request->get('supplier_id', null),
            ];

            return redirect()->route('goods-receipts.index-edit', $params);
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

            ApprovalService::deleteData($goodsReceipt->approvals);
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
                'number' => $request->get('number', null),
                'supplier_id' => $request->get('supplier_id', null),
            ];

            return redirect()->route('goods-receipts.index-edit', $params);
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

    public function print(Request $request, $id) {
        $filter = (object) $request->all();
        $startNumber = $filter->start_number ?? 0;
        $finalNumber = $filter->final_number ?? 0;

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

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
            ->where('goods_receipts.status', '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->get();

        $data = [
            'id' => $id,
            'goodsReceipts' => $goodsReceipts,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'rowNumbers' => 35
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

        return Excel::download(new GoodsReceiptExport($request), 'Goods_Receipt_Data_'.$fileDate.'.xlsx');
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

        return $pdf->stream('Goods_Receipt_Data_'.$fileDate.'.pdf');
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $product = Product::query()
            ->with(['mainPrice'])
            ->findOrFail($filter->product_id);

        $mainPrice = $product->mainPrice ? $product->mainPrice->price : 0;

        $latestReceiptPrice = GoodsReceipt::query()
            ->select(
                'goods_receipt_items.product_id',
                'goods_receipt_items.price',
            )
            ->leftJoin('goods_receipt_items', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->where('supplier_id', $filter->supplier_id)
            ->where('goods_receipt_items.product_id', $filter->product_id)
            ->whereNull('goods_receipt_items.deleted_at')
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->first();

        if($latestReceiptPrice) {
            $mainPrice = $latestReceiptPrice->price;
        }

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
            'main_price_id' => $product->mainPrice ? $product->mainPrice->price_id : null,
            'main_price' => $mainPrice,
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
