<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = json_decode(file_get_contents(database_path('seeders/json/customers.json')), true);

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
