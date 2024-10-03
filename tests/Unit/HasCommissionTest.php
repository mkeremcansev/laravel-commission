<?php

use Illuminate\Support\Facades\Schema;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;
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
        $commissionGroupId = Str::uuid()->toString();

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
                function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                    $context
                        ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                        ->commission->commission_type_id
                        ->toBe($percentageCommission->commission_type_id)
                        ->commission->id
                        ->toBe($percentageCommission->id)
                        ->commission->rate
                        ->toBe(10.00)
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
                        ->commission->commission_type_id
                        ->toBe($fixedCommission->commission_type_id)
                        ->commission->id
                        ->toBe($fixedCommission->id)
                        ->commission->amount
                        ->toBe(200)
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

    it('can calculate commissions with multiple columns', function() {
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
            ->toHaveCount(4)
            ->sequence(
                function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                    $context
                        ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                        ->commission->commission_type_id
                        ->toBe($percentageCommission->commission_type_id)
                        ->commission->id
                        ->toBe($percentageCommission->id)
                        ->commission->rate
                        ->toBe(10.00)
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
                        ->commission->commission_type_id
                        ->toBe($fixedCommission->commission_type_id)
                        ->commission->id
                        ->toBe($fixedCommission->id)
                        ->commission->amount
                        ->toBe(200)
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
                },
                function (Expectation|PercentageCommissionCalculatorContext $context) use ($percentageCommission, $model) {
                    $context
                        ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                        ->commission->commission_type_id
                        ->toBe($percentageCommission->commission_type_id)
                        ->commission->id
                        ->toBe($percentageCommission->id)
                        ->commission->rate
                        ->toBe(10.00)
                        ->model->id
                        ->toBe($model->id)
                        ->originalAmount
                        ->toBe(200)
                        ->commissionAmount
                        ->toBe(20)
                        ->totalAmount
                        ->toBe(220)
                        ->status
                        ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
                        ->reason
                        ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
                        ->includedPreviousCommissionAmount
                        ->toBe(0)
                        ->column
                        ->toBe('other_column');

                },
                function (Expectation|FixedCommissionCalculatorContext $context) use ($fixedCommission, $model) {
                    $context
                        ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                        ->commission->commission_type_id
                        ->toBe($fixedCommission->commission_type_id)
                        ->commission->id
                        ->toBe($fixedCommission->id)
                        ->commission->amount
                        ->toBe(200)
                        ->model->id
                        ->toBe($model->id)
                        ->originalAmount
                        ->toBe(200)
                        ->commissionAmount
                        ->toBe(200)
                        ->totalAmount
                        ->toBe(400)
                        ->status
                        ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
                        ->reason
                        ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
                        ->includedPreviousCommissionAmount
                        ->toBe(0)
                        ->column
                        ->toBe('other_column');
                }
            );
    });
});
