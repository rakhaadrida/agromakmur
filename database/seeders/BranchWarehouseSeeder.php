<?php

namespace Database\Seeders;

use App\Models\BranchWarehouse;
use Illuminate\Database\Seeder;

class BranchWarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $branchWarehouses = [
            [
                'branch_id' => 1,
                'warehouse_id' => 1,
            ],
            [
                'branch_id' => 1,
                'warehouse_id' => 3,
            ],
            [
                'branch_id' => 2,
                'warehouse_id' => 2,
            ],
            [
                'branch_id' => 2,
                'warehouse_id' => 3,
            ],
        ];

        foreach ($branchWarehouses as $branchWarehouse) {
            BranchWarehouse::create([
                'branch_id' => $branchWarehouse['branch_id'],
                'warehouse_id' => $branchWarehouse['warehouse_id'],
            ]);
        }
    }
}
