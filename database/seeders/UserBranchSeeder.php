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
                'user_id' => 2,
                'branch_id' => 2,
            ],
            [
                'user_id' => 3,
                'branch_id' => 1,
            ],
            [
                'user_id' => 3,
                'branch_id' => 2,
            ],
            [
                'user_id' => 4,
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
