<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'name' => 'New Price',
                'code' => 'NP',
            ],
            [
                'name' => 'Old Price',
                'code' => 'OP',
            ],
            [
                'name' => 'Wholesale Price',
                'code' => 'WP',
            ],
        ];

        foreach ($prices as $price) {
            Price::create([
                'name' => $price['name'],
                'code' => $price['code'],
            ]);
        }
    }
}
