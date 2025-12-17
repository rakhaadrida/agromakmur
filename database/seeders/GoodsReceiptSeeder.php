<?php

namespace Database\Seeders;

use App\Models\GoodsReceipt;
use App\Models\Product;
use Illuminate\Database\Seeder;

class GoodsReceiptSeeder extends Seeder
{
    public function run(): void
    {
        $goodsReceipts = json_decode(file_get_contents(database_path('seeders/json/goods_receipts.json')), true);
        $goodsReceiptItems = json_decode(file_get_contents(database_path('seeders/json/goods_receipt_items.json')), true);

        foreach ($goodsReceipts as $goodsReceipt) {
            $data = GoodsReceipt::create([
                'branch_id' => $goodsReceipt['branch_id'],
                'supplier_id' => $goodsReceipt['supplier_id'],
                'warehouse_id' => $goodsReceipt['warehouse_id'],
                'number' => $goodsReceipt['number'],
                'date' => $goodsReceipt['date'],
                'tempo' => $goodsReceipt['tempo'],
                'subtotal' => $goodsReceipt['subtotal'],
                'tax_amount' => $goodsReceipt['tax_amount'],
                'grand_total' => $goodsReceipt['grand_total'],
                'status' => $goodsReceipt['status'],
                'user_id' => $goodsReceipt['user_id'],
                'is_printed' => 1
            ]);

            $number = $data->number;
            $items = array_values(array_filter($goodsReceiptItems, function($item) use($number) {
                return $item['goods_receipt_number'] === $number;
            }));

            foreach($items as $item) {
                $product = Product::query()
                    ->where('sku', $item['product_sku'])
                    ->first();

                if($product) {
                    $data->goodsReceiptItems()->create([
                        'product_id' => $product->id,
                        'unit_id' => $product->unit_id,
                        'quantity' => $item['quantity'],
                        'actual_quantity' => $item['actual_quantity'],
                        'price' => $item['price'],
                        'wages' => $item['wages'],
                        'shipping_cost' => $item['shipping_cost'],
                        'cost_price' => $item['price'],
                        'total' => $item['total'],
                    ]);
                }
            }
        }
    }
}
