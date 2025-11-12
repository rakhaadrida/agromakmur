<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceCreateRequest;
use App\Http\Requests\PriceUpdateRequest;
use App\Models\Price;
use App\Utilities\Constant;
use App\Utilities\Services\PriceService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PriceController extends Controller
{
    public function index() {
        $prices = Price::all();

        $data = [
            'prices' => $prices
        ];

        return view('pages.admin.price.index', $data);
    }

    public function create() {
        $priceTypes = Constant::PRICE_TYPE_LABELS;

        $data = [
            'priceTypes' => $priceTypes
        ];

        return view('pages.admin.price.create', $data);
    }

    public function store(PriceCreateRequest $request) {
        try {
            DB::beginTransaction();

            PriceService::updateExistingPrice($request->get('type'));
            Price::create($request->all());

            DB::commit();

            return redirect()->route('prices.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $price = Price::query()->findOrFail($id);
        $priceTypes = Constant::PRICE_TYPE_LABELS;

        $data = [
            'id' => $id,
            'price' => $price,
            'priceTypes' => $priceTypes
        ];

        return view('pages.admin.price.edit', $data);
    }

    public function update(PriceUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();

            PriceService::updateExistingPrice($data['type']);

            $price = Price::query()->findOrFail($id);
            $price->update($data);

            DB::commit();

            return redirect()->route('prices.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('prices.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $price = Price::query()->findOrFail($id);
            $price->productPrices()->delete();
            $price->delete();

            DB::commit();

            return redirect()->route('prices.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $prices = Price::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'prices' => $prices
        ];

        return view('pages.admin.price.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $prices = Price::onlyTrashed()->where('is_destroy', 0);

            if($id) {
                $prices = $prices->where('id', $id);
            }

            $prices->restore();

            PriceService::restoreProductPricesByPriceId($id);

            DB::commit();

            return redirect()->route('prices.deleted');
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

            $prices = Price::onlyTrashed();

            if($id) {
                $prices = $prices->where('id', $id);
            }

            $prices->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('prices.deleted');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
