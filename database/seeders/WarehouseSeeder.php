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
                'name' => 'Pusat',
                'address' => 'Selupu Rejang, Bengkulu',
                'type' => Constant::WAREHOUSE_TYPE_PRIMARY,
            ],
            [
                'name' => 'Rejang Lebong',
                'address' => 'Jl. Dr. AK. Gani',
                'type' => Constant::WAREHOUSE_TYPE_SECONDARY,
            ],
            [
                'name' => 'Cadangan',
                'address' => 'Lubuk Linggau',
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
