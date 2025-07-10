<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $subcategories = [
            [
                'name' => 'Subcategory X',
                'reminder_limit' => 50,
                'category_id' => 2
            ],
            [
                'name' => 'Subcategory Y',
                'reminder_limit' => 100,
                'category_id' => 1
            ],
            [
                'name' => 'Subcategory Z',
                'reminder_limit' => 30,
                'category_id' => 3
            ],
        ];

        foreach ($subcategories as $subcategory) {
            Subcategory::create([
                'name' => $subcategory['name'],
                'reminder_limit' => $subcategory['reminder_limit'],
                'category_id' => $subcategory['category_id'],
            ]);
        }
    }
}
