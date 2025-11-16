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
                'name' => 'New Price',
                'code' => 'NP',
                'type' => Constant::PRICE_TYPE_GENERAL
            ],
            [
                'name' => 'Old Price',
                'code' => 'OP',
                'type' => Constant::PRICE_TYPE_RETAIL
            ],
            [
                'name' => 'Wholesale Price',
                'code' => 'WP',
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
