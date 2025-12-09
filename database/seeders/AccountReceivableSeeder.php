<?php

namespace Database\Seeders;

use App\Models\AccountReceivable;
use App\Models\SalesOrder;
use App\Utilities\Constant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AccountReceivableSeeder extends Seeder
{
    public function run(): void
    {
        $accountReceivables = json_decode(file_get_contents(database_path('seeders/json/account_receivables.json')), true);
        $payments = json_decode(file_get_contents(database_path('seeders/json/account_receivable_payments.json')), true);

        foreach ($accountReceivables as $accountReceivable) {
            $salesOrder = SalesOrder::query()
                ->where('number', $accountReceivable['sales_order_number'])
                ->first();

            if($salesOrder) {
                $data = AccountReceivable::create([
                    'sales_order_id' => $salesOrder->id,
                    'status' => $accountReceivable['status'],
                ]);

                if($accountReceivable['status'] == Constant::ACCOUNT_RECEIVABLE_STATUS_PAID) {
                    $data->payments()->create([
                        'date' => $accountReceivable['date'],
                        'amount' => $accountReceivable['amount']
                    ]);
                } else {
                    $items = array_values(array_filter($payments, function($item) use($accountReceivable) {
                        return $item['sales_order_number'] === $accountReceivable['sales_order_number'];
                    }));

                    $total = 0;
                    foreach($items as $item) {
                        $data->payments()->create([
                            'date' => $item['date'],
                            'amount' => $item['amount']
                        ]);

                        $total += $item['amount'];
                    }

                    if($total == $accountReceivable['grand_total']) {
                        $data->status = Constant::ACCOUNT_RECEIVABLE_STATUS_PAID;
                        $data->save();
                    } else if($total > 0) {
                        $data->status = Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
                        $data->save();
                    }
                }
            } else {
                Log::info($accountReceivable['sales_order_number']);
            }
        }
    }
}
