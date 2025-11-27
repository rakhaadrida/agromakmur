<?php

namespace App\Http\Controllers;

use App\Exports\BranchExport;
use App\Http\Requests\BranchCreateRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Models\Branch;
use App\Models\User;
use App\Models\Warehouse;
use App\Utilities\Services\BranchService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BranchController extends Controller
{
    public function index() {
        $branches = Branch::all();

        $data = [
            'branches' => $branches
        ];

        return view('pages.admin.branch.index', $data);
    }

    public function create() {
        $users = User::all();
        $warehouses = Warehouse::all();

        $data = [
            'users' => $users,
            'warehouses' => $warehouses,
        ];

        return view('pages.admin.branch.create', $data);
    }

    public function store(BranchCreateRequest $request) {
        try {
            DB::beginTransaction();

            $branch = Branch::create($request->all());

            BranchService::createUserBranchByBranch($branch, $request->get('user_ids', []));
            BranchService::createBranchWarehouseByBranch($branch, $request->get('warehouse_ids', []));

            DB::commit();

            return redirect()->route('branches.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $branch = Branch::query()->findOrFail($id);

        $userIds = BranchService::findUserIdsByBranchId($id);
        $userIds = implode(',', $userIds);

        $warehouseIds = BranchService::findWarehouseIdsByBranchIds([$id]);
        $warehouseIds = implode(',', $warehouseIds);

        $users = User::all();
        $warehouses = Warehouse::all();

        $data = [
            'id' => $id,
            'branch' => $branch,
            'userIds' => $userIds,
            'warehouseIds' => $warehouseIds,
            'users' => $users,
            'warehouses' => $warehouses,
        ];

        return view('pages.admin.branch.edit', $data);
    }

    public function update(BranchUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $branch = Branch::query()->findOrFail($id);
            $branch->update($data);

            BranchService::createUserBranchByBranch($branch, $request->get('user_ids', []), true);
            BranchService::createBranchWarehouseByBranch($branch, $request->get('warehouse_ids', []), true);

            DB::commit();

            return redirect()->route('branches.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('branches.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $branch = Branch::query()->findOrFail($id);

            $branch->userBranches()->delete();
            $branch->branchWarehouses()->delete();

            $branch->delete();

            DB::commit();

            return redirect()->route('branches.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $branches = Branch::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'branches' => $branches
        ];

        return view('pages.admin.branch.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $branches = Branch::onlyTrashed()->where('is_destroy', 0);

            if($id) {
                $branches = $branches->where('id', $id);
            }

            $branches->restore();

            BranchService::restoreUserBranchByBranchId($id);
            BranchService::restoreBranchWarehouseByBranchId($id);

            DB::commit();

            return redirect()->route('branches.deleted');
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

            $branches = Branch::onlyTrashed();

            if($id) {
                $branches = $branches->where('id', $id);
            }

            $branches->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('branches.deleted');
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

        return Excel::download(new BranchExport(), 'Daftar_Cabang_'.$fileDate.'.xlsx');
    }

    public function branchWarehouseAjax(Request $request) {
        $filter = (object) $request->all();

        $branch = Branch::query()->findOrFail($filter->branch_id);
        $warehouses = $branch->warehouses;

        return response()->json([
            'warehouses' => $warehouses
        ]);
    }
}
