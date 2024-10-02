<?php

namespace Mkeremcansev\LaravelCommission\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Contracts\HasCommissionInterface;
use Mkeremcansev\LaravelCommission\Traits\HasCommission;

class Order extends Model implements HasCommissionInterface
{
    use HasCommission;

    public int $id = 1;

    public int $amount = 100;

    public int $other_column = 200;

    public string $fakeColumn = 'fake';

    public function getCommissionableColumns(): array
    {
        return ['amount', 'other_column'];
    }
}
