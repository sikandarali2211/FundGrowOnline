<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvestmentPlan;

class InvestmentPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'GROWER',
                'entry_amount' => 10.00,
                'return_percentage' => 100.00,
                'total_return' => 10.00,
                'description' => 'Entry level plan with 100% return',
                'is_active' => true
            ],
            [
                'name' => 'BUILDER',
                'entry_amount' => 20.00,
                'return_percentage' => 30.00,
                'total_return' => 6.00,
                'description' => 'Popular plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'BLOOM',
                'entry_amount' => 40.00,
                'return_percentage' => 30.00,
                'total_return' => 12.00,
                'description' => 'Growth plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'MULTIPLIER',
                'entry_amount' => 60.00,
                'return_percentage' => 30.00,
                'total_return' => 18.00,
                'description' => 'Multiplier plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'ACCELERATOR',
                'entry_amount' => 100.00,
                'return_percentage' => 30.00,
                'total_return' => 30.00,
                'description' => 'Accelerator plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'CONTRIBUTOR',
                'entry_amount' => 200.00,
                'return_percentage' => 30.00,
                'total_return' => 60.00,
                'description' => 'Contributor plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'SUPPORTER',
                'entry_amount' => 400.00,
                'return_percentage' => 30.00,
                'total_return' => 120.00,
                'description' => 'Supporter plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'CATALYST',
                'entry_amount' => 600.00,
                'return_percentage' => 30.00,
                'total_return' => 180.00,
                'description' => 'Catalyst plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'CHAMPION',
                'entry_amount' => 1000.00,
                'return_percentage' => 30.00,
                'total_return' => 300.00,
                'description' => 'Champion plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'HARVESTER',
                'entry_amount' => 2000.00,
                'return_percentage' => 30.00,
                'total_return' => 600.00,
                'description' => 'Harvester plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'PIONEER',
                'entry_amount' => 5000.00,
                'return_percentage' => 30.00,
                'total_return' => 1500.00,
                'description' => 'Pioneer plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'VISIONARY',
                'entry_amount' => 10000.00,
                'return_percentage' => 30.00,
                'total_return' => 3000.00,
                'description' => 'Visionary plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'LEADER',
                'entry_amount' => 20000.00,
                'return_percentage' => 30.00,
                'total_return' => 6000.00,
                'description' => 'Leader plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'INNOVATOR',
                'entry_amount' => 50000.00,
                'return_percentage' => 30.00,
                'total_return' => 15000.00,
                'description' => 'Innovator plan with 30% return',
                'is_active' => true
            ],
            [
                'name' => 'GAME CHANGER',
                'entry_amount' => 100000.00,
                'return_percentage' => 30.00,
                'total_return' => 30000.00,
                'description' => 'Game changer plan with 30% return',
                'is_active' => true
            ]
        ];

        foreach ($plans as $plan) {
            InvestmentPlan::create($plan);
        }
    }
}








