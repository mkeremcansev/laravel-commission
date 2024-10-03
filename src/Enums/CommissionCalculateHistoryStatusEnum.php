<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Enums;

use Mkeremcansev\LaravelCommission\Traits\PoweredEnum;

enum CommissionCalculateHistoryStatusEnum: int
{
    use PoweredEnum;

    case SUCCESS = 1;
    case FAILED = 2;
}
