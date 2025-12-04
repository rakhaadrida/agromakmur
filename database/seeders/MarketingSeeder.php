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
                'name' => 'Budi Santoso',
            ],
            [
                'name' => 'Rina Wijaya',
            ],
            [
                'name' => 'Agus Pratama',
            ],
        ];

        foreach ($marketings as $marketing) {
            Marketing::create([
                'name' => $marketing['name'],
            ]);
        }
    }
}
