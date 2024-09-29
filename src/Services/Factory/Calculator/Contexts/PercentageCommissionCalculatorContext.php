<?php

namespace Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contexts;

use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;

class PercentageCommissionCalculatorContext extends BaseCommissionCalculatorContext
{
    public function __construct(
        public Commission $commission,
        public Model $model,
        public int $originalAmount,
        public int $commissionAmount,
        public int $totalAmount,
        public float $rate,
        public CommissionCalculateHistoryStatusEnum $status,
        public CommissionCalculateHistoryReasonEnum $reason,
        public string $groupId,
        public string $column
    )
    {

    }
}
