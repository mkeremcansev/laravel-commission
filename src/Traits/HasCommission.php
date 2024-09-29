<?php

namespace Mkeremcansev\LaravelCommission\Traits;

use Exception;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Factory\Calculator\CommissionCalculatorFactory;

trait HasCommission
{
    /**
     * @throws Exception
     */
    public function calculate(): array
    {
        $service = new CommissionCalculatorService($this);
        $commissionsByColumn = collect($service->getCalculableCommissions(columns: $this->getCommissionableColumns()));

        return $commissionsByColumn
            ->map(function ($commissionByColumn) {
                $commission = $commissionByColumn['commission'];
                $column = $commissionByColumn['column'];

                $calculator = CommissionCalculatorFactory::make($commission, $this);

                return $calculator->calculate($this->{$column});
            })
            ->toArray();
    }
}
