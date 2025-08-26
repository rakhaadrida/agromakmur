<?php

namespace App\Utilities\Services;

class CommonService
{
    public static function calculateDiscountPercentage($discount) {
        $maxDiscount = 100;

        $discount = str_replace(',', '.', $discount);
        $arrayDiscount = explode('+', $discount);

        foreach($arrayDiscount as $value) {
            $maxDiscount -= ($value * $maxDiscount) / 100;
        }

        return (($maxDiscount - 100) * -1);
    }
}
