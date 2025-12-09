<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,
            MarketingSeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            WarehouseSeeder::class,
            PriceSeeder::class,
            CategorySeeder::class,
            SubcategorySeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
            ProductPriceSeeder::class,
            ProductStockSeeder::class,
            UserBranchSeeder::class,
            BranchWarehouseSeeder::class,
            GoodsReceiptSeeder::class,
            SalesOrderSeeder::class
        ]);
    }
}
