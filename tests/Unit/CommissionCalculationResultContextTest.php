<?php

use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionCalculationResultContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\FixedCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\PercentageCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;
use Pest\Expectation;

describe('result()', function () {
    it('can return groupped columns with calculation amounts', function () {
        $model = new Product;
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create();

        $fixedCommissionCalculatorContext = new FixedCommissionCalculatorContext(
            commission: $commission,
            model: $model, originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 110,
            status: CommissionCalculateHistoryStatusEnum::SUCCESS,
            reason: CommissionCalculateHistoryReasonEnum::CALCULATED,
            groupId: 'fixed_commission',
            column: 'amount',
            includedPreviousCommissionAmount: 0
        );

        $percentageCommissionCalculatorContext = new PercentageCommissionCalculatorContext(
            commission: $commission,
            model: $model, originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 110,
            rate: 10.00,
            status: CommissionCalculateHistoryStatusEnum::SUCCESS,
            reason: CommissionCalculateHistoryReasonEnum::CALCULATED,
            groupId: 'percentage_commission',
            column: 'amount', includedPreviousCommissionAmount: 0
        );

        $context = new CommissionCalculationResultContext(
            contexts: [
                $fixedCommissionCalculatorContext,
                $percentageCommissionCalculatorContext,
            ],
        );

        // Act & Assert:
        expect($context->result())
            ->toBeArray()
            ->toHaveCount(1)
            ->sequence(
                function (Expectation|CommissionCalculationResultContext $context) {
                    $context
                        ->toBeInstanceOf(CommissionCalculationResultContext::class)
                        ->totalCommissionAmount
                        ->toBe(20)
                        ->totalIncludedPreviousCommissionAmount
                        ->toBe(0)
                        ->totalAmount
                        ->toBe(120)
                        ->originalAmount
                        ->toBe(100)
                        ->column
                        ->toBe('amount')
                        ->contexts
                        ->toBeArray()
                        ->toHaveCount(2)
                        ->sequence(
                            function (Expectation|FixedCommissionCalculatorContext $context) {
                                $context
                                    ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                                    ->commission
                                    ->toBeInstanceOf(Commission::class)
                                    ->model
                                    ->toBeInstanceOf(Product::class)
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
                                    ->groupId
                                    ->toBe('fixed_commission')
                                    ->column
                                    ->toBe('amount')
                                    ->includedPreviousCommissionAmount
                                    ->toBe(0);
                            },
                            function (Expectation|PercentageCommissionCalculatorContext $context) {
                                $context
                                    ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                                    ->commission
                                    ->toBeInstanceOf(Commission::class)
                                    ->model
                                    ->toBeInstanceOf(Product::class)
                                    ->originalAmount
                                    ->toBe(100)
                                    ->commissionAmount
                                    ->toBe(10)
                                    ->totalAmount
                                    ->toBe(110)
                                    ->rate
                                    ->toBe(10.00)
                                    ->status
                                    ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
                                    ->reason
                                    ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
                                    ->groupId
                                    ->toBe('percentage_commission')
                                    ->column
                                    ->toBe('amount')
                                    ->includedPreviousCommissionAmount
                                    ->toBe(0);
                            }
                        );
                }
            );
    });
});

describe('get()', function () {
    it('can return coming parameter calculation amount', function () {
        $model = new Product;
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create();

        $fixedCommissionCalculatorContext = new FixedCommissionCalculatorContext(
            commission: $commission,
            model: $model, originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 110,
            status: CommissionCalculateHistoryStatusEnum::SUCCESS,
            reason: CommissionCalculateHistoryReasonEnum::CALCULATED,
            groupId: 'fixed_commission',
            column: 'amount',
            includedPreviousCommissionAmount: 0
        );

        $percentageCommissionCalculatorContext = new PercentageCommissionCalculatorContext(
            commission: $commission,
            model: $model, originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 110,
            rate: 10.00,
            status: CommissionCalculateHistoryStatusEnum::SUCCESS,
            reason: CommissionCalculateHistoryReasonEnum::CALCULATED,
            groupId: 'percentage_commission',
            column: 'amount', includedPreviousCommissionAmount: 0
        );

        $context = new CommissionCalculationResultContext(
            contexts: [
                $fixedCommissionCalculatorContext,
                $percentageCommissionCalculatorContext,
            ],
        );

        // Act & Assert:
        expect($context->get('amount'))
            ->toBeInstanceOf(CommissionCalculationResultContext::class)
            ->totalCommissionAmount
            ->toBe(20)
            ->totalIncludedPreviousCommissionAmount
            ->toBe(0)
            ->totalAmount
            ->toBe(120)
            ->originalAmount
            ->toBe(100)
            ->column
            ->toBe('amount')
            ->contexts
            ->toBeArray()
            ->toHaveCount(2)
            ->sequence(
                function (Expectation|FixedCommissionCalculatorContext $context) {
                    $context
                        ->toBeInstanceOf(FixedCommissionCalculatorContext::class)
                        ->commission
                        ->toBeInstanceOf(Commission::class)
                        ->model
                        ->toBeInstanceOf(Product::class)
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
                        ->groupId
                        ->toBe('fixed_commission')
                        ->column
                        ->toBe('amount')
                        ->includedPreviousCommissionAmount
                        ->toBe(0);
                },
                function (Expectation|PercentageCommissionCalculatorContext $context) {
                    $context
                        ->toBeInstanceOf(PercentageCommissionCalculatorContext::class)
                        ->commission
                        ->toBeInstanceOf(Commission::class)
                        ->model
                        ->toBeInstanceOf(Product::class)
                        ->originalAmount
                        ->toBe(100)
                        ->commissionAmount
                        ->toBe(10)
                        ->totalAmount
                        ->toBe(110)
                        ->rate
                        ->toBe(10.00)
                        ->status
                        ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS)
                        ->reason
                        ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED)
                        ->groupId
                        ->toBe('percentage_commission')
                        ->column
                        ->toBe('amount')
                        ->includedPreviousCommissionAmount
                        ->toBe(0);
                }
            );
    });
    it('cannot return calculation amount for results is empty', function (){
        $context = new CommissionCalculationResultContext(
            contexts: [],
        );

        // Act & Assert:
        expect($context->get('amount'))
            ->toBeNull();
    });
});
