<?php

namespace App\Utilities\Services;

use App\Models\NumberSetting;
use Illuminate\Support\Facades\DB;

class NumberSettingService
{
    public static function currentNumber($key, $branchId, $isProductTransfer = false): string {
        return DB::transaction(function () use ($key, $branchId, $isProductTransfer) {
            $year = intval(now()->format('y'));
            $month = intval(now()->format('m'));

            $seq = NumberSetting::where('key', $key)
                ->where('branch_id', $branchId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if (!$seq) {
                $seq = static::createNumberSetting($key, $branchId, $year, $month);
            }

            $seq->last_number += 1;

            $branch = !$isProductTransfer ? static::padNumber($branchId, 2) : '';
            $yy     = static::padNumber($year, 2);
            $mm     = static::padNumber($month, 2);
            $num    = static::padNumber($seq->last_number, 4);

            return "{$key}-{$branch}{$yy}{$mm}{$num}";
        });
    }

    public static function generateNumber($key, $branchId, $isProductTransfer = false) {
        return DB::transaction(function () use ($key, $branchId, $isProductTransfer) {
            $year = intval(now()->format('y'));
            $month = intval(now()->format('m'));

            $seq = NumberSetting::where('key', $key)
                ->where('branch_id', $branchId)
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if (!$seq) {
                $seq = static::createNumberSetting($key, $branchId, $year, $month);
            }

            $seq->last_number += 1;
            $seq->save();

            $branch = !$isProductTransfer ? static::padNumber($branchId, 2) : '';
            $yy     = static::padNumber($year, 2);
            $mm     = static::padNumber($month, 2);
            $num    = static::padNumber($seq->last_number, 4);

            return "{$key}-{$branch}{$yy}{$mm}{$num}";
        });
    }

    protected static function createNumberSetting($key, $branchId, $year, $month) {
        return NumberSetting::create([
            'key' => $key,
            'branch_id' => $branchId,
            'year' => $year,
            'month' => $month,
            'last_number' => 0,
        ]);
    }

    protected static function padNumber($number, $length): string {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }
}
