<?php

namespace Database\Seeders;

use App\Models\AccountPayable;
use App\Models\GoodsReceipt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AccountPayableSeeder extends Seeder
{
    public function run(): void
    {
        $accountPayables = json_decode(file_get_contents(database_path('seeders/json/account_payables.json')), true);

        foreach ($accountPayables as $accountPayable) {
            $goodsReceipt = GoodsReceipt::query()
                ->where('number', $accountPayable['goods_receipt_number'])
                ->first();

            if($goodsReceipt) {
                $data = AccountPayable::create([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'status' => $accountPayable['status'],
                ]);

                if($accountPayable['amount'] > 0) {
                    $data->payments()->create([
                        'date' => $accountPayable['date'],
                        'amount' => $accountPayable['amount']
                    ]);
                }
            } else {
                Log::info($accountPayable['goods_receipt_number']);
            }
        }
    }
}
