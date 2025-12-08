<?php

namespace Database\Seeders;

use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    public function run(): void
    {
        $productPrices = json_decode(file_get_contents(database_path('seeders/json/product_prices.json')), true);

        foreach ($productPrices as $productPrice) {
            $basePrice = floor($productPrice['price'] / 1.1);
            $taxAmount = floor($productPrice['price'] - $basePrice);

            ProductPrice::create([
                'product_id' => $productPrice['product_id'],
                'price_id' => $productPrice['price_id'],
                'base_price' => $basePrice,
                'tax_amount' => $taxAmount,
                'price' => $productPrice['price'],
            ]);
        }
    }
}
