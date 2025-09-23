<?php

namespace App\Utilities\Services;

use App\Models\Price;
use App\Models\ProductPrice;
use App\Utilities\Constant;

class PriceService
{
    public static function getRetailPrice() {
        return Price::query()
            ->where('type', Constant::PRICE_TYPE_RETAIL)
            ->first();
    }

    public static function getWholesalePrice() {
        return Price::query()
            ->where('type', Constant::PRICE_TYPE_WHOLESALE)
            ->first();
    }

    public static function updateExistingPrice($type) {
        if($type == Constant::PRICE_TYPE_RETAIL) {
            $retailPrice = static::getRetailPrice();
            $retailPrice?->update([
                'type' => Constant::PRICE_TYPE_GENERAL
            ]);
        } else if($type == Constant::PRICE_TYPE_WHOLESALE) {
            $wholesalePrice = static::getWholesalePrice();
            $wholesalePrice?->update([
                'type' => Constant::PRICE_TYPE_GENERAL
            ]);
        }
    }

    public static function restoreProductPricesByPriceId($priceId) {
        $prices = ProductPrice::onlyTrashed()
            ->where('price_id', $priceId);

        $prices->restore();

        return true;
    }
}
