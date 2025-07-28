<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\PurchaseOrderCreateRequest;
use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\PurchaseOrder;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
use App\Utilities\Services\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function index() {
        $date = Carbon::now()->format('d-m-Y');

        $purchaseOrders = PurchaseOrder::query()
            ->select(
                'purchase_orders.*',
                'warehouses.name AS warehouse_name',
                'suppliers.name AS supplier_name',
                'users.username AS user_name'
            )
            ->leftJoin('warehouses', 'warehouses.id', 'purchase_orders.warehouse_id')
            ->leftJoin('suppliers', 'suppliers.id', 'purchase_orders.supplier_id')
            ->leftJoin('users', 'users.id', 'purchase_orders.user_id')
            ->where('purchase_orders.date', '>=',  Carbon::now()->startOfDay())
            ->get();

        $data = [
            'date' => $date,
            'purchaseOrders' => $purchaseOrders
        ];

        return view('pages.admin.purchase-order.index', $data);
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

        return view('pages.admin.purchase-order.create', $data);
    }

    public function store(PurchaseOrderCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'date' => $date,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'status' => Constant::PURCHASE_ORDER_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $purchaseOrder = PurchaseOrder::create($request->all());

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

                    $purchaseOrder->purchaseOrderItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price' => $price,
                        'total' => $total
                    ]);

                    $productStock = ProductService::getProductStockQuery(
                        $productId,
                        $purchaseOrder->warehouse_id
                    );

                    if($productStock) {
                        $productStock->increment('stock', $actualQuantity);
                    } else {
                        ProductStock::create([
                            'product_id' => $productId,
                            'warehouse_id' => $purchaseOrder->warehouse_id,
                            'stock' => $actualQuantity
                        ]);
                    }
                }
            }

            $taxAmount = $subtotal * (10 / 100);
            $grandTotal = $subtotal + $taxAmount;

            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal
            ]);

            AccountPayableService::createData($purchaseOrder);

            DB::commit();

            return redirect()->route('purchase-orders.create');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $product = Product::query()->findOrFail($id);
        $categories = Category::all();
        $units = Unit::all();
        $prices = Price::all();
        $subcategories = Subcategory::query()
            ->where('category_id', $product->category_id)
            ->get();

        $productPrices = $product->productPrices->mapWithKeys(function($productPrice) {
            $array = [];

            $array[$productPrice->price_id] = [
                'base_price' => $productPrice->base_price,
                'tax_amount' => $productPrice->tax_amount,
                'price' => $productPrice->price
            ];

            return $array;
        });

        $productPrices = $productPrices->toArray();

        $data = [
            'id' => $id,
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'prices' => $prices,
            'subcategories' => $subcategories,
            'productPrices' => $productPrices
        ];

        return view('pages.admin.product.edit', $data);
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

    public function stock($id) {
        $product = Product::query()
            ->select(
                'products.*',
                'categories.name AS category_name',
                'subcategories.name AS subcategory_name',
                'units.name AS unit_name'
            )
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('subcategories', 'subcategories.id', 'products.subcategory_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->findOrFail($id);

        $warehouses = Warehouse::all();
        $productStocks = $product->productStocks->mapWithKeys(function($productStock) {
            $array = [];

            $array[$productStock->warehouse_id] = [
                'product_id' => $productStock->product_id,
                'stock' => $productStock->stock,
            ];

            return $array;
        });

        $productStocks = $productStocks->toArray();

        $data = [
            'product' => $product,
            'warehouses' => $warehouses,
            'productStocks' => $productStocks
        ];

        return view('pages.admin.product.stock', $data);
    }

    public function indexPrint() {
        $purchaseOrders = PurchaseOrder::query()
            ->select(
                'purchase_orders.*',
                'warehouses.name AS warehouse_name',
                'suppliers.name AS supplier_name'
            )
            ->leftJoin('warehouses', 'warehouses.id', 'purchase_orders.warehouse_id')
            ->leftJoin('suppliers', 'suppliers.id', 'purchase_orders.supplier_id')
            ->where('purchase_orders.is_printed', 0)
            ->get();

        $data = [
            'purchaseOrders' => $purchaseOrders
        ];

        return view('pages.admin.purchase-order.index-print', $data);
    }
}
