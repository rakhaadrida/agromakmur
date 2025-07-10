<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceCreateRequest;
use App\Http\Requests\PriceUpdateRequest;
use App\Models\Price;
use Exception;
use Illuminate\Support\Facades\DB;

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
        return view('pages.admin.price.create', []);
    }

    public function store(PriceCreateRequest $request) {
        try {
            DB::beginTransaction();

            Price::create($request->all());

            DB::commit();

            return redirect()->route('prices.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $price = Price::query()->findOrFail($id);

        $data = [
            'id' => $id,
            'price' => $price,
        ];

        return view('pages.admin.price.edit', $data);
    }

    public function update(PriceUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $price = Price::query()->findOrFail($id);
            $price->update($data);

            DB::commit();

            return redirect()->route('prices.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('prices.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $price = Price::query()->findOrFail($id);
            $price->delete();

            DB::commit();

            return redirect()->route('prices.index');
        } catch (Exception $e) {
            DB::rollBack();

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

            $prices = Price::onlyTrashed();
            if($id) {
                $prices = $prices->where('id', $id);
            }

            $prices->restore();

            DB::commit();

            return redirect()->route('prices.deleted');
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

            $prices = Price::onlyTrashed();
            if($id) {
                $prices = $prices->where('id', $id);
            }

            $prices->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('prices.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
