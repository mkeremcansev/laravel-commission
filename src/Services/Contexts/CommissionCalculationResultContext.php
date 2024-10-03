<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services\Contexts;

class CommissionCalculationResultContext
{
    public function __construct(
        public array $contexts,
        public int $totalCommissionAmount = 0,
        public int $totalIncludedPreviousCommissionAmount = 0,
        public int $totalAmount = 0,
        public int $originalAmount = 0,
        public string $column = ''
    ) {}

    public function result(): array
    {
        $groupedContexts = [];

        collect($this->contexts)->groupBy('column')->each(function ($childs, $column) use (&$groupedContexts) {
            $totalCommissionAmount = $childs->sum('commissionAmount');
            $totalIncludedPreviousCommissionAmount = $childs->sum('includedPreviousCommissionAmount');
            $originalAmount = $childs->sum('originalAmount') / $childs->count();
            $totalAmount = $totalCommissionAmount + $originalAmount;

            $groupedContexts[] = new self(
                contexts: $childs->toArray(),
                totalCommissionAmount: $totalCommissionAmount,
                totalIncludedPreviousCommissionAmount: $totalIncludedPreviousCommissionAmount,
                totalAmount: $totalAmount,
                originalAmount: $originalAmount,
                column: $column
            );
        });

        return $groupedContexts;
    }

    public function get(?string $column = null): CommissionCalculationResultContext|array|null
    {
        $results = $this->result();

        if (empty($results) === true) {
            return null;
        }

        if ($column === null) {
            return $results;
        }

        return collect($results)->first(fn (self $context) => $context->column === $column);
    }
}
