<?php

namespace Mkeremcansev\LaravelCommission\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\Calculators\FixedCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\Calculators\PercentageCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Services\Contracts\CommissionCalculatorInterface;

class CommissionCalculatorFactory
{
    /**
     * @throws Exception
     */
    public static function make(CommissionBundleContext $context, Model $model): CommissionCalculatorInterface
    {
        return match ($context->commission->type) {
            CommissionTypeEnum::FIXED => new FixedCommissionCalculator($context, $model),
            CommissionTypeEnum::PERCENTAGE => new PercentageCommissionCalculator($context, $model),
        };
    }
}
