<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\Marketing;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index() {
        $customers = Customer::query()
            ->select(
                'customers.*',
                'marketings.name AS marketing_name'
            )
            ->leftJoin('marketings', 'marketings.id', 'customers.marketing_id')
            ->get();

        $data = [
            'customers' => $customers
        ];

        return view('pages.admin.customer.index', $data);
    }

    public function create() {
        $marketings = Marketing::all();

        $data = [
            'marketings' => $marketings
        ];

        return view('pages.admin.customer.create', $data);
    }

    public function store(CustomerCreateRequest $request) {
        try {
            DB::beginTransaction();

            Customer::create($request->all());

            DB::commit();

            return redirect()->route('customers.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $customer = Customer::query()->findOrFail($id);
        $marketings = Marketing::all();

        $data = [
            'id' => $id,
            'customer' => $customer,
            'marketings' => $marketings
        ];

        return view('pages.admin.customer.edit', $data);
    }

    public function update(CustomerUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $customer = Customer::query()->findOrFail($id);
            $customer->update($data);

            DB::commit();

            return redirect()->route('customers.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('customers.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $customer = Customer::query()->findOrFail($id);
            $customer->delete();

            DB::commit();

            return redirect()->route('customers.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $customers = Customer::onlyTrashed()
            ->select(
                'customers.*',
                'marketings.name AS marketing_name'
            )
            ->leftJoin('marketings', 'marketings.id', 'customers.marketing_id')
            ->where('customers.is_destroy', 0)
            ->get();

        $data = [
            'customers' => $customers
        ];

        return view('pages.admin.customer.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $customers = Customer::onlyTrashed();
            if($id) {
                $customers = $customers->where('id', $id);
            }

            $customers->restore();

            DB::commit();

            return redirect()->route('customers.deleted');
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

            $customers = Customer::onlyTrashed();
            if($id) {
                $customers = $customers->where('id', $id);
            }

            $customers->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('customers.deleted');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
