<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitCreateRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    public function index() {
        $units = Unit::all();

        $data = [
            'units' => $units
        ];

        return view('pages.admin.unit.index', $data);
    }

    public function create() {
        return view('pages.admin.unit.create', []);
    }

    public function store(UnitCreateRequest $request) {
        try {
            DB::beginTransaction();

            Unit::create($request->all());

            DB::commit();

            return redirect()->route('units.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $unit = Unit::query()->findOrFail($id);

        $data = [
            'id' => $id,
            'unit' => $unit,
        ];

        return view('pages.admin.unit.edit', $data);
    }

    public function update(UnitUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $unit = Unit::query()->findOrFail($id);
            $unit->update($data);

            DB::commit();

            return redirect()->route('units.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('units.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $unit = Unit::query()->findOrFail($id);
            $unit->delete();

            DB::commit();

            return redirect()->route('units.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $units = Unit::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'units' => $units
        ];

        return view('pages.admin.unit.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $units = Unit::onlyTrashed();
            if($id) {
                $units = $units->where('id', $id);
            }

            $units->restore();

            DB::commit();

            return redirect()->route('units.deleted');
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

            $units = Unit::onlyTrashed();
            if($id) {
                $units = $units->where('id', $id);
            }

            $units->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('units.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
