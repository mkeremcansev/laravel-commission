<?php

namespace Mkeremcansev\LaravelCommission\Enums;

use Mkeremcansev\LaravelCommission\Traits\PoweredEnum;

enum CommissionRoundingEnum: int
{
    use PoweredEnum;

    case UP = 1;
    case DOWN = 2;
}
