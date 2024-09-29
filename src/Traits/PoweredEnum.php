<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Traits;

trait PoweredEnum
{
    public function is(self $enum): bool
    {
        return $this === $enum;
    }

    public function isNot(self $enum): bool
    {
        return $this !== $enum;
    }

    public function in(iterable $values): bool
    {
        foreach ($values as $value) {
            if ($value instanceof self && $this->is($value)) {
                return true;
            }

            if ($this->value === $value) {
                return true;
            }
        }

        return false;
    }
}
