<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductStockRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Utilities\Constant;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index() {
        $products = Product::query()
            ->select(
                'products.*',
                'categories.name AS category_name',
                'subcategories.name AS subcategory_name',
                'units.name AS unit_name'
            )
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('subcategories', 'subcategories.id', 'products.subcategory_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->get();

        $warehouses = Warehouse::all();
        $productStocks = ProductStock::query()
            ->whereNull('deleted_at')
            ->get();

        $mapStockByProductWarehouse = [];
        foreach($productStocks as $productStock) {
            $mapStockByProductWarehouse[$productStock->product_id][$productStock->warehouse_id] = $productStock->stock;
        }

        $data = [
            'products' => $products,
            'warehouses' => $warehouses,
            'mapStockByProductWarehouse' => $mapStockByProductWarehouse
        ];

        return view('pages.admin.product.index', $data);
    }

    public function create() {
        $categories = Category::all();
        $units = Unit::all();
        $prices = Price::all();

        $data = [
            'categories' => $categories,
            'units' => $units,
            'prices' => $prices
        ];

        return view('pages.admin.product.create', $data);
    }

    public function store(ProductCreateRequest $request) {
        try {
            DB::beginTransaction();

            $product = Product::create($request->all());

            if($request->get('has_conversion')) {
                $product->productConversions()->create([
                    'unit_id' => $request->get('unit_conversion_id'),
                    'quantity' => $request->get('quantity')
                ]);
            }

            $prices = $request->get('price', []);
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
            Log::error($e->getMessage());

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
            Log::error($e->getMessage());

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

    public function updateStock(ProductStockRequest $request, $id) {
        try {
            DB::beginTransaction();

            $product = Product::query()->findOrFail($id);

            $stocks = $request->get('stock', []);
            $product->productStocks()->delete();
            foreach ($stocks as $index => $stock) {
                $product->productStocks()->create([
                    'warehouse_id' => $request->get('warehouse_id')[$index],
                    'stock' => $stock
                ]);
            }

            DB::commit();

            return redirect()->route('products.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('products.stock', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function indexDeleted() {
        $products = Product::onlyTrashed()
            ->select(
                'products.*',
                'categories.name AS category_name',
                'subcategories.name AS subcategory_name',
                'units.name AS unit_name'
            )
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('subcategories', 'subcategories.id', 'products.subcategory_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->where('products.is_destroy', 0)
            ->get();

        $data = [
            'products' => $products
        ];

        return view('pages.admin.product.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $products = Product::onlyTrashed();
            if($id) {
                $products = $products->where('id', $id);
            }

            $products->restore();

            DB::commit();

            return redirect()->route('products.deleted');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function remove($id) {
        try {
            DB::beginTransaction();

            $products = Product::onlyTrashed();
            if($id) {
                $products = $products->where('id', $id);
            }

            $products->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('products.deleted');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new ProductExport(), 'Product_Data_'.$fileDate.'.xlsx');
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $product = Product::query()
            ->with(['mainPrice'])
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

        foreach ($product->productPrices as $productPrice) {
            $prices[] = [
                'id' => $productPrice->price_id,
                'code' => $productPrice->pricing->code,
                'type' => $productPrice->pricing->type,
                'price' => $productPrice->price
            ];
        }

        $productStocks = $product->productStocks->mapWithKeys(function($stock) {
            $array = [];
            $array[$stock->warehouse_id] = $stock->stock;

            return $array;
        });

        return response()->json([
            'data' => $product,
            'units' => $units,
            'prices' => $prices,
            'main_price_id' => $product->mainPrice ? $product->mainPrice->price_id : null,
            'main_price' => $product->mainPrice ? $product->mainPrice->price : 0,
            'product_stocks' => $productStocks,
        ]);
    }

    public function checkStockAjax(Request $request) {
        $filter = (object) $request->all();

        $product = Product::query()
            ->with(['mainPrice'])
            ->findOrFail($filter->product_id);

        $productStocks = $product->productStocks;
        $totalStock = $productStocks->sum('stock');

        $primaryWarehouse = [];
        $otherWarehouses = [];
        foreach ($productStocks as $productStock) {
            if ($productStock->warehouse->type == Constant::WAREHOUSE_TYPE_PRIMARY) {
                $primaryWarehouse = [
                    'id' => $productStock->warehouse_id,
                    'name' => $productStock->warehouse->name,
                    'stock' => $productStock->stock
                ];
            } else {
                $otherWarehouses[] = [
                    'id' => $productStock->warehouse_id,
                    'name' => $productStock->warehouse->name,
                    'stock' => $productStock->stock
                ];
            }
        }

        return response()->json([
            'data' => $product,
            'product_stocks' => $productStocks,
            'total_stock' => $totalStock,
            'primary_warehouse' => $primaryWarehouse,
            'other_warehouses' => $otherWarehouses,
        ]);
    }
}
