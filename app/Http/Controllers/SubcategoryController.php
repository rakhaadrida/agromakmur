<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubcategoryCreateRequest;
use App\Http\Requests\SubcategoryUpdateRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Exception;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function index() {
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
