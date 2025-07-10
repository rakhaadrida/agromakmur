<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();

        $data = [
            'categories' => $categories
        ];

        return view('pages.admin.category.index', $data);
    }

    public function create() {
        return view('pages.admin.category.create', []);
    }

    public function store(CategoryCreateRequest $request) {
        try {
            DB::beginTransaction();

            Category::create($request->all());

            DB::commit();

            return redirect()->route('categories.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $category = Category::query()->findOrFail($id);

        $data = [
            'id' => $id,
            'category' => $category,
        ];

        return view('pages.admin.category.edit', $data);
    }

    public function update(CategoryUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $category = Category::query()->findOrFail($id);
            $category->update($data);

            DB::commit();

            return redirect()->route('categories.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('categories.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $category = Category::query()->findOrFail($id);
            $category->delete();

            DB::commit();

            return redirect()->route('categories.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $categories = Category::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'categories' => $categories
        ];

        return view('pages.admin.category.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $categories = Category::onlyTrashed();
            if($id) {
                $categories = $categories->where('id', $id);
            }

            $categories->restore();

            DB::commit();

            return redirect()->route('categories.deleted');
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

            $categories = Category::onlyTrashed();
            if($id) {
                $categories = $categories->where('id', $id);
            }

            $categories->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('categories.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
