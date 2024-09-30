<?php

namespace Mkeremcansev\LaravelCommission\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Contracts\HasCommissionInterface;
use Mkeremcansev\LaravelCommission\Traits\HasCommission;

class Product extends Model implements HasCommissionInterface
{
    use HasCommission;

    public int $id = 1;

    public int $amount = 100;

    public function getCommissionableColumns(): array
    {
        return ['amount'];
    }
}
