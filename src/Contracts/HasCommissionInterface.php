<?php

namespace Mkeremcansev\LaravelCommission\Contracts;

interface HasCommissionInterface
{
    public function getCommissionableColumns(): array;
}
