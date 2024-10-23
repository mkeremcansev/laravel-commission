<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Contexts;

use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;

class FixedCommissionCalculatorContext extends BaseCommissionCalculatorContext
{
    public function __construct(
        public Commission $commission,
        public Model $model,
        public int|float $originalAmount,
        public int|float $commissionAmount,
        public int|float $totalAmount,
        public CommissionCalculateHistoryStatusEnum $status,
        public CommissionCalculateHistoryReasonEnum $reason,
        public string $groupId,
        public string $column,
        public int|float $includedPreviousCommissionAmount,
    ) {}
}
