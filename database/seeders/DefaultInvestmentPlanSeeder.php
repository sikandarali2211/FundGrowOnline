<?php

namespace Database\Seeders;

use App\Models\InvestmentPlan;
use Illuminate\Database\Seeder;

class DefaultInvestmentPlanSeeder extends Seeder
{
    public function run(): void
    {
        InvestmentPlan::create([
            'name' => 'Grower Plan',
            'entry_amount' => 10,
            'return_percentage' => 0,
            'total_return' => 10,
            'description' => 'Default grower plan',
            'is_active' => true,
        ]);
        
        echo "Default investment plan created!\n";
    }
}