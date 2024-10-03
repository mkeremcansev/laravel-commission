<?php

namespace Mkeremcansev\LaravelCommission\Services\Calculators;

use Exception;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Services\Contexts\BaseCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\PercentageCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contracts\CommissionCalculatorInterface;

class PercentageCommissionCalculator extends BaseCalculator implements CommissionCalculatorInterface
{
    /**
     * @throws Exception
     */
    public function calculate(int $amount): BaseCommissionCalculatorContext
    {
        $status = $this->status(amount: $amount);
        $reason = $this->reason(amount: $amount);

        $commissionAmount = $amount * ($this->context->commission->rate / 100);
        $totalAmount = $amount + $commissionAmount;
        $includedPreviousCommissionAmount = 0;

        if ($this->context->commission->is_total === true) {
            $totalCommissionAmountByHistory = (int) CommissionCalculateHistory::where([
                'model_id' => $this->model->id,
                'model_type' => get_class($this->model),
                'group_id' => $this->context->commissionGroupId,
                'column' => $this->context->column,
            ])->sum('commission_amount');

            /**
             * Here we calculate the total amount that can be calculated
             * The total commission amount by history + the current amount
             */
            $totalCalculableAmount = $totalCommissionAmountByHistory + $amount;

            /**
             * Here we calculate the total commission amount
             * The total amount that can be calculated * the rate of the commission
             */
            $totalCommissionAmount = $totalCalculableAmount * ($this->context->commission->rate / 100);

            $commissionAmount = $totalCommissionAmount;

            $includedPreviousCommissionAmount = $totalCommissionAmountByHistory;

            /**
             * Here we calculate the total amount
             * The total amount + the total commission amount by history + the total commission amount
             */
            $totalAmount = $amount + $totalCommissionAmount + $totalCommissionAmountByHistory;
        }

        $commissionAmount = $this->round($commissionAmount);
        $totalAmount = $this->round($totalAmount);

        $context = match ($status) {
            CommissionCalculateHistoryStatusEnum::FAILED => new PercentageCommissionCalculatorContext(
                commission: $this->context->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: 0,
                totalAmount: 0,
                rate: $this->context->commission->rate,
                status: $status,
                reason: $reason,
                groupId: $this->context->commissionGroupId,
                column: $this->context->column,
                includedPreviousCommissionAmount: 0,
            ),
            CommissionCalculateHistoryStatusEnum::SUCCESS => new PercentageCommissionCalculatorContext(
                commission: $this->context->commission,
                model: $this->model,
                originalAmount: $amount,
                commissionAmount: $commissionAmount,
                totalAmount: $totalAmount,
                rate: $this->context->commission->rate,
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
