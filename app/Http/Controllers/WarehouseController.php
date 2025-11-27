<?php

namespace App\Http\Controllers;

use App\Exports\WarehouseExport;
use App\Http\Requests\WarehouseCreateRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\ProductStockService;
use App\Utilities\Services\WarehouseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseController extends Controller
{
    public function index() {
        $warehouses = WarehouseService::getBaseQueryIndex();

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

        $branches = Branch::all();

        $data = [
            'warehouseTypes' => $warehouseTypes,
            'branches' => $branches,
        ];

        return view('pages.admin.warehouse.create', $data);
    }

    public function store(WarehouseCreateRequest $request) {
        try {
            DB::beginTransaction();

            $warehouse = Warehouse::create($request->all());

            WarehouseService::createBranchWarehouseByWarehouse($warehouse, $request->get('branch_ids', []));
            ProductStockService::createStockByWarehouse($warehouse);

            DB::commit();

            return redirect()->route('warehouses.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

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

        $branchIds = WarehouseService::findBranchIdsByWarehouseId($id);
        $branchIds = implode(',', $branchIds);

        $branches = Branch::all();

        $data = [
            'id' => $id,
            'warehouse' => $warehouse,
            'warehouseTypes' => $warehouseTypes,
            'branchIds' => $branchIds,
            'branches' => $branches,
        ];

        return view('pages.admin.warehouse.edit', $data);
    }

    public function update(WarehouseUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $warehouse = Warehouse::query()->findOrFail($id);
            $warehouse->update($data);

            WarehouseService::createBranchWarehouseByWarehouse($warehouse, $request->get('branch_ids', []), true);

            DB::commit();

            return redirect()->route('warehouses.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('warehouses.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $warehouse = Warehouse::query()->findOrFail($id);

            $warehouse->branchWarehouses()->delete();
            $warehouse->productStocks()->delete();

            $warehouse->delete();

            DB::commit();

            return redirect()->route('warehouses.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

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

            $warehouses = Warehouse::onlyTrashed()->where('is_destroy', 0);

            if($id) {
                $warehouses = $warehouses->where('id', $id);
            }

            $warehouses->restore();

            WarehouseService::restoreBranchWarehouseByWarehouseId($id);
            ProductStockService::restoreStockByWarehouseId($id);

            DB::commit();

            return redirect()->route('warehouses.deleted');
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

            $warehouses = Warehouse::onlyTrashed();
            if($id) {
                $warehouses = $warehouses->where('id', $id);
            }

            $warehouses->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('warehouses.deleted');
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

        return Excel::download(new WarehouseExport(), 'Daftar_Gudang_'.$fileDate.'.xlsx');
    }
}
