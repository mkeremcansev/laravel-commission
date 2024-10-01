<?php

use Illuminate\Support\Facades\Schema;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;
use Pest\Expectation;

describe('getCommissionsWithColumn()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });
    it('returns commissions for custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;

        $expectedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        Commission::factory()
            ->withPercentageCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model, true)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount')
            ->toBeArray()
            ->each(function (Expectation|CommissionBundleContext $context) use ($commissionType, $expectedCommission) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount')
                    ->commission->commission_type_id
                    ->toBe($commissionType->id)
                    ->commission->id
                    ->toBe($expectedCommission->id);
            });
    });
    it('returns commissions for non-custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;

        $expectedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        Commission::factory()
            ->withPercentageCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount')
            ->toBeArray()
            ->each(function (Expectation|CommissionBundleContext $context) use ($commissionType, $expectedCommission) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount')
                    ->commission->commission_type_id
                    ->toBe($commissionType->id)
                    ->commission->id
                    ->toBe($expectedCommission->id);
            });
    });
    it('does not return commissions with different model_id for custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;

        Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create([
                'model_id' => 100,
            ]);

        // Act && Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount')
            ->toBeArray()
            ->toBeEmpty();
    });
    it('does not return commissions with custom model has no commissions', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount')
            ->toBeArray()
            ->toBeEmpty();
    });
    it('returns multiple commissions for custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;

        $expectedFixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        $expectedPercentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create();

        Commission::factory()
            ->withPercentageCommission()
            ->create();

        Commission::factory()
            ->withFixedCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model, true)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount')
            ->toBeArray()
            ->toHaveCount(2)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id),
            )
            ->each(function (Expectation|CommissionBundleContext $context) use ($expectedFixedCommission, $expectedPercentageCommission) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount');
            });
    });
    it('returns multiple commissions for non-custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;

        $expectedFixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        $expectedPercentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create();

        Commission::factory()
            ->withPercentageCommission()
            ->create();

        Commission::factory()
            ->withFixedCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount')
            ->toBeArray()
            ->toHaveCount(2)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id),
            )
            ->each(function (Expectation|CommissionBundleContext $context) use ($expectedFixedCommission, $expectedPercentageCommission) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount');
            });
    });
});
