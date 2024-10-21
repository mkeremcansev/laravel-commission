<?php

use Illuminate\Support\Facades\Schema;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionCalculationResultContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\FixedCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\PercentageCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Order;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;
use Pest\Expectation;

describe('calculate()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });
    it('can calculate commissions', function () {
        // Arrange:
        $model = new Product;

        $commissionType = CommissionType::factory()
            ->create();

        $percentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => true,
            ]);

        $fixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create([
                'amount' => 200,
                'status' => true,
            ]);

        // Inactive commission:
        Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'amount' => 300,
                'status' => false,
            ]);

        // Other commission type for other model:
        Commission::factory()
            ->withPercentageCommission()
            ->create([
                'amount' => 400,
                'status' => true,
            ]);

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ]);

        // Act & Assert:
        expect($model)
            ->calculate()
            ->toHaveCount(1)
            ->each(function (Expectation|CommissionCalculationResultContext $context) use ($percentageCommission, $fixedCommission, $model) {
                $context
                    ->toBeInstanceOf(CommissionCalculationResultContext::class)
                    ->totalCommissionAmount
                    ->toBe(210)
                    ->totalIncludedPreviousCommissionAmount
                    ->toBe(0)
                    ->totalAmount
                    ->toBe(310)
                    ->originalAmount
                    ->toBe(100)
                    ->contexts
                    ->toHaveCount(2)
                    ->sequence(
                        function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                            $context
                                ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                                ->commission->id
                                ->toBe($percentageCommission->id)
                                ->commission->rate
                                ->toBe(10.00)
                                ->commission->amount
                                ->toBeNull()
                                ->commission->commission_type_id
                                ->toBe($percentageCommission->commission_type_id)
                                ->model->id
                                ->toBe($model->id)
                                ->originalAmount
                                ->toBe(100)
                                ->commissionAmount
                                ->toBe(10)
                                ->totalAmount
                                ->toBe(110)
                                ->status
                                ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
                                ->reason
                                ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
                                ->includedPreviousCommissionAmount
                                ->toBe(0)
                                ->column
                                ->toBe('amount');
                        },
                        function (Expectation|FixedCommissionCalculatorContext $context) use ($fixedCommission, $model) {
                            $context
                                ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                                ->commission->id
                                ->toBe($fixedCommission->id)
                                ->commission->amount
                                ->toBe(200)
                                ->commission->rate
                                ->toBeNull()
                                ->commission->commission_type_id
                                ->toBe($fixedCommission->commission_type_id)
                                ->model->id
                                ->toBe($model->id)
                                ->originalAmount
                                ->toBe(100)
                                ->commissionAmount
                                ->toBe(200)
                                ->totalAmount
                                ->toBe(300)
                                ->status
                                ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
                                ->reason
                                ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
                                ->includedPreviousCommissionAmount
                                ->toBe(0)
                                ->column
                                ->toBe('amount');
                        }
                    );
            });

        $this->assertDatabaseCount(CommissionCalculateHistory::class, 2);
    });

    it('can calculate commissions with multiple columns', function () {
        // Arrange:
        $model = new Order;

        $commissionType = CommissionType::factory()
            ->create();

        $percentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => true,
            ]);

        $fixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create([
                'amount' => 200,
                'status' => true,
            ]);

        // Inactive commission:
        Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'amount' => 300,
                'status' => false,
            ]);

        // Other commission type for other model:
        Commission::factory()
            ->withPercentageCommission()
            ->create([
                'amount' => 400,
                'status' => true,
            ]);

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ]);

        // Act & Assert:
        expect($model)
            ->calculate()
            ->toHaveCount(2)
            ->sequence(
                function (Expectation|CommissionCalculationResultContext $context) use ($percentageCommission, $fixedCommission, $model) {
                    $context
                        ->toBeInstanceOf(CommissionCalculationResultContext::class)
                        ->totalCommissionAmount
                        ->toBe(210)
                        ->totalIncludedPreviousCommissionAmount
                        ->toBe(0)
                        ->totalAmount
                        ->toBe(310)
                        ->originalAmount
                        ->toBe(100)
                        ->column
                        ->toBe('amount')
                        ->contexts
                        ->sequence(
                            function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                                $context
                                    ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                                    ->commission->id
                                    ->toBe($percentageCommission->id)
                                    ->column
                                    ->toBe('amount')
                                    ->model->id
                                    ->toBe($model->id);
                            },
                            function (Expectation|FixedCommissionCalculatorContext $context) use ($fixedCommission, $model) {
                                $context
                                    ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                                    ->commission->id
                                    ->toBe($fixedCommission->id)
                                    ->column
                                    ->toBe('amount')
                                    ->model->id
                                    ->toBe($model->id);
                            },
                        );
                },
                function (Expectation|CommissionCalculationResultContext $context) use ($percentageCommission, $fixedCommission, $model) {
                    $context
                        ->toBeInstanceOf(CommissionCalculationResultContext::class)
                        ->totalCommissionAmount
                        ->toBe(220)
                        ->totalIncludedPreviousCommissionAmount
                        ->toBe(0)
                        ->totalAmount
                        ->toBe(420)
                        ->originalAmount
                        ->toBe(200)
                        ->column
                        ->toBe('other_column')
                        ->contexts
                        ->sequence(
                            function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                                $context
                                    ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                                    ->commission->id
                                    ->toBe($percentageCommission->id)
                                    ->column
                                    ->toBe('other_column')
                                    ->model->id
                                    ->toBe($model->id);
                            },
                            function (Expectation|FixedCommissionCalculatorContext $context) use ($fixedCommission, $model) {
                                $context
                                    ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                                    ->commission->id
                                    ->toBe($fixedCommission->id)
                                    ->column
                                    ->toBe('other_column')
                                    ->model->id
                                    ->toBe($model->id);
                            },
                        );
                }
            );

        $this->assertDatabaseCount(CommissionCalculateHistory::class, 4);
    });

    it('can calculate commissions with multiple columns but return only incoming parameter column', function () {
        // Arrange:
        $model = new Order;

        $commissionType = CommissionType::factory()
            ->create();

        $percentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => true,
            ]);

        $fixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create([
                'amount' => 200,
                'status' => true,
            ]);

        // Inactive commission:
        Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'amount' => 300,
                'status' => false,
            ]);

        // Other commission type for other model:
        Commission::factory()
            ->withPercentageCommission()
            ->create([
                'amount' => 400,
                'status' => true,
            ]);

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ]);

        // Act:
        $result = $model->calculate('amount');

        // Assert:
        expect($result)
            ->toBeInstanceOf(CommissionCalculationResultContext::class)
            ->totalCommissionAmount
            ->toBe(210)
            ->totalIncludedPreviousCommissionAmount
            ->toBe(0)
            ->totalAmount
            ->toBe(310)
            ->originalAmount
            ->toBe(100)
            ->column
            ->toBe('amount')
            ->contexts
            ->sequence(
                function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                    $context
                        ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                        ->commission->id
                        ->toBe($percentageCommission->id)
                        ->column
                        ->toBe('amount')
                        ->model->id
                        ->toBe($model->id);
                },
                function (Expectation|FixedCommissionCalculatorContext $context) use ($fixedCommission, $model) {
                    $context
                        ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                        ->commission->id
                        ->toBe($fixedCommission->id)
                        ->column
                        ->toBe('amount')
                        ->model->id
                        ->toBe($model->id);
                },
            );

        $this->assertDatabaseCount(CommissionCalculateHistory::class, 2);
    });

    it('can calculate commissions with custom amount and return only incoming parameter column', function () {
        // Arrange:
        $model = new Order;

        $commissionType = CommissionType::factory()
            ->create();

        $percentageCommission = Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => true,
            ]);

        $fixedCommission = Commission::factory()
            ->for($commissionType)
            ->withFixedCommission()
            ->create([
                'amount' => 200,
                'status' => true,
            ]);

        // Inactive commission:
        Commission::factory()
            ->for($commissionType)
            ->withPercentageCommission()
            ->create([
                'amount' => 300,
                'status' => false,
            ]);

        // Other commission type for other model:
        Commission::factory()
            ->withPercentageCommission()
            ->create([
                'amount' => 400,
                'status' => true,
            ]);

        CommissionTypeModel::factory()
            ->for($commissionType)
            ->create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ]);

        // Act:
        $result = $model->calculate('amount', 100);

        // Assert:
        expect($result)
            ->toBeInstanceOf(CommissionCalculationResultContext::class)
            ->totalCommissionAmount
            ->toBe(210)
            ->totalIncludedPreviousCommissionAmount
            ->toBe(0)
            ->totalAmount
            ->toBe(310)
            ->originalAmount
            ->toBe(100)
            ->column
            ->toBe('amount')
            ->contexts
            ->sequence(
                function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                    $context
                        ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                        ->commission->id
                        ->toBe($percentageCommission->id)
                        ->column
                        ->toBe('amount')
                        ->model->id
                        ->toBe($model->id);
                },
                function (Expectation|FixedCommissionCalculatorContext $context) use ($fixedCommission, $model) {
                    $context
                        ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                        ->commission->id
                        ->toBe($fixedCommission->id)
                        ->column
                        ->toBe('amount')
                        ->model->id
                        ->toBe($model->id);
                },
            );
    });

    it('cannot calculate because custom amount is provided but column name is not provided', function () {
        // Arrange:
        $model = new Order;

        // Act & Assert:
        expect(function () use ($model) {
            $model->calculate(null, 100);
        })->toThrow(Exception::class, 'Column name must be provided when custom amount is provided.');
    });
});
