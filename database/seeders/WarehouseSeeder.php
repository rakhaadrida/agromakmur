<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Utilities\Constant;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Warehouse E',
                'address' => 'Bandung, Jawa Barat',
                'type' => Constant::WAREHOUSE_TYPE_PRIMARY,
            ],
            [
                'name' => 'Warehouse F',
                'address' => 'Klaten, Jawa Tengah',
                'type' => Constant::WAREHOUSE_TYPE_SECONDARY,
            ],
            [
                'name' => 'Warehouse G',
                'address' => 'Palembang, Sumatera Selatan',
                'type' => Constant::WAREHOUSE_TYPE_SECONDARY,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'name' => $warehouse['name'],
                'address' => $warehouse['address'],
                'type' => $warehouse['type'],
            ]);
        }
    }
}
