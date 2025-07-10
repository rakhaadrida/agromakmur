<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Supplier A',
                'address' => 'Bandung, Jawa Barat',
                'contact_number' => '08123456789',
                'tax_number' => '228877554433',
            ],
            [
                'name' => 'Supplier B',
                'address' => 'Klaten, Jawa Tengah',
                'contact_number' => '08987654321',
                'tax_number' => null,
            ],
            [
                'name' => 'Supplier C',
                'address' => 'Palembang, Sumatera Selatan',
                'contact_number' => '08561237894',
                'tax_number' => '0055663338877',
            ],
        ];

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
