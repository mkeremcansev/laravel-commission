<?php

namespace Mkeremcansev\LaravelCommission\Tests\Fixtures\Enums;

use Mkeremcansev\LaravelCommission\Traits\PoweredEnum;

enum PoweredEnumTest: int
{
    use PoweredEnum;

    case ONE = 1;
    case TWO = 2;
    case THREE = 3;
}
