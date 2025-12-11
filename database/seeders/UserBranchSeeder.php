<?php

namespace Database\Seeders;

use App\Models\UserBranch;
use Illuminate\Database\Seeder;

class UserBranchSeeder extends Seeder
{
    public function run(): void
    {
        $userBranches = [
            [
                'user_id' => 2,
                'branch_id' => 1,
            ],
            [
                'user_id' => 3,
                'branch_id' => 2,
            ],
            [
                'user_id' => 4,
                'branch_id' => 1,
            ],
            [
                'user_id' => 5,
                'branch_id' => 2,
            ],
            [
                'user_id' => 6,
                'branch_id' => 1,
            ],
            [
                'user_id' => 7,
                'branch_id' => 2,
            ],
            [
                'user_id' => 8,
                'branch_id' => 1,
            ],
            [
                'user_id' => 9,
                'branch_id' => 2,
            ],
            [
                'user_id' => 10,
                'branch_id' => 1,
            ],
            [
                'user_id' => 11,
                'branch_id' => 2,
            ],
        ];

        foreach ($userBranches as $userBranch) {
            UserBranch::create([
                'user_id' => $userBranch['user_id'],
                'branch_id' => $userBranch['branch_id'],
            ]);
        }
    }
}
