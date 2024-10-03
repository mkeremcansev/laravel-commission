<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Contracts;

use Mkeremcansev\LaravelCommission\Services\Contexts\BaseCommissionCalculatorContext;

interface CommissionCalculatorInterface
{
    public function calculate(int $amount): BaseCommissionCalculatorContext;
}
