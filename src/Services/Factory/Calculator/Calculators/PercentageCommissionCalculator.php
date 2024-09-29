<?php

namespace Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Calculators;

use Exception;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contexts\BaseCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contexts\PercentageCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contracts\CommissionCalculatorInterface;

class PercentageCommissionCalculator extends BaseCalculator implements CommissionCalculatorInterface
{
    /**
     * @throws Exception
     */
    public function calculate(int $amount): BaseCommissionCalculatorContext
    {
        $status = $this->status(amount: $amount);
        $reason = $this->reason(amount: $amount);

        $commissionAmount = $amount * ($this->commission->rate / 100);
        $totalAmount = $amount + $commissionAmount;

        if ($this->commission->is_total === true) {
            $totalCommissionAmountByHistory = (int) CommissionCalculateHistory::where([
                'model_id' => $this->model->id,
                'model_type' => get_class($this->model),
                'group_id' => $this->model->group_id,
            ])->sum('commission_amount');

            $commissionAmountWithoutHistoryAmount = ($totalCommissionAmountByHistory + $amount) * ($this->commission->rate / 100);
            $commissionAmount = $commissionAmountWithoutHistoryAmount + $totalCommissionAmountByHistory;
            $totalAmount = $amount + $commissionAmountWithoutHistoryAmount + $totalCommissionAmountByHistory;
        }

        $context = match ($status) {
            CommissionCalculateHistoryStatusEnum::FAILED => new PercentageCommissionCalculatorContext(
                commission: $this->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: 0,
                totalAmount: 0,
                rate: $this->commission->rate,
                status: $status,
                reason: $reason,
                groupId: $this->model->group_id,
                column: $this->model->current_calculation_column,
            ),
            CommissionCalculateHistoryStatusEnum::SUCCESS => new PercentageCommissionCalculatorContext(
                commission: $this->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: $commissionAmount,
                totalAmount: $totalAmount,
                rate: $this->commission->rate,
                status: $status,
                reason: $reason,
                groupId: $this->model->group_id,
                column: $this->model->current_calculation_column,
            ),
            default => throw new Exception("Invalid status: $status->value"),
        };

        $this->executePipeline($context);

        return $context;
    }
}
