<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductTransferCancelRequest;
use App\Http\Requests\ProductTransferCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductTransfer;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\ProductTransferService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductTransferController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $baseQuery = ProductTransferService::getBaseQueryIndex();

        $productTransfers = $baseQuery
            ->where('product_transfers.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('product_transfers.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('product_transfers.date')
            ->get();

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'productTransfers' => $productTransfers
        ];

        return view('pages.admin.product-transfer.index', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');
        $warehouses = Warehouse::all();
        $products = Product::all();
        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'warehouses' => $warehouses,
            'products' => $products,
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.product-transfer.create', $data);
    }

    public function store(ProductTransferCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'date' => $date,
                'status' => Constant::PRODUCT_TRANSFER_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $productTransfer = ProductTransfer::create($request->all());

            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                if(!empty($productId)) {
                    $unitId = $request->get('unit_id')[$index];
                    $sourceWarehouseId = $request->get('source_warehouse_id')[$index];
                    $destinationWarehouseId = $request->get('destination_warehouse_id')[$index];
                    $quantity = $request->get('quantity')[$index];
                    $realQuantity = $request->get('real_quantity')[$index];

                    $actualQuantity = $quantity * $realQuantity;

                    $productTransfer->productTransferItems()->create([
                        'product_id' => $productId,
                        'source_warehouse_id' => $sourceWarehouseId,
                        'destination_warehouse_id' => $destinationWarehouseId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'unit_id' => $unitId,
                    ]);

                    $sourceWarehouseStock = ProductService::getProductStockQuery(
                        $productId,
                        $sourceWarehouseId
                    );

                    $sourceWarehouseStock?->decrement('stock', $actualQuantity);

                    $destinationWarehouseStock = ProductService::getProductStockQuery(
                        $productId,
                        $destinationWarehouseId
                    );

                    ProductService::updateProductStockIncrement(
                        $productId,
                        $destinationWarehouseStock,
                        $actualQuantity,
                        $destinationWarehouseId
                    );
                }
            }

            $parameters = [];
            $route = 'product-transfers.create';

            if($request->get('is_print')) {
                $route = 'product-transfers.print';
                $parameters = ['id' => $productTransfer->id];
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

    public function detail($id) {
        $productTransfer = ProductTransfer::query()->findOrFail($id);
        $productTransferItems = $productTransfer->productTransferItems;

        $data = [
            'id' => $id,
            'productTransfer' => $productTransfer,
            'productTransferItems' => $productTransferItems,
        ];

        return view('pages.admin.product-transfer.detail', $data);
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

    public function destroy(ProductTransferCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $productTransfer = ProductTransfer::query()->findOrFail($id);
            $productTransfer->update([
                'status' => Constant::PRODUCT_TRANSFER_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::createData(
                $productTransfer,
                $productTransfer->productTransferItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            return redirect()->route('product-transfers.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexPrint() {
        $baseQuery = ProductTransferService::getBaseQueryIndex();

        $productTransfers = $baseQuery
            ->where('product_transfers.is_printed', 0)
            ->where('product_transfers.status', '!=', Constant::PRODUCT_TRANSFER_STATUS_WAITING_APPROVAL)
            ->orderBy('product_transfers.date')
            ->get();

        $data = [
            'productTransfers' => $productTransfers
        ];

        return view('pages.admin.product-transfer.index-print', $data);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();
        $startNumber = $filter->start_number ?? 0;
        $finalNumber = $filter->final_number ?? 0;

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = ProductTransferService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('product_transfers.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('product_transfers.id', '>=', $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('product_transfers.id', '<=', $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('product_transfers.id', '<=', $startNumber);
            }
        }

        $productTransfers = $baseQuery
            ->where('product_transfers.is_printed', 0)
            ->get();

        $data = [
            'id' => $id,
            'productTransfers' => $productTransfers,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'rowNumbers' => 35
        ];

        return view('pages.admin.product-transfer.print', $data);
    }

    public function afterPrint(Request $request, $id) {
        try {
            DB::beginTransaction();

            $filter = (object) $request->all();
            $startNumber = $filter->start_number ?? 0;
            $finalNumber = $filter->final_number ?? 0;

            $baseQuery = ProductTransfer::query();

            if($id) {
                $baseQuery = $baseQuery->where('product_transfers.id', $id);
            } else {
                if($startNumber) {
                    $baseQuery = $baseQuery->where('product_transfers.id', '>=', $startNumber);
                }

                if($finalNumber) {
                    $baseQuery = $baseQuery->where('product_transfers.id', '<=', $finalNumber);
                } else {
                    $baseQuery = $baseQuery->where('product_transfers.id', '<=', $startNumber);
                }
            }

            $productTransfers = $baseQuery
                ->where('product_transfers.is_printed', 0)
                ->get();

            foreach ($productTransfers as $productTransfer) {
                $productTransfer->update(['is_printed' => 1]);
            }

            $route = $id ? 'product-transfers.create' : 'product-transfers.index-print';

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
