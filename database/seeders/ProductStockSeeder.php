<?php

namespace Database\Seeders;

use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class ProductStockSeeder extends Seeder
{
    public function run(): void
    {
        $productStocks = json_decode(file_get_contents(database_path('seeders/json/product_stocks.json')), true);

        foreach ($productStocks as $productStock) {
            ProductStock::create([
                'product_id' => $productStock['product_id'],
                'warehouse_id' => $productStock['warehouse_id'],
                'stock' => $productStock['stock'],
            ]);
        }
    }
}
