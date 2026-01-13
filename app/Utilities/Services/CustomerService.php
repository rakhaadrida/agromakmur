<?php

namespace App\Utilities\Services;

use App\Models\Customer;

class CustomerService
{
    public static function createDataFromSalesOrder($name, $marketingId)
    {
        $customer = Customer::create([
            'name' => $name,
            'address' => '-',
            'contact_number' => '-',
            'credit_limit' => 10000000,
            'tempo' => 0,
            'marketing_id' => $marketingId,
        ]);

        return $customer->id;
    }
}
