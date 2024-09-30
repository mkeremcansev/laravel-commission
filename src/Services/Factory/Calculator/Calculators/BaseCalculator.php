<?php

namespace Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Calculators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Pipeline;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contexts\BaseCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Pipes\CreateHistoryPipe;

abstract class BaseCalculator
{
    public function __construct(public Commission $commission, public Model $model) {}

    public function executePipeline(BaseCommissionCalculatorContext $context): void
    {
        Pipeline::send($context)
            ->through([
                CreateHistoryPipe::class,
                ...config('commission.pipes', []),
            ])
            ->thenReturn();
    }

    public function isStarted(): bool
    {
        if ($this->commission->start_date === null) {
            return true;
        }

        return $this->commission->start_date <= now();
    }

    public function isEnded(): bool
    {
        if ($this->commission->end_date === null) {
            return false;
        }

        return $this->commission->end_date <= now();
    }

    public function isActive(): bool
    {
        return $this->commission->status;
    }

    public function isInRange(int $amount): bool
    {
        if ($this->commission->min_amount === null && $this->commission->max_amount === null) {
            return true;
        }

        if ($this->commission->min_amount === null) {
            return $this->isAmountBelowMax(amount: $amount);
        }

        if ($this->commission->max_amount === null) {
            return $this->isAmountAboveMin(amount: $amount);
        }

        return $this->isAmountAboveMin(amount: $amount) && $this->isAmountBelowMax(amount: $amount);
    }

    public function isAmountAboveMin(int $amount): bool
    {
        if ($this->commission->min_amount === null) {
            return true;
        }

        return $this->commission->min_amount <= $amount;
    }

    public function isAmountBelowMax(int $amount): bool
    {
        if ($this->commission->max_amount === null) {
            return true;
        }

        return $amount <= $this->commission->max_amount;
    }

    public function status(int $amount): CommissionCalculateHistoryStatusEnum
    {
        if ($this->isActive() === true && $this->isStarted() === true && $this->isInRange(amount: $amount) === true && $this->isEnded() === false) {
            return CommissionCalculateHistoryStatusEnum::SUCCESS;
        }

        return CommissionCalculateHistoryStatusEnum::FAILED;
    }

    public function reason(int $amount): CommissionCalculateHistoryReasonEnum
    {
        if ($this->isActive() === false) {
            return CommissionCalculateHistoryReasonEnum::INACTIVE;
        }

        if ($this->isStarted() === false) {
            return CommissionCalculateHistoryReasonEnum::NOT_STARTED;
        }

        if ($this->isEnded() === true) {
            return CommissionCalculateHistoryReasonEnum::ENDED;
        }

        if ($this->isInRange(amount: $amount) === false) {
            return CommissionCalculateHistoryReasonEnum::OUT_OF_RANGE;
        }

        return CommissionCalculateHistoryReasonEnum::CALCULATED;
    }

    public function round(float $amount): int
    {
        return match ($this->commission->rounding) {
            CommissionRoundingEnum::UP => ceil($amount),
            CommissionRoundingEnum::DOWN => floor($amount),
            default => round($amount),
        };
    }
}
