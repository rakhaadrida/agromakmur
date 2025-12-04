<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = json_decode(file_get_contents(database_path('seeders/json/suppliers.json')), true);

        foreach ($suppliers as $supplier) {
            Supplier::create([
                'name' => $supplier['name'],
                'address' => $supplier['address'],
                'contact_number' => $supplier['contact_number'],
                'tax_number' => $supplier['tax_number'],
            ]);
        }
    }
}
