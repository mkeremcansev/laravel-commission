<?php

namespace Mkeremcansev\LaravelCommission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Mkeremcansev\LaravelCommission\Models\CommissionTypeModel>
 */
class CommissionTypeModelFactory extends Factory
{
    protected $model = CommissionTypeModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commission_type_id' => CommissionType::factory()->lazy(),
        ];
    }

    public function withModel(Model $model, bool $isCustom = false)
    {
        return $this->state([
            'model_id' => $isCustom ? $model->id : null,
            'model_type' => get_class($model),
        ]);
    }
}
