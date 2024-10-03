<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Traits;

use Exception;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorFactory;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionCalculationResultContext;

trait HasCommission
{
    /**
     * @throws Exception
     */
    public function calculate(?string $column = null): CommissionCalculationResultContext|array|null
    {
        $service = new CommissionCalculatorService($this);
        $commissions = $service->getCalculableCommissions();

        $commissions = array_map(function (CommissionBundleContext $context) {
            return CommissionCalculatorFactory::make($context, $this)->calculate($this->{$context->column});
        }, $commissions);

        return (new CommissionCalculationResultContext($commissions))->get($column);
    }
}
