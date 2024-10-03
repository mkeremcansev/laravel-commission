<?php

use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Services\Calculators\FixedCommissionCalculator;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\FixedCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Pipes\CreateHistoryPipe;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;

describe('calculate()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });
    it('can return fixed calculated commission with success status', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create([
                'amount' => 100,
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

        $context = (new FixedCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(100)
            ->totalAmount
            ->toBe(200)
            ->includedPreviousCommissionAmount
            ->toBe(0)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('can return fixed non-calculated commission with failed status', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create([
                'amount' => 100,
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

        $context = (new FixedCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
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
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::INACTIVE)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('returns successful status with fixed calculated commission and total including previous commissions', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create([
                'amount' => 100,
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

        $context = (new FixedCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
            ->commission
            ->toBe($commission)
            ->model
            ->toBe($model)
            ->originalAmount
            ->toBe(100)
            ->commissionAmount
            ->toBe(100)
            ->totalAmount
            ->toBe(500)
            ->includedPreviousCommissionAmount
            ->toBe(300)
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });

    it('returns failed status with fixed non-calculated commission and total including previous commissions', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create([
                'amount' => 100,
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

        $context = (new FixedCommissionCalculator($bundleContext, $model))
            ->calculate(100);

        // Assert:
        expect($context)
            ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
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
            ->status
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED)
            ->reason
            ->toBe(CommissionCalculateHistoryReasonEnum::INACTIVE)
            ->groupId
            ->toBe($commissionGroupId)
            ->column
            ->toBe('amount');
    });
});
