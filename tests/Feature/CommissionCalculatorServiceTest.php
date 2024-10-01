<?php

use Illuminate\Support\Facades\Schema;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;

describe('getCommissionsWithColumn()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });
    it('returns commissions for custom model type', function () {
        # Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product();

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

        # Act:
        $service = new CommissionCalculatorService($model);
        $result = $service->getCommissionsWithColumn('amount');

        # Assert:
        expect($result)->toBeArray();

        foreach ($result as $commissionBundleContext) {
            $commission = $commissionBundleContext->commission;
            $column = $commissionBundleContext->column;

            expect($commissionBundleContext)
                ->toBeInstanceOf(CommissionBundleContext::class)
                ->and($commission)
                ->toBeInstanceOf(Commission::class)
                ->and($column)
                ->toBe('amount')
                ->and($commission->commission_type_id)
                ->toBe($commissionType->id)
                ->and($commission->id)
                ->toBe($expectedCommission->id);
        }
    });
    it('returns commissions for non-custom model type', function () {
        # Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product();

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

        # Act:
        $service = new CommissionCalculatorService($model);
        $result = $service->getCommissionsWithColumn('amount');

        # Assert:
        expect($result)->toBeArray();

        foreach ($result as $commissionBundleContext) {
            $commission = $commissionBundleContext->commission;
            $column = $commissionBundleContext->column;

            expect($commissionBundleContext)
                ->toBeInstanceOf(CommissionBundleContext::class)
                ->and($commission)
                ->toBeInstanceOf(Commission::class)
                ->and($column)
                ->toBe('amount')
                ->and($commission->commission_type_id)
                ->toBe($commissionType->id)
                ->and($commission->id)
                ->toBe($expectedCommission->id);
        }
    });
    it('does not return commissions with different model_id for custom model type', function () {
        # Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product();

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

        # Act:
        $service = new CommissionCalculatorService($model);
        $result = $service->getCommissionsWithColumn('amount');

        # Assert:
        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });
    it('does not return commissions with custom model has no commissions', function () {
        # Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create();

        # Act:
        $service = new CommissionCalculatorService($model);
        $result = $service->getCommissionsWithColumn('amount');

        # Assert:
        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });
    it('returns multiple commissions for custom model type', function () {
        # Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product();

        $expectedFixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        $expectedPercentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model, true)
            ->create();

        # Act:
        $service = new CommissionCalculatorService($model);
        $result = $service->getCommissionsWithColumn('amount');

        # Assert:
        expect($result)->toBeArray()
            ->and($result)
            ->toHaveCount(2)
            ->and($result)
            ->each
            ->toBeInstanceOf(CommissionBundleContext::class);

        $resultCommissions = collect($result)->pluck('commission.id');

        expect($resultCommissions)
            ->toContain($expectedFixedCommission->id)
            ->and($resultCommissions)
            ->toContain($expectedPercentageCommission->id);
    });
    it('returns multiple commissions for non-custom model type', function () {
        # Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product();

        $expectedFixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create();

        $expectedPercentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create();

        # Act:
        $service = new CommissionCalculatorService($model);
        $result = $service->getCommissionsWithColumn('amount');

        # Assert:
        expect($result)->toBeArray()
            ->and($result)->toHaveCount(2)
            ->and($result)
            ->each
            ->toBeInstanceOf(CommissionBundleContext::class);

        $resultCommissions = collect($result)->pluck('commission.id');
        expect($resultCommissions)->toContain($expectedFixedCommission->id)
            ->and($resultCommissions)->toContain($expectedPercentageCommission->id);
    });
});
