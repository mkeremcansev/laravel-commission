<?php

use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Services\Calculators\PercentageCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\PercentageCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Pipes\CreateHistoryPipe;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;

describe('calculate()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });
    it('can return percentage calculated commission with success status', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => true,
                'is_total' => false,
            ]);

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act:
        Pipeline::shouldReceive('send')->once()->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn();

        $context = (new PercentageCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(10)
            ->totalAmount
            ->toBe(110)
            ->includedPreviousCommissionAmount
            ->toBe(0)
            ->rate
            ->toBe(10.00)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('can return percentage non-calculated commission with failed status', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => false,
                'is_total' => false,
            ]);

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act:
        Pipeline::shouldReceive('send')->once()->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn();

        $context = (new PercentageCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(0)
            ->totalAmount
            ->toBe(0)
            ->includedPreviousCommissionAmount
            ->toBe(0)
            ->rate
            ->toBe(10.00)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::INACTIVE)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('can return successful status with fixed calculated commission and total including previous commissions', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => true,
                'is_total' => true,
            ]);

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        CommissionCalculateHistory::factory()
            ->count(3)
            ->for($commission)
            ->withModel($model)
            ->create([
                'original_amount' => 100,
                'calculated_amount' => 100,
                'commission_amount' => 100,
                'status' => CommissionCalculateHistoryStatusEnum::SUCCESS,
                'reason' => CommissionCalculateHistoryReasonEnum::CALCULATED,
                'column' => 'amount',
                'group_id' => $commissionGroupId,
            ]);

        // Act:
        Pipeline::shouldReceive('send')->once()->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn();

        $context = (new PercentageCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(40)
            ->totalAmount
            ->toBe(440)
            ->includedPreviousCommissionAmount
            ->toBe(300)
            ->rate
            ->toBe(10.00)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('can return failed status with percentage non-calculated commission and total including previous commissions', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create([
                'rate' => 10.00,
                'status' => false,
                'is_total' => true,
            ]);

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        CommissionCalculateHistory::factory()
            ->count(3)
            ->for($commission)
            ->withModel($model)
            ->create([
                'original_amount' => 100,
                'calculated_amount' => 100,
                'commission_amount' => 100,
                'status' => CommissionCalculateHistoryStatusEnum::SUCCESS,
                'reason' => CommissionCalculateHistoryReasonEnum::CALCULATED,
                'column' => 'amount',
                'group_id' => $commissionGroupId,
            ]);

        // Act:
        Pipeline::shouldReceive('send')->once()->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn();

        $context = (new PercentageCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(0)
            ->totalAmount
            ->toBe(0)
            ->includedPreviousCommissionAmount
            ->toBe(0)
            ->rate
            ->toBe(10.00)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::INACTIVE)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('can return percentage calculated commission with success status and round up', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create([
                'rate' => 10.27,
                'status' => true,
                'is_total' => false,
                'rounding' => CommissionRoundingEnum::UP
            ]);

        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act:
        Pipeline::shouldReceive('send')->once()->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn();

        $context = (new PercentageCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(11)
            ->totalAmount
            ->toBe(111)
            ->includedPreviousCommissionAmount
            ->toBe(0)
            ->rate
            ->toBe(10.27)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });
});
