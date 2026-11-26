<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Greenhouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ExpenseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $monthlyPlan = [
            'gaz' => [900_000, 1_600_000],
            'svet' => [700_000, 1_300_000],
            'suv' => [200_000, 450_000],
            'ishchi_kuchi' => [1_500_000, 2_500_000],
            'urug_kochat' => [0, 600_000],
            'ogit' => [150_000, 500_000],
            'dori' => [0, 350_000],
            'texnika' => [0, 400_000],
        ];

        Greenhouse::all()->each(function (Greenhouse $greenhouse) use ($monthlyPlan) {
            if ($greenhouse->expenses()->exists()) {
                return;
            }

            for ($monthsAgo = 3; $monthsAgo >= 0; $monthsAgo--) {
                $month = Carbon::now()->subMonths($monthsAgo);

                foreach ($monthlyPlan as $category => [$min, $max]) {
                    if ($max === 0) {
                        continue;
                    }

                    Expense::create([
                        'greenhouse_id' => $greenhouse->id,
                        'category' => $category,
                        'amount' => random_int($min, $max),
                        'spent_on' => $month->copy()->setDay(random_int(3, 25)),
                    ]);
                }
            }
        });
    }
}
