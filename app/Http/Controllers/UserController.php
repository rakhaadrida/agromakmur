<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdatePasswordRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Branch;
use App\Models\User;
use App\Utilities\Constant;
use App\Utilities\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index() {
        $users = UserService::getBaseQueryIndex();

        $data = [
            'users' => $users
        ];

        return view('pages.admin.user.index', $data);
    }

    public function create() {
        $userRoles = Constant::USER_ROLE_LABELS;
        $branches = Branch::all();

        $data = [
            'userRoles' => $userRoles,
            'branches' => $branches,
        ];

        return view('pages.admin.user.create', $data);
    }

    public function store(UserCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'status' => Constant::USER_STATUS_ACTIVE,
            ]);

            $user = User::create($request->all());

            UserService::createUserBranchByUser($user, $request->get('branch_ids', []));

            DB::commit();

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $user = User::query()->findOrFail($id);
        $userRoles = Constant::USER_ROLE_LABELS;

        $branchIds = UserService::findBranchIdsByUserId($id);
        $branchIds = implode(',', $branchIds);

        $branches = Branch::all();

        $data = [
            'id' => $id,
            'user' => $user,
            'userRoles' => $userRoles,
            'branchIds' => $branchIds,
            'branches' => $branches,
        ];

        return view('pages.admin.user.edit', $data);
    }

    public function update(UserUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $user = User::query()->findOrFail($id);
            $user->update($data);

            UserService::createUserBranchByUser($user, $request->get('branch_ids', []), true);

            DB::commit();

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('users.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $user = User::query()->findOrFail($id);

            $user->userBranches()->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexDeleted() {
        $users = User::onlyTrashed()->where('is_destroy', 0)->get();

        $data = [
            'users' => $users
        ];

        return view('pages.admin.user.trash', $data);
    }

    public function restore($id) {
        try {
            DB::beginTransaction();

            $users = User::onlyTrashed()->where('is_destroy', 0);

            if($id) {
                $users = $users->where('id', $id);
            }

            $users->restore();

            UserService::restoreUserBranchByUserId($id);

            DB::commit();

            return redirect()->route('users.deleted');
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

            $users = User::onlyTrashed();
            if($id) {
                $users = $users->where('id', $id);
            }

            $users->update(['is_destroy' => 1]);

            DB::commit();

            return redirect()->route('users.deleted');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function changePassword() {
        return view('auth.passwords.reset', []);
    }

    public function updatePassword(UserUpdatePasswordRequest $request) {
        try {
            DB::beginTransaction();

            $userId = Auth::user()->id;
            $user = User::query()->findOrFail($userId);

            $user->update([
                'password' => $request->get('new_password'),
            ]);

            DB::commit();

            $message = 'Password changed successfully';

            return redirect()->route('change-password')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function validatePasswordAjax(UserPasswordRequest $request) {
        $filter = (object) $request->all();

        $userId = Auth::user()->id;
        $user = User::query()->findOrFail($userId);

        $password = $filter->password;

        if (password_verify($password, $user->password)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password is valid'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Password'
            ], 422);
        }
    }
}
