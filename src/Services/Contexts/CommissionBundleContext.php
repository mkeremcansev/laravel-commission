<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Contexts;

use Mkeremcansev\LaravelCommission\Models\Commission;

class CommissionBundleContext
{
    public function __construct(public Commission $commission, public string $column, public string $commissionGroupId) {}
}
