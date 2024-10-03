<?php

use Illuminate\Support\Facades\Schema;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionCalculateHistory;
use Mkeremcansev\LaravelCommission\Services\CommissionCalculatorService;
use Mkeremcansev\LaravelCommission\Services\Contexts\PercentageCommissionCalculatorContext;
use Mkeremcansev\LaravelCommission\Services\Pipes\CreateHistoryPipe;
use Mkeremcansev\LaravelCommission\Tests\Fixtures\Models\Product;

describe('handle()', function () {
    beforeEach(function () {
        Schema::shouldReceive('hasColumn')
            ->andReturn(true);
    });
    it('can create commission calculate history after calculate with pipe', function () {
        // Arrange:
        $commission = Commission::factory()
            ->withPercentageCommission()
            ->create([
                'amount' => 100,
                'status' => true,
                'is_total' => false,
            ]);

        $model = new Product;

        $context = new PercentageCommissionCalculatorContext(
            commission: $commission,
            model: $model,
            originalAmount: 100,
            commissionAmount: 10,
            totalAmount: 90,
            rate: 10,
            status: fake()->randomElement(CommissionCalculateHistoryStatusEnum::cases()),
            reason: fake()->randomElement(CommissionCalculateHistoryReasonEnum::cases()),
            groupId: 'group-id',
            column: 'amount',
            includedPreviousCommissionAmount: 0,
        );

        // Act & Assert:
        (new CreateHistoryPipe())->handle($context, function ($context) {
            return $context;
        });

        $this->assertDatabaseHas(CommissionCalculateHistory::class, [
            'commission_id' => $commission->id,
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'original_amount' => 100,
            'commission_amount' => 10,
            'status' => $context->status,
            'reason' => $context->reason,
            'group_id' => $context->groupId,
            'column' => $context->column,
        ]);
    });
});
