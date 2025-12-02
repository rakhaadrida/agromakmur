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

    public static function paginatePrintPages($subject, $subjectItems, $itemsPerPage) {
        $totalItems = $subjectItems->count();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $subject->total_pages = $totalPages;
        $subject->pages = range(1, $totalPages);

        return $subject;
    }
}
