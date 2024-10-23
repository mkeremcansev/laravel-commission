<?php

use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Str;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;
use Mkeremcansev\LaravelCommission\Services\Contexts\FixedCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Pipes\CreateHistoryPipe;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Calculators\TestCalculator;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Pipes\TestPipe;

describe('isStarted()', function () {
    it('can return true is started when start date is null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['start_date' => null]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isStarted()
            ->toBeTrue();
    });

    it('can return true is started when start date is less than now', function () {
        // Arrange:
        $commission = Commission::factory()->make(['start_date' => now()->subDay()]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isStarted()
            ->toBeTrue();
    });

    it('can return false is started when start date is greater than now', function () {
        // Arrange:
        $commission = Commission::factory()->make(['start_date' => now()->addDay()]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isStarted()
            ->toBeFalse();
    });
});

describe('isEnded()', function () {
    it('can return false is ended when end date is null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['end_date' => null]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isEnded()
            ->toBeFalse();
    });

    it('can return true is ended when end date is less than now', function () {
        // Arrange:
        $commission = Commission::factory()->make(['end_date' => now()->subDay()]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isEnded()
            ->toBeTrue();
    });

    it('can return false is ended when end date is greater than now', function () {
        // Arrange:
        $commission = Commission::factory()->make(['end_date' => now()->addDay()]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isEnded()
            ->toBeFalse();
    });
});

describe('isActive()', function () {
    it('can return true is active when status is true', function () {
        // Arrange:
        $commission = Commission::factory()->make(['status' => true]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isActive()
            ->toBeTrue();
    });

    it('can return false is active when status is false', function () {
        // Arrange:
        $commission = Commission::factory()->make(['status' => false]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isActive()
            ->toBeFalse();
    });
});

describe('isInRange()', function () {
    it('can return true is in range when min and max amount are null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => null, 'max_amount' => null]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isInRange(100)
            ->toBeTrue();
    });

    it('can return true is in range when min amount is null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => null, 'max_amount' => 200]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isInRange(100)
            ->toBeTrue();
    });

    it('can return true is in range when max amount is null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100, 'max_amount' => null]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isInRange(200)
            ->toBeTrue();
    });

    it('can return true is in range when amount is between min and max amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100, 'max_amount' => 200]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isInRange(150)
            ->toBeTrue();
    });

    it('can return false is in range when amount is below min amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100, 'max_amount' => 200]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isInRange(50)
            ->toBeFalse();
    });

    it('can return false is in range when amount is above max amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100, 'max_amount' => 200]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isInRange(250)
            ->toBeFalse();
    });
});

describe('isAmountAboveMin()', function () {
    it('can return true is amount above min when min amount is null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => null]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountAboveMin(100)
            ->toBeTrue();
    });

    it('can return true is amount above min when amount is equal to min amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountAboveMin(100)
            ->toBeTrue();
    });

    it('can return true is amount above min when amount is greater than min amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountAboveMin(200)
            ->toBeTrue();
    });

    it('can return false is amount above min when amount is less than min amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['min_amount' => 100]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountAboveMin(50)
            ->toBeFalse();
    });
});

describe('isAmountBelowMax()', function () {
    it('can return true is amount below max when max amount is null', function () {
        // Arrange:
        $commission = Commission::factory()->make(['max_amount' => null]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountBelowMax(100)
            ->toBeTrue();
    });

    it('can return true is amount below max when amount is equal to max amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['max_amount' => 100]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountBelowMax(100)
            ->toBeTrue();
    });

    it('can return true is amount below max when amount is less than max amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['max_amount' => 100]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountBelowMax(50)
            ->toBeTrue();
    });

    it('can return false is amount below max when amount is greater than max amount', function () {
        // Arrange:
        $commission = Commission::factory()->make(['max_amount' => 100]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->isAmountBelowMax(150)
            ->toBeFalse();
    });
});

