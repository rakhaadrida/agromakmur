<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = json_decode(file_get_contents(database_path('seeders/json/products.json')), true);

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'category_id' => $product['category_id'],
                'subcategory_id' => $product['subcategory_id'],
                'unit_id' => $product['unit_id'],
            ]);
        }
    }
}
