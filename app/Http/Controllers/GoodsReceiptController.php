<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceiptCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
use App\Utilities\Services\GoodsReceiptService;
use App\Utilities\Services\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsReceiptController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('goods_receipts.date')
            ->get();

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'goodsReceipts' => $goodsReceipts
        ];

        return view('pages.admin.goods-receipt.index', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::all();
        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'products' => $products,
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.goods-receipt.create', $data);
    }

    public function store(GoodsReceiptCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
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

                    $actualQuantity = $quantity * $realQuantity;
                    $total = $quantity * $price;
                    $subtotal += $total;

                    $goodsReceipt->goodsReceiptItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price' => $price,
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
                        $goodsReceipt->warehouse_id
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

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'number' => $number,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'goodsReceipts' => $goodsReceipts,
        ];

        return view('pages.admin.goods-receipt.index-edit', $data);
    }

    public function update(ProductUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $product = Product::query()->findOrFail($id);
            $product->update($data);

            $product->productConversions()->delete();
            if($request->get('has_conversion')) {
                $product->productConversions()->create([
                    'unit_id' => $request->get('unit_conversion_id'),
                    'quantity' => $request->get('quantity')
                ]);
            }

            $prices = $request->get('price', []);
            $product->productPrices()->delete();
            foreach ($prices as $index => $price) {
                $product->productPrices()->create([
                    'price_id' => $request->get('price_id')[$index],
                    'base_price' => $request->get('base_price')[$index],
                    'tax_amount' => $request->get('tax_amount')[$index],
                    'price' => $price
                ]);
            }

            DB::commit();

            return redirect()->route('products.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('products.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $product = Product::query()->findOrFail($id);
            $product->productPrices()->delete();
            $product->productConversions()->delete();
            $product->delete();

            DB::commit();

            return redirect()->route('products.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function detail($id) {
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;

        $data = [
            'id' => $id,
            'goodsReceipt' => $goodsReceipt,
            'goodsReceiptItems' => $goodsReceiptItems,
        ];

        return view('pages.admin.goods-receipt.detail', $data);
    }

    public function indexPrint() {
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.is_printed', 0)
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
                $goodsReceipt->update(['is_printed' => 1]);
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
}
