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
                'name' => 'Utama',
                'address' => 'Jl. Raya Curup, Lubuk Linggau, Cawang Baru, Kec. Selupu Rejang, Bengkulu',
                'phone_number' => '082282393930',
            ],
            [
                'name' => 'Rejang Lebong',
                'address' => 'Jl. Dr. AK. Gani Kabupaten Rejang Lebong',
                'phone_number' => '082378961876',
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
