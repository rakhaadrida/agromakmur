<?php

namespace App\Http\Controllers;

use App\Exports\MarketingExport;
use App\Http\Requests\MarketingCreateRequest;
use App\Http\Requests\MarketingUpdateRequest;
use App\Models\Marketing;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MarketingController extends Controller
{
    public function index() {
        $marketings = Marketing::all();

        $data = [
            'marketings' => $marketings
        ];

        return view('pages.admin.marketing.index', $data);
    }

    public function create() {
        return view('pages.admin.marketing.create', []);
    }

    public function store(MarketingCreateRequest $request) {
        try {
            DB::beginTransaction();

            Marketing::create($request->all());

            DB::commit();

            return redirect()->route('marketings.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $marketing = Marketing::query()->findOrFail($id);

        $data = [
            'id' => $id,
            'marketing' => $marketing,
        ];

        return view('pages.admin.marketing.edit', $data);
    }

    public function update(MarketingUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $marketing = Marketing::query()->findOrFail($id);
            $marketing->update($data);

            DB::commit();

            return redirect()->route('marketings.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('marketings.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $marketing = Marketing::query()->findOrFail($id);
            $marketing->delete();

            DB::commit();

            return redirect()->route('marketings.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $marketings = Marketing::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'marketings' => $marketings
        ];

        return view('pages.admin.marketing.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $marketings = Marketing::onlyTrashed();
            if($id) {
                $marketings = $marketings->where('id', $id);
            }

            $marketings->restore();

            DB::commit();

            return redirect()->route('marketings.deleted');
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

            $marketings = Marketing::onlyTrashed();
            if($id) {
                $marketings = $marketings->where('id', $id);
            }

            $marketings->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('marketings.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function export() {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new MarketingExport(), 'Marketing_Data_'.$fileDate.'.xlsx');
    }
}
