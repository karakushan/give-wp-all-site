<?php

declare(strict_types=1);

namespace StellarWP\Validation\Tests\Unit\Rules;

use InvalidArgumentException;
use StellarWP\Validation\Exceptions\ValidationException;
use StellarWP\Validation\Rules\Min;
use StellarWP\Validation\Tests\TestCase;

class MinTest extends TestCase
{
    /**
     * @dataProvider validationsProvider
     */
    public function testRuleValidations($value, $shouldPass)
    {
        $rule = new Min(3);

        if ( $shouldPass ) {
            self::assertValidationRulePassed($rule, $value);
        } else {
            self::assertValidationRuleFailed($rule, $value);
        }
    }

    public function validationsProvider(): array
    {
        return [
            // numbers
            [5, true],
            [3, true],
            [3.3, true],
            [2, false],
            [-10, false],

            // strings
            ['bob', true],
            ['bobby', true],
            ['bo', false],
            ['', false],
        ];
    }

    public function testRuleShouldThrowValidationExceptionForInvalidValue()
    {
        $this->expectException(ValidationException::class);

        $rule = new Min(5);
        self::assertValidationRulePassed($rule, true);
    }

    public function testRuleThrowsExceptionForNonPositiveSize()
    {
        $this->expectException(InvalidArgumentException::class);
        new Min(0);
    }
}
