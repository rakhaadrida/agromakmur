<?php

namespace App\Utilities\Services;

class CommonService
{
    public static function removeThousandSeparator($value) {
        return str_replace('.', '', $value);
    }
}
