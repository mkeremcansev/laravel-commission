<?php

use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\Calculators\FixedCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\Calculators\PercentageCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorFactory;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;

describe('make()', function () {
    it('can return fixed commission calculator instance', function () {
        // Arrange:
        $model = new Product;

        $commission = Commission::factory()
            ->withFixedCommission()
            ->create();

        // Act & Assert:
        expect(CommissionCalculatorFactory::make($commission, $model))
            ->toBeInstanceOf(FixedCommissionCalculator::class)
            ->commission->id
            ->toBe($commission->id)
            ->model->id->toBe($model->id)
            ->model->getCommissionableColumns()
            ->toBe($model->getCommissionableColumns());
    });

    it('can return percentage commission calculator instance', function () {
        // Arrange:
        $model = new Product;

        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create();

        // Act & Assert:
        expect(CommissionCalculatorFactory::make($commission, $model))
            ->toBeInstanceOf(PercentageCommissionCalculator::class)
            ->commission->id
            ->toBe($commission->id)
            ->model->id->toBe($model->id)
            ->model->getCommissionableColumns()
            ->toBe($model->getCommissionableColumns());
    });
});
