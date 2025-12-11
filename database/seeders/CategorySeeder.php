<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = json_decode(file_get_contents(database_path('seeders/json/categories.json')), true);

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'reminder_limit' => $category['reminder_limit'],
            ]);
        }
    }
}
