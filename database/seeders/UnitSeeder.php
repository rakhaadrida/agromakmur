<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = json_decode(file_get_contents(database_path('seeders/json/units.json')), true);

        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
            ]);
        }
    }
}
