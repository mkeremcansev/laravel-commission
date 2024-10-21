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
    public function calculate(?string $column = null, ?int $amount = null): CommissionCalculationResultContext|array|null
    {
        if ($column === null && $amount !== null) {
            throw new Exception('Column name must be provided when custom amount is provided.');
        }

        $service = new CommissionCalculatorService($this);
        $commissions = $service->getCalculableCommissions($column);

        $commissions = array_map(function (CommissionBundleContext $context) use ($amount) {
            return CommissionCalculatorFactory::make($context, $this)->calculate($amount ?? $this->{$context->column});
        }, $commissions);

        return (new CommissionCalculationResultContext($commissions))->get($column);
    }
}
