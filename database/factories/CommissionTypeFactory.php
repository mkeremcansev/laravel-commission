<?php

namespace Mkeremcansev\LaravelCommission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mkeremcansev\LaravelCommission\Models\CommissionType;

/**
 * @extends Factory<CommissionType>
 */
class CommissionTypeFactory extends Factory
{
    protected $model = CommissionType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
        ];
    }
}
