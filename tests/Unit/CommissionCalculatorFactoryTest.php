<?php

use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorFactory;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;

describe('make()', function () {
    it('can return fixed commission calculator instance', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create();

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(CommissionCalculatorFactory::make($bundleContext, $model))
            ->context
            ->toBeInstanceOf(CommissionBundleContext::class)
            ->context->commission
            ->toBeInstanceOf(Commission::class)
            ->context->commission->id
            ->toBe($commission->id)
            ->model->id->toBe($model->id)
            ->model->getCommissionableColumns()
            ->toBe($model->getCommissionableColumns());
    });

    it('can return percentage commission calculator instance', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create();

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(CommissionCalculatorFactory::make($bundleContext, $model))
            ->context
            ->toBeInstanceOf(CommissionBundleContext::class)
            ->context->commission
            ->toBeInstanceOf(Commission::class)
            ->context->commission->id
            ->toBe($commission->id)
            ->model->id->toBe($model->id)
            ->model->getCommissionableColumns()
            ->toBe($model->getCommissionableColumns());
    });
});
