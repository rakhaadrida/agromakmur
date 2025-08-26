<?php

namespace App\Utilities\Services;

use App\Models\Warehouse;
use App\Utilities\Constant;

class WarehouseService
{
    public static function getReturnWarehouse() {
        return Warehouse::query()
            ->where('type', Constant::WAREHOUSE_TYPE_RETURN)
            ->whereNull('deleted_at')
            ->first();
    }
}
