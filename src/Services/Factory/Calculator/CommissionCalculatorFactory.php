<?php

namespace Mkeremcansev\LaravelCommission\Services\Factory\Calculator;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Calculators\FixedCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Calculators\PercentageCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contracts\CommissionCalculatorInterface;

class CommissionCalculatorFactory
{
    /**
     * @throws Exception
     */
    public static function make(Commission $commission, Model $model): CommissionCalculatorInterface
    {
        return match ($commission->type) {
            CommissionTypeEnum::FIXED => new FixedCommissionCalculator($commission, $model),
            CommissionTypeEnum::PERCENTAGE => new PercentageCommissionCalculator($commission, $model),
            default => throw new Exception('Invalid commission type'),
        };
    }
}
