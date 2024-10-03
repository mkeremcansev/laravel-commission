<?php

use Mkeremcansev\LaravelCommission\Tests\Fixtures\Enums\PoweredEnumTest;

describe('is()', function () {
    it('can checks if enum value equals to given value', function () {
        // Arrange:
        $enum = PoweredEnumTest::ONE;

        // Act & Assert:
        expect($enum->is(PoweredEnumTest::ONE))
            ->toBeTrue()
            ->and($enum->is(PoweredEnumTest::TWO))
            ->toBeFalse();
    });
});

describe('isNot()', function () {
    it('can checks if enum value does not equal to given value', function () {
        // Arrange:
        $enum = PoweredEnumTest::ONE;

        // Act & Assert:
        expect($enum->isNot(PoweredEnumTest::ONE))
            ->toBeFalse()
            ->and($enum->isNot(PoweredEnumTest::TWO))
            ->toBeTrue();
    });
});

describe('in()', function () {
    it('can checks if enum contains any value from iterable values', function () {
        // Arrange:
        $arrayForCases = [PoweredEnumTest::TWO, PoweredEnumTest::THREE];
        $arrayForValues = [PoweredEnumTest::TWO->value, PoweredEnumTest::THREE->value];

        // Act & Assert:
        expect(PoweredEnumTest::THREE->in($arrayForCases))
            ->toBeTrue()
            ->and(PoweredEnumTest::ONE->in($arrayForCases))
            ->toBeFalse()
            ->and(PoweredEnumTest::THREE->in($arrayForValues))
            ->toBeTrue()
            ->and(PoweredEnumTest::ONE->in($arrayForValues))
            ->toBeFalse();
    });
});
