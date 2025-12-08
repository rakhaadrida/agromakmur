<?php

namespace Database\Seeders;

use App\Models\Price;
use App\Utilities\Constant;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'name' => 'Harga Beli',
                'code' => 'HB',
                'type' => Constant::PRICE_TYPE_GENERAL
            ],
            [
                'name' => 'Harga Grosir',
                'code' => 'HG',
                'type' => Constant::PRICE_TYPE_RETAIL
            ],
            [
                'name' => 'Harga Eceran',
                'code' => 'HE',
                'type' => Constant::PRICE_TYPE_WHOLESALE
            ],
        ];

        foreach ($prices as $price) {
            Price::create([
                'name' => $price['name'],
                'code' => $price['code'],
                'type' => $price['type'],
            ]);
        }
    }
}
