<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'name' => 'Pcs',
            ],
            [
                'name' => 'Kg',
            ],
            [
                'name' => 'Dus',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
            ]);
        }
    }
}
