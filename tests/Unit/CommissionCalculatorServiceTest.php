<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Order;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;
use Pest\Expectation;

describe('getCommissionsWithColumn()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });

    it('can returns commissions for custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();

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
            ->getCommissionsWithColumn('amount', $commissionGroupId)
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
    it('can returns commissions for non-custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();

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
            ->getCommissionsWithColumn('amount', $commissionGroupId)
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
    it('can not return commissions with different model_id for custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();

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
            ->getCommissionsWithColumn('amount', $commissionGroupId)
            ->toBeArray()
            ->toBeEmpty();
    });
    it('can not return commissions with custom model has no commissions', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($model)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCommissionsWithColumn('amount', $commissionGroupId)
            ->toBeArray()
            ->toBeEmpty();
    });
    it('can returns multiple commissions for custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();

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
            ->getCommissionsWithColumn('amount', $commissionGroupId)
            ->toBeArray()
            ->toHaveCount(2)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id),
            )
            ->each(function (Expectation|CommissionBundleContext $context) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount');
            });
    });
    it('can returns multiple commissions for non-custom model type', function () {
        // Arrange:
        $commissionType = CommissionType::factory()->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();

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
            ->getCommissionsWithColumn('amount', $commissionGroupId)
            ->toBeArray()
            ->toHaveCount(2)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id),
            )
            ->each(function (Expectation|CommissionBundleContext $context) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount');
            });
    });
});

describe('validateColumnsExistence()', function () {
    it('can throws exception if columns do not exist', function () {
        // Arrange:
        $model = new Product;

        Schema::shouldReceive('hasColumn')
            ->andReturn(false);

        // Act & Assert:
        expect(fn () => (new CommissionCalculatorService($model))
            ->validateColumnsExistence(['amount'])
        )->toThrow(Exception::class, "Column amount does not exist in table {$model->getTable()}");
    });
    it('can throws exception if column is not numeric', function () {
        // Arrange:
        $model = new Product;

        Schema::shouldReceive('hasColumn')
            ->andReturn(true);

        // Act & Assert:
        expect(fn () => (new CommissionCalculatorService($model))
            ->validateColumnsExistence(['fakeColumn'])
        )->toThrow(Exception::class, 'Column fakeColumn is not numeric');
    });
});

describe('getCalculableCommissions()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });

    it('can returns commissions for multiple columns', function () {
        // Arrange:
        $model = new Product;
        $commissionType = CommissionType::factory()->create();

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

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCalculableCommissions()
            ->toBeArray()
            ->toHaveCount(2)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id),
            )
            ->each(function (Expectation|CommissionBundleContext $context) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class)
                    ->column
                    ->toBe('amount');
            });
    });

    it('can returns empty array if there are no commissions', function () {
        // Arrange:
        $model = new Product;

        // Act & Assert:
        expect(new CommissionCalculatorService($model))
            ->getCalculableCommissions()
            ->toBeEmpty();
    });

    it('can handles multiple columns with different commissions', function () {
        // Arrange:
        $model = new Order;
        $commissionType = CommissionType::factory()->create();

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

        // Act:
        $service = new CommissionCalculatorService($model);
        $commissions = $service->getCalculableCommissions();

        // Assert:
        expect($commissions)
            ->toBeArray()
            ->toHaveCount(4)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id)->column->toBe('amount'),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id)->column->toBe('amount'),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id)->column->toBe('other_column'),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id)->column->toBe('other_column'),
            )
            ->each(function (Expectation|CommissionBundleContext $context) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class);
            });
    });

    it('can handle multiple columns with different commissions for different models', function () {
        // Arrange:
        $orderModel = new Order;
        $productModel = new Product;

        $commissionType = CommissionType::factory()->create();

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
            ->withModel($orderModel)
            ->create();

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->withModel($productModel)
            ->create();

        // Act & Assert:
        expect(new CommissionCalculatorService($orderModel))
            ->getCalculableCommissions()
            ->toBeArray()
            ->toHaveCount(4)
            ->sequence(
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id)->column->toBe('amount'),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id)->column->toBe('amount'),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedFixedCommission->id)->column->toBe('other_column'),
                fn (Expectation|CommissionBundleContext $e) => $e->commission->id->toBe($expectedPercentageCommission->id)->column->toBe('other_column'),
            )
            ->each(function (Expectation|CommissionBundleContext $context) {
                $context
                    ->toBeInstanceOf(CommissionBundleContext::class)
                    ->commission
                    ->toBeInstanceOf(Commission::class);
            });
    });

});
