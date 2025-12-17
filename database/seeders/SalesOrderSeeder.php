<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Database\Seeder;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        $salesOrders = json_decode(file_get_contents(database_path('seeders/json/sales_orders.json')), true);
        $salesOrderItems = json_decode(file_get_contents(database_path('seeders/json/sales_order_items.json')), true);

        foreach ($salesOrders as $salesOrder) {
            $customer = Customer::query()
                ->where('name', $salesOrder['customer_name'])
                ->first();

            if(!$customer) {
                $customer = Customer::create([
                    'name' => $salesOrder['customer_name'],
                    'address' => $salesOrder['customer_address'],
                    'contact_number' => '-',
                    'tax_number' => null,
                    'credit_limit' => 10000000,
                    'tempo' => 0,
                    'marketing_id' => 1,
                ]);
            }

            $data = SalesOrder::create([
                'branch_id' => $salesOrder['branch_id'],
                'customer_id' => $customer->id,
                'marketing_id' => $salesOrder['marketing_id'],
                'number' => $salesOrder['number'],
                'date' => $salesOrder['date'],
                'delivery_date' => $salesOrder['delivery_date'],
                'tempo' => $salesOrder['tempo'],
                'is_taxable' => $salesOrder['is_taxable'],
                'type' => $salesOrder['type'],
                'note' => $salesOrder['note'],
                'subtotal' => $salesOrder['subtotal'],
                'tax_amount' => $salesOrder['tax_amount'],
                'grand_total' => $salesOrder['grand_total'],
                'status' => $salesOrder['status'],
                'delivery_status' => $salesOrder['delivery_status'],
                'user_id' => $salesOrder['user_id'],
                'is_printed' => 1
            ]);

            $number = $data->number;
            $items = array_values(array_filter($salesOrderItems, function($item) use($number) {
                return $item['sales_order_number'] === $number;
            }));

            foreach($items as $item) {
                $product = Product::query()
                    ->where('sku', $item['product_sku'])
                    ->first();

                if($product) {
                    $data->salesOrderItems()->create([
                        'product_id' => $product->id,
                        'warehouse_id' => $item['warehouse_id'],
                        'unit_id' => $product->unit_id,
                        'quantity' => $item['quantity'],
                        'actual_quantity' => $item['actual_quantity'],
                        'price_id' => $item['price_id'],
                        'price' => $item['price'],
                        'total' => $item['total']
                    ]);
                }
            }
        }
    }
}
