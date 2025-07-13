<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Product M',
                'category_id' => 1,
                'subcategory_id' => 2,
                'unit_id' => 2
            ],
            [
                'name' => 'Product N',
                'category_id' => 3,
                'subcategory_id' => 3,
                'unit_id' => 1
            ],
            [
                'name' => 'Product O',
                'category_id' => 2,
                'subcategory_id' => 1,
                'unit_id' => 3
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'category_id' => $product['category_id'],
                'subcategory_id' => $product['subcategory_id'],
                'unit_id' => $product['unit_id'],
            ]);
        }
    }
}
