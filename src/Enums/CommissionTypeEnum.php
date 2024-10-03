<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Enums;

use Mkeremcansev\LaravelCommission\Traits\PoweredEnum;

enum CommissionTypeEnum: int
{
    use PoweredEnum;

    case FIXED = 1;
    case PERCENTAGE = 2;
}
