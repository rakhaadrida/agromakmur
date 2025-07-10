<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Customer A',
                'address' => 'Senayan, Jakarta Pusat',
                'contact_number' => '08123456789',
                'tax_number' => '228877554433',
                'credit_limit' => 10000000,
                'tempo' => 60,
                'marketing_id' => 2
            ],
            [
                'name' => 'Customer B',
                'address' => 'Cempakah Putih, Jakarta Timur',
                'contact_number' => '08987654321',
                'tax_number' => null,
                'credit_limit' => 50000000,
                'tempo' => 0,
                'marketing_id' => 1
            ],
            [
                'name' => 'Customer C',
                'address' => 'Garut, Jawa Barat',
                'contact_number' => '08561237894',
                'tax_number' => '0055663338877',
                'credit_limit' => 0,
                'tempo' => 30,
                'marketing_id' => 3
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer['name'],
                'address' => $customer['address'],
                'contact_number' => $customer['contact_number'],
                'tax_number' => $customer['tax_number'],
                'credit_limit' => $customer['credit_limit'],
                'tempo' => $customer['tempo'],
                'marketing_id' => $customer['marketing_id'],
            ]);
        }
    }
}
