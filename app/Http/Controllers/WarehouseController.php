<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseCreateRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Models\Warehouse;
use App\Utilities\Constant;
use Exception;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index() {
        $warehouses = Warehouse::all();

        $data = [
            'warehouses' => $warehouses
        ];

        return view('pages.admin.warehouse.index', $data);
    }

    public function create() {
        $warehouseTypes = Constant::WAREHOUSE_TYPE_LABELS;

        $primaryWarehouse = Warehouse::query()->where('type', Constant::WAREHOUSE_TYPE_PRIMARY)->first();
        if($primaryWarehouse) {
            unset($warehouseTypes[Constant::WAREHOUSE_TYPE_PRIMARY]);
        }

        $data = [
            'warehouseTypes' => $warehouseTypes
        ];

        return view('pages.admin.warehouse.create', $data);
    }

    public function store(WarehouseCreateRequest $request) {
        try {
            DB::beginTransaction();

            Warehouse::create($request->all());

            DB::commit();

            return redirect()->route('warehouses.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $warehouse = Warehouse::query()->findOrFail($id);
        $warehouseTypes = Constant::WAREHOUSE_TYPE_LABELS;

        $primaryWarehouse = Warehouse::query()->where('type', Constant::WAREHOUSE_TYPE_PRIMARY)->first();
        if($primaryWarehouse) {
            unset($warehouseTypes[Constant::WAREHOUSE_TYPE_PRIMARY]);
        }

        $data = [
            'id' => $id,
            'warehouse' => $warehouse,
            'warehouseTypes' => $warehouseTypes
        ];

        return view('pages.admin.warehouse.edit', $data);
    }

    public function update(WarehouseUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $warehouse = Warehouse::query()->findOrFail($id);
            $warehouse->update($data);

            DB::commit();

            return redirect()->route('warehouses.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('warehouses.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $warehouse = Warehouse::query()->findOrFail($id);
            $warehouse->delete();

            DB::commit();

            return redirect()->route('warehouses.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $warehouses = Warehouse::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'warehouses' => $warehouses
        ];

        return view('pages.admin.warehouse.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $warehouses = Warehouse::onlyTrashed();
            if($id) {
                $warehouses = $warehouses->where('id', $id);
            }

            $warehouses->restore();

            DB::commit();

            return redirect()->route('warehouses.deleted');
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

            $warehouses = Warehouse::onlyTrashed();
            if($id) {
                $warehouses = $warehouses->where('id', $id);
            }

            $warehouses->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('warehouses.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
