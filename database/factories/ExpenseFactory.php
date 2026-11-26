<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Greenhouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'greenhouse_id' => Greenhouse::factory(),
            'category' => fake()->randomElement(array_keys(Expense::CATEGORIES)),
            'amount' => fake()->randomFloat(2, 50000, 2000000),
            'spent_on' => fake()->dateTimeBetween('-3 months', 'now'),
            'note' => null,
        ];
    }
}
