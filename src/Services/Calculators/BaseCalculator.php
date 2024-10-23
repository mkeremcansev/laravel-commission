<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Calculators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Pipeline;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum;
use Mkeremcansev\LaravelCommission\Services\Contexts\BaseCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Services\Pipes\CreateHistoryPipe;

abstract class BaseCalculator
{
    public function __construct(public CommissionBundleContext $context, public Model $model) {}

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
        if ($this->context->commission->start_date === null) {
            return true;
        }

        return $this->context->commission->start_date <= now();
    }

    public function isEnded(): bool
    {
        if ($this->context->commission->end_date === null) {
            return false;
        }

        return $this->context->commission->end_date <= now();
    }

    public function isActive(): bool
    {
        return $this->context->commission->status;
    }

    public function isInRange(int|float $amount): bool
    {
        if ($this->context->commission->min_amount === null && $this->context->commission->max_amount === null) {
            return true;
        }

        if ($this->context->commission->min_amount === null) {
            return $this->isAmountBelowMax(amount: $amount);
        }

        if ($this->context->commission->max_amount === null) {
            return $this->isAmountAboveMin(amount: $amount);
        }

        return $this->isAmountAboveMin(amount: $amount) && $this->isAmountBelowMax(amount: $amount);
    }

    public function isAmountAboveMin(int|float $amount): bool
    {
        if ($this->context->commission->min_amount === null) {
            return true;
        }

        return $this->context->commission->min_amount <= $amount;
    }

    public function isAmountBelowMax(int|float $amount): bool
    {
        if ($this->context->commission->max_amount === null) {
            return true;
        }

        return $amount <= $this->context->commission->max_amount;
    }

    public function status(int|float $amount): CommissionCalculateHistoryStatusEnum
    {
        if ($this->isActive() === true && $this->isStarted() === true && $this->isInRange(amount: $amount) === true && $this->isEnded() === false) {
            return CommissionCalculateHistoryStatusEnum::SUCCESS;
        }

        return CommissionCalculateHistoryStatusEnum::FAILED;
    }

    public function reason(int|float $amount): CommissionCalculateHistoryReasonEnum
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

    public function round(int|float $amount): int|float
    {
        return match ($this->context->commission->rounding) {
            CommissionRoundingEnum::UP => ceil($amount),
            CommissionRoundingEnum::DOWN => floor($amount),
            CommissionRoundingEnum::NONE => is_float($amount)
                ? (floor($amount) == $amount ? (int) $amount : round($amount, 2))
                : $amount,
        };
    }
}
