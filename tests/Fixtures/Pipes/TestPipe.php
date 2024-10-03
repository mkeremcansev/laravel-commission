<?php

namespace Mkeremcansev\LaravelCommission\Tests\Fixtures\Pipes;

use Closure;
use Mkeremcansev\LaravelCommission\Services\Contexts\BaseCommissionCalculatorContext;

class TestPipe
{
    public function handle(BaseCommissionCalculatorContext $commissionCalculatorContext, Closure $next)
    {
        return $next($commissionCalculatorContext);
    }
}
