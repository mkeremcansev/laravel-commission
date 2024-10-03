<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Traits;

use Exception;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorFactory;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;

trait HasCommission
{
    /**
     * @throws Exception
     */
    public function calculate(): array
    {
        $service = new CommissionCalculatorService($this);
        $commissions = $service->getCalculableCommissions();

        return array_map(function (CommissionBundleContext $context) {
            return CommissionCalculatorFactory::make($context, $this)->calculate($this->{$context->column});
        }, $commissions);
    }
}
