<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Calculators;

use Exception;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Services\Contexts\BaseCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\FixedCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contracts\CommissionCalculatorInterface;

class FixedCommissionCalculator extends BaseCalculator implements CommissionCalculatorInterface
{
    /**
     * @throws Exception
     */
    public function calculate(int|float $amount): BaseCommissionCalculatorContext
    {
        $status = $this->status(amount: $amount);
        $reason = $this->reason(amount: $amount);

        $commissionAmount = $this->context->commission->amount;
        $totalAmount = $amount + $commissionAmount;
        $includedPreviousCommissionAmount = 0;

        if ($this->context->commission->is_total === true) {
            $totalCommissionAmountByHistory = (int) CommissionCalculateHistory::where([
                'model_id' => $this->model->id,
                'model_type' => get_class($this->model),
                'group_id' => $this->context->commissionGroupId,
                'column' => $this->context->column,
            ])->sum('commission_amount');

            $commissionAmount = $this->context->commission->amount;
            $includedPreviousCommissionAmount = $totalCommissionAmountByHistory;
            $totalAmount = $amount + $totalCommissionAmountByHistory + $this->context->commission->amount;
        }

        $context = match ($status) {
            CommissionCalculateHistoryStatusEnum::FAILED => new FixedCommissionCalculatorContext(
                commission: $this->context->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: 0,
                totalAmount: 0,
                status: $status,
                reason: $reason,
                groupId: $this->context->commissionGroupId,
                column: $this->context->column,
                includedPreviousCommissionAmount: 0,
            ),
            CommissionCalculateHistoryStatusEnum::SUCCESS => new FixedCommissionCalculatorContext(
                commission: $this->context->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: $commissionAmount,
                totalAmount: $totalAmount,
                status: $status,
                reason: $reason,
                groupId: $this->context->commissionGroupId,
                column: $this->context->column,
                includedPreviousCommissionAmount: $includedPreviousCommissionAmount,
            ),
        };

        $this->executePipeline($context);

        return $context;
    }
}
