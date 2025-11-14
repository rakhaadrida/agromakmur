<?php

namespace App\Http\Controllers;

use App\Utilities\Services\BranchService;
use App\Utilities\Services\ReturnService;
use App\Utilities\Services\UserService;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    public function index() {
        $branchIds = UserService::findBranchIdsByUserId(Auth::id());
        $warehouseIds = BranchService::findWarehouseIdsByBranchIds($branchIds);

        $baseQuery = ReturnService::getBaseQueryIndex();

        if(!isUserSuperAdmin()) {
            $baseQuery = $baseQuery->whereIn('warehouses.id', $warehouseIds);
        }

        $products = $baseQuery
            ->groupBy('products.id')
            ->get();

        $data = [
            'products' => $products
        ];

        return view('pages.admin.return.index', $data);
    }
}
