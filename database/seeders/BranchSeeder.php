<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Main Branch',
                'address' => 'Solok, Sumatera Barat',
                'phone_number' => '021-74759320',
            ],
            [
                'name' => 'New Branch',
                'address' => 'Jambi',
                'phone_number' => '0812359542902',
            ],
            [
                'name' => 'Future Branch',
                'address' => 'Bengkulu',
                'phone_number' => '021-73820421',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create([
                'name' => $branch['name'],
                'address' => $branch['address'],
                'phone_number' => $branch['phone_number'],
            ]);
        }
    }
}
