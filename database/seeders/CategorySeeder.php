<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'AKARISIDA',
            ],
            [
                'name' => 'ALAT PERTANIAN',
            ],
            [
                'name' => 'ASAM AMINO',
            ],
            [
                'name' => 'BENIH',
            ],
            [
                'name' => 'DEKOMPOSER',
            ],
            [
                'name' => 'FUNGISIDA',
            ],
            [
                'name' => 'HERBISIDA',
            ],
            [
                'name' => 'INSEKTISIDA',
            ],
            [
                'name' => 'MOLUSKISIDA',
            ],
            [
                'name' => 'PEREKAT',
            ],
            [
                'name' => 'PLASTIK MULSA',
            ],
            [
                'name' => 'PUPUK CAIR',
            ],
            [
                'name' => 'PUPUK',
            ],
            [
                'name' => 'PUPUK MIKRO',
            ],
            [
                'name' => 'SPRAYER',
            ],
            [
                'name' => 'ZPT',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
            ]);
        }
    }
}
