<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Utilities\Constant;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index() {
        $users = User::all();

        $data = [
            'users' => $users
        ];

        return view('pages.admin.user.index', $data);
    }

    public function create() {
        $userRoles = Constant::USER_ROLE_LABELS;

        $data = [
            'userRoles' => $userRoles,
        ];

        return view('pages.admin.user.create', $data);
    }

    public function store(UserCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'status' => Constant::USER_STATUS_ACTIVE,
            ]);

            User::create($request->all());

            DB::commit();

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $user = User::query()->findOrFail($id);
        $userRoles = Constant::USER_ROLE_LABELS;

        $data = [
            'id' => $id,
            'user' => $user,
            'userRoles' => $userRoles,
        ];

        return view('pages.admin.user.edit', $data);
    }

    public function update(UserUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $user = User::query()->findOrFail($id);
            $user->update($data);

            DB::commit();

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('users.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $user = User::query()->findOrFail($id);
            $user->delete();

            DB::commit();

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function trash() {
        $users = User::onlyTrashed()->get();

        $data = [
            'users' => $users
        ];

        return view('pages.admin.user.trash', $data);
    }

    /* public function approve($id) {
        try {
            DB::beginTransaction();

            $user = User::query()->findOrFail($id);
            $user->status = Constant::USER_STATUS_ACTIVE;
            $user->save();

            DB::commit();

            $user->notify(new UserApprovedNotification());

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    } */
}
