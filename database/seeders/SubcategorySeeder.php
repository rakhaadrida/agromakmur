<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $subcategories = json_decode(file_get_contents(database_path('seeders/json/subcategories.json')), true);

        foreach ($subcategories as $subcategory) {
            Subcategory::create([
                'name' => $subcategory['name'],
                'reminder_limit' => $subcategory['reminder_limit'],
                'category_id' => $subcategory['category_id'],
            ]);
        }
    }
}
