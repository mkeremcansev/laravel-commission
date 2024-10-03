<?php

namespace Mkeremcansev\LaravelCommission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<CommissionCalculateHistory>
 */
class CommissionCalculateHistoryFactory extends Factory
{

    protected $model = CommissionCalculateHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commission_id' => Commission::factory()->lazy(),
            'group_id' => $this->faker->uuid(),
            'column' => $this->faker->word(),
            'original_amount' => $this->faker->randomNumber(2),
            'calculated_amount' => $this->faker->randomNumber(2),
            'commission_amount' => $this->faker->randomNumber(2),
            'status' => CommissionCalculateHistoryStatusEnum::SUCCESS,
            'reason' => CommissionCalculateHistoryReasonEnum::CALCULATED,
        ];
    }

    public function withModel(Model $model)
    {
        return $this->state([
            'model_id' => $model->id,
            'model_type' => get_class($model),
        ]);
    }
}
