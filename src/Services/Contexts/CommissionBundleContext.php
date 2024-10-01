<?php

namespace Mkeremcansev\LaravelCommission\Services\Contexts;

use Mkeremcansev\LaravelCommission\Models\Commission;

class CommissionBundleContext
{
    public function __construct(public Commission $commission, public string $column) {}
}
