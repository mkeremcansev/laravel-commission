<?php

namespace Mkeremcansev\LaravelCommission\Enums;

use Mkeremcansev\LaravelCommission\Traits\PoweredEnum;

enum CommissionCalculateHistoryReasonEnum: int
{
    use PoweredEnum;

    case CALCULATED = 1;
    case NOT_STARTED = 2;
    case ENDED = 3;
    case INACTIVE = 4;
    case OUT_OF_RANGE = 5;
}
