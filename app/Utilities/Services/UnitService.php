<?php

namespace App\Utilities\Services;

use App\Models\ProductConversion;

class UnitService
{
    public static function restoreProductConversionByUnitId($unitId) {
        $conversions = ProductConversion::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('unit', function($query) {
                $query->where('is_destroy', 0);
            });

        if($unitId) {
            $conversions->where('unit_id', $unitId);
        }

        $conversions->restore();

        return true;
    }
}
