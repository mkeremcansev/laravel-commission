<?php

namespace Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Pipes;

use Closure;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\Contexts\BaseCommissionCalculatorContext;

class CreateHistoryPipe
{
    public function handle(BaseCommissionCalculatorContext $commissionCalculatorContext, Closure $next)
    {
        $model = $commissionCalculatorContext->model;

        $commissionCalculatorContext->commission->histories()->create([
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'group_id' => $commissionCalculatorContext->groupId,
            'column' => $model->current_calculation_column,
            'original_amount' => $model->amount,
            'calculated_amount' => $commissionCalculatorContext->totalAmount,
            'commission_amount' => $commissionCalculatorContext->commissionAmount,
            'status' => $commissionCalculatorContext->status,
            'reason' => $commissionCalculatorContext->reason,
        ]);

        return $next($commissionCalculatorContext);
    }
}