describe('status()', function () {
    it('can return commission calculate history status success when all conditions are ok', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => true,
            'min_amount' => 100,
            'max_amount' => 200,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(150)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::SUCCESS);
    });

    it('can return commission calculate history status failed when status is false', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'status' => false,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(150)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });

    it('can return commission calculate history status failed when start date is greater than now', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'start_date' => now()->addDay(),
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(150)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });

    it('can return commission calculate history status failed when end date is less than now', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'end_date' => now()->subDay(),
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(150)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });

    it('can return commission calculate history status failed when amount is below min amount', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'min_amount' => 100,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(50)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });

    it('can return commission calculate history status failed when amount is above max amount', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'max_amount' => 100,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(150)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });

    it('can return commission calculate history status failed when amount is not in range', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'min_amount' => 100,
            'max_amount' => 200,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(250)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });

    it('can return commission calculate history status failed when commission is ended', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'end_date' => now()->subDay(),
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->status(150)
            ->toBeInstanceOf(CommissionCalculateHistoryStatusEnum::class)
            ->toBe(CommissionCalculateHistoryStatusEnum::FAILED);
    });
});

describe('reason()', function () {
    it('can return commission calculate history reason inactive when status is false', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'status' => false,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->reason(150)
            ->toBeInstanceOf(CommissionCalculateHistoryReasonEnum::class)
            ->toBe(CommissionCalculateHistoryReasonEnum::INACTIVE);
    });

    it('can return commission calculate history reason started when start date is greater than now', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'start_date' => now()->addDay(),
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->reason(150)
            ->toBeInstanceOf(CommissionCalculateHistoryReasonEnum::class)
            ->toBe(CommissionCalculateHistoryReasonEnum::NOT_STARTED);
    });

    it('can return commission calculate history reason ended when end date is less than now', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'end_date' => now()->subDay(),
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->reason(150)
            ->toBeInstanceOf(CommissionCalculateHistoryReasonEnum::class)
            ->toBe(CommissionCalculateHistoryReasonEnum::ENDED);
    });

    it('can return commission calculate history reason out of range when amount is not in range', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'min_amount' => 100,
            'max_amount' => 200,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->reason(250)
            ->toBeInstanceOf(CommissionCalculateHistoryReasonEnum::class)
            ->toBe(CommissionCalculateHistoryReasonEnum::OUT_OF_RANGE);
    });

    it('can return commission calculate history reason calculated when all conditions are ok', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => true,
            'min_amount' => 100,
            'max_amount' => 200,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->reason(150)
            ->toBeInstanceOf(CommissionCalculateHistoryReasonEnum::class)
            ->toBe(CommissionCalculateHistoryReasonEnum::CALCULATED);
    });
});

describe('round()', function () {
    it('can round up the amount', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'rounding' => CommissionRoundingEnum::UP,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->round(100.50)
            ->toEqual(101);
    });

    it('can round down the amount', function () {
        // Arrange:
        $commission = Commission::factory()->make([
            'rounding' => CommissionRoundingEnum::DOWN,
        ]);
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        // Act & Assert:
        expect(new TestCalculator($bundleContext, $model))
            ->round(100.50)
            ->toEqual(100);
    });
});

describe('executePipeline()', function () {
    it('can execute pipelines', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        $context = new FixedCommissionCalculatorContext(
            commission: $commission,
            model: $model,
            originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 90,
            status: fake()->randomElement(CommissionCalculateHistoryStatusEnum::cases()),
            reason: fake()->randomElement(CommissionCalculateHistoryReasonEnum::cases()),
            groupId: 'group-id',
            column: 'column',
            includedPreviousCommissionAmount: 0,
        );

        Pipeline::shouldReceive('send')->once()->with($context)->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn($context);

        // Act & Assert:
        (new TestCalculator($bundleContext, $model))->executePipeline($context);
    });

    it('can execute pipelines with custom pipes', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withFixedCommission()
            ->create();
        $model = new Product;
        $commissionGroupId = Str::uuid()->toString();
        $bundleContext = new CommissionBundleContext($commission, 'amount', $commissionGroupId);

        $context = new FixedCommissionCalculatorContext(
            commission: $commission,
            model: $model,
            originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 90,
            status: fake()->randomElement(CommissionCalculateHistoryStatusEnum::cases()),
            reason: fake()->randomElement(CommissionCalculateHistoryReasonEnum::cases()),
            groupId: 'group-id',
            column: 'column',
            includedPreviousCommissionAmount: 0,
        );

        config()->set('commission.pipes', [
            TestPipe::class,
        ]);

        Pipeline::shouldReceive('send')->once()->with($context)->andReturnSelf();
        Pipeline::shouldReceive('through')->once()->with([
            CreateHistoryPipe::class,
            TestPipe::class,
        ])->andReturnSelf();
        Pipeline::shouldReceive('thenReturn')->once()->andReturn($context);

        // Act & Assert:
        (new TestCalculator($bundleContext, $model))->executePipeline($context);
    });
});
