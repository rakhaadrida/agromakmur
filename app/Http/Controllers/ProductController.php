<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\SubcategoryUpdateRequest;
use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Facades\DB;

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

        $data = [
            'products' => $products
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

            if($request->get('conversion')) {
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
            'productPrices' => $productPrices
        ];

        return view('pages.admin.product.edit', $data);
    }

    public function update(SubcategoryUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $subcategory = Subcategory::query()->findOrFail($id);
            $subcategory->update($data);

            DB::commit();

            return redirect()->route('subcategories.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('subcategories.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $subcategory = Subcategory::query()->findOrFail($id);
            $subcategory->delete();

            DB::commit();

            return redirect()->route('subcategories.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $subcategories = Subcategory::onlyTrashed()
            ->select(
                'subcategories.*',
                'categories.name AS category_name'
            )
            ->leftJoin('categories', 'categories.id', 'subcategories.category_id')
            ->where('subcategories.is_destroy', 0)
            ->get();

        $data = [
            'subcategories' => $subcategories
        ];

        return view('pages.admin.subcategory.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $subcategories = Subcategory::onlyTrashed();
            if($id) {
                $subcategories = $subcategories->where('id', $id);
            }

            $subcategories->restore();

            DB::commit();

            return redirect()->route('subcategories.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function remove($id) {
        try {
            DB::beginTransaction();

            $subcategories = Subcategory::onlyTrashed();
            if($id) {
                $subcategories = $subcategories->where('id', $id);
            }

            $subcategories->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('subcategories.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
