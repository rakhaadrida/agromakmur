<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Http\Requests\SupplierCreateRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index() {
        $suppliers = Supplier::all();

        $data = [
            'suppliers' => $suppliers
        ];

        return view('pages.admin.supplier.index', $data);
    }

    public function create() {
        return view('pages.admin.supplier.create', []);
    }

    public function store(SupplierCreateRequest $request) {
        try {
            DB::beginTransaction();

            Supplier::create($request->all());

            DB::commit();

            return redirect()->route('suppliers.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $supplier = Supplier::query()->findOrFail($id);

        $data = [
            'id' => $id,
            'supplier' => $supplier,
        ];

        return view('pages.admin.supplier.edit', $data);
    }

    public function update(SupplierUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $supplier = Supplier::query()->findOrFail($id);
            $supplier->update($data);

            DB::commit();

            return redirect()->route('suppliers.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('suppliers.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $supplier = Supplier::query()->findOrFail($id);
            $supplier->delete();

            DB::commit();

            return redirect()->route('suppliers.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $suppliers = Supplier::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'suppliers' => $suppliers
        ];

        return view('pages.admin.supplier.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $suppliers = Supplier::onlyTrashed();
            if($id) {
                $suppliers = $suppliers->where('id', $id);
            }

            $suppliers->restore();

            DB::commit();

            return redirect()->route('suppliers.deleted');
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

            $suppliers = Supplier::onlyTrashed();
            if($id) {
                $suppliers = $suppliers->where('id', $id);
            }

            $suppliers->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('suppliers.deleted');
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

        return Excel::download(new SupplierExport(), 'Supplier_Data_'.$fileDate.'.xlsx');
    }
}
