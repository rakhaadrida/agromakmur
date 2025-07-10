<?php

namespace Database\Seeders;

use App\Models\Marketing;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        $marketings = [
            [
                'name' => 'Marketing X',
            ],
            [
                'name' => 'Marketing Y',
            ],
            [
                'name' => 'Marketing Z',
            ],
        ];

        foreach ($marketings as $marketing) {
            Marketing::create([
                'name' => $marketing['name'],
            ]);
        }
    }
}
