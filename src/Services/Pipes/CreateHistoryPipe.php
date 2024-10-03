<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Pipes;

use Closure;
use Mkeremcansev\LaravelCommission\Services\Contexts\BaseCommissionCalculatorContext;

class CreateHistoryPipe
{
    public function handle(BaseCommissionCalculatorContext $commissionCalculatorContext, Closure $next)
    {
        $model = $commissionCalculatorContext->model;

        $commissionCalculatorContext->commission->histories()->create([
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'group_id' => $commissionCalculatorContext->groupId,
            'column' => $commissionCalculatorContext->column,
            'original_amount' => $commissionCalculatorContext->originalAmount,
            'calculated_amount' => $commissionCalculatorContext->totalAmount,
            'commission_amount' => $commissionCalculatorContext->commissionAmount,
            'status' => $commissionCalculatorContext->status,
            'reason' => $commissionCalculatorContext->reason,
        ]);

        return $next($commissionCalculatorContext);
    }
}
