<?php

namespace App\Http\Controllers;

use App\Exports\SubcategoryExport;
use App\Http\Requests\SubcategoryCreateRequest;
use App\Http\Requests\SubcategoryUpdateRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Utilities\Services\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SubcategoryController extends Controller
{
    public function index(Request $request) {
        $subcategories = Subcategory::query()
            ->select(
                'subcategories.*',
                'categories.name AS category_name'
            )
            ->leftJoin('categories', 'categories.id', 'subcategories.category_id')
            ->get();

        $data = [
            'subcategories' => $subcategories
        ];

        return view('pages.admin.subcategory.index', $data);
    }

    public function create() {
        $categories = Category::all();

        $data = [
            'categories' => $categories
        ];

        return view('pages.admin.subcategory.create', $data);
    }

    public function store(SubcategoryCreateRequest $request) {
        try {
            DB::beginTransaction();

            Subcategory::create($request->all());

            DB::commit();

            return redirect()->route('subcategories.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $subcategory = Subcategory::query()->findOrFail($id);
        $categories = Category::all();

        $data = [
            'id' => $id,
            'subcategory' => $subcategory,
            'categories' => $categories
        ];

        return view('pages.admin.subcategory.edit', $data);
    }

    public function update(SubcategoryUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $subcategory = Subcategory::query()->findOrFail($id);
            $subcategory->update($data);

            ProductService::updateProductCategoryBySubcategory($subcategory);

            DB::commit();

            return redirect()->route('subcategories.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

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
            Log::error($e->getMessage());

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
            Log::error($e->getMessage());

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
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new SubcategoryExport(), 'Subcategory_Data_'.$fileDate.'.xlsx');
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $subcategories = Subcategory::query();

        if ($filter->category_id) {
            $subcategories = $subcategories->where('category_id', $filter->category_id);
        }

        $subcategories = $subcategories->get();

        return response()->json($subcategories);
    }
}
