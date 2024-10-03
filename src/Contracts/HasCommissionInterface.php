<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Contracts;

interface HasCommissionInterface
{
    public function getCommissionableColumns(): array;
}
