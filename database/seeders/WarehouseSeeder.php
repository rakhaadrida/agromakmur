<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Pusat',
                'address' => 'Selupu Rejang, Bengkulu'
            ],
            [
                'name' => 'Rejang Lebong',
                'address' => 'Jl. Dr. AK. Gani'
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'name' => $warehouse['name'],
                'address' => $warehouse['address']
            ]);
        }
    }
}
