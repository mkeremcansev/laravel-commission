<?php

namespace Mkeremcansev\LaravelCommission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionType;

/**
 * @extends Factory<Commission>
 */
class CommissionFactory extends Factory
{
    protected $model = Commission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commission_type_id' => CommissionType::factory()->lazy(),
            'status' => true,
            'is_total' => false,
            'order' => 1,
            'rounding' => CommissionRoundingEnum::UP,
        ];
    }

    public function withFixedCommission(): self
    {
        return $this->state(fn (): array => [
            'type' => CommissionTypeEnum::FIXED,
            'amount' => 100,
        ]);
    }

    public function withPercentageCommission(): self
    {
        return $this->state(fn (): array => [
            'type' => CommissionTypeEnum::PERCENTAGE,
            'rate' => 10,
        ]);
    }

    protected function withStartDate(): self
    {
        return $this->state(fn (): array => [
            'start_date' => now(),
        ]);
    }

    protected function withEndDate(int $days = 1): self
    {
        return $this->state(fn (): array => [
            'end_date' => now()->addDays($days),
        ]);
    }

    protected function withMinAmount(): self
    {
        return $this->state(fn (): array => [
            'min_amount' => 100,
        ]);
    }

    protected function withMaxAmount(): self
    {
        return $this->state(fn (): array => [
            'max_amount' => 1000,
        ]);
    }

    protected function withTotalCommission(): self
    {
        return $this->state(fn (): array => [
            'is_total' => true,
        ]);
    }

    protected function withInactiveCommission(): self
    {
        return $this->state(fn (): array => [
            'status' => false,
        ]);
    }

    protected function withOrder(int $order): self
    {
        return $this->state(fn (): array => [
            'order' => $order,
        ]);
    }

    protected function withCommissionType(CommissionTypeEnum $type): self
    {
        return $this->state(fn (): array => [
            'type' => $type,
        ]);
    }

    protected function forCommissionTypeModel(CommissionType $commissionType): self
    {
        return $this->state(fn (): array => [
            'commission_type_id' => $commissionType->id,
        ]);
    }
}
