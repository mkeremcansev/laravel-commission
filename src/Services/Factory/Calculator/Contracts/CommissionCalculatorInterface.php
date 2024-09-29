<?php

namespace Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contracts;

use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contexts\BaseCommissionCalculatorContext;

interface CommissionCalculatorInterface
{
    public function calculate(int $amount): BaseCommissionCalculatorContext;
}
