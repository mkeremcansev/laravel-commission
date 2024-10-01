<?php

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
    public function calculate(int $amount): BaseCommissionCalculatorContext
    {
        $status = $this->status(amount: $amount);
        $reason = $this->reason(amount: $amount);

        $commissionAmount = $this->commission->amount;
        $totalAmount = $amount + $commissionAmount;

        if ($this->commission->is_total === true) {
            $totalCommissionAmountByHistory = (int) CommissionCalculateHistory::where([
                'model_id' => $this->model->id,
                'model_type' => get_class($this->model),
                'group_id' => $this->model->commission_group_id,
            ])->sum('commission_amount');

            $commissionAmount = $totalCommissionAmountByHistory + $this->commission->amount;
            $totalAmount = $amount + $totalCommissionAmountByHistory + $this->commission->amount;
        }

        $context = match ($status) {
            CommissionCalculateHistoryStatusEnum::FAILED => new FixedCommissionCalculatorContext(
                commission: $this->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: 0,
                totalAmount: 0,
                status: $status,
                reason: $reason,
                groupId: $this->model->commission_group_id,
                column: $this->model->current_calculation_column,
            ),
            CommissionCalculateHistoryStatusEnum::SUCCESS => new FixedCommissionCalculatorContext(
                commission: $this->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: $commissionAmount,
                totalAmount: $totalAmount,
                status: $status,
                reason: $reason,
                groupId: $this->model->commission_group_id,
                column: $this->model->current_calculation_column,
            ),
            default => throw new Exception("Invalid status: $status->value"),
        };

        $this->executePipeline($context);

        return $context;
    }
}
