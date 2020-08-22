<?php

declare(strict_types=1);

namespace Tests\Bridge\PHPUnit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use th\Bridge\PHPUnit\OptionAsserts;
use th\Option;

/**
 * @covers \th\Bridge\PHPUnit\OptionAsserts
 * @covers \th\Bridge\PHPUnit\Constraint\IsNone
 * @covers \th\Bridge\PHPUnit\Constraint\IsSome
 * @uses \th\Option
 */
final class OptionAssertsTest extends TestCase
{
    use OptionAsserts;

    /**
     * @dataProvider optionAssertsTypeProvider
     */
    public function testAssertType(string $method, Option $subject, ?string $exceptionExceptionMessage): void
    {
        if ($exceptionExceptionMessage !== null) {
            $this->expectException(ExpectationFailedException::class);
            $this->expectExceptionMessageMatches("/^$exceptionExceptionMessage\$/s");
        }

        $this->$method($subject);
    }

    /**
     * @dataProvider optionAssertsComparaisonProvider
     */
    public function testAssertComparaison(
        string $method,
        Option $subject,
        mixed $expected,
        ?string $exceptionExceptionMessage
    ): void {
        if ($exceptionExceptionMessage !== null) {
            $this->expectException(ExpectationFailedException::class);
            $this->expectExceptionMessageMatches("/^$exceptionExceptionMessage\$/s");
        }

        $this->$method($expected, $subject);
    }

    public function optionAssertsTypeProvider()
    {
        yield ["assertNone", Option::some(null), "Failed asserting that th\\\Option Object &.* is none\."];
        yield ["assertNone", Option::none(), null];
        yield ["assertSome", Option::some(null), null];
        yield ["assertSome", Option::none(), "Failed asserting that th\\\Option Object &.* is some\."];
    }

    public function optionAssertsComparaisonProvider()
    {
        yield ["assertEqualsSome", Option::some(null), null, null];
        yield ["assertEqualsSome", Option::none(), null, "Failed asserting that th\\\Option Object &.* is some\."];
        yield ["assertEqualsSome", Option::some(null), false, null];
        yield ["assertEqualsSome", Option::some(null), true, "Failed asserting that null matches expected true."];

        yield ["assertSameSome", Option::some(null), null, null];
        yield ["assertSameSome", Option::none(), null, "Failed asserting that th\\\Option Object &.* is some\."];
        yield ["assertSameSome", Option::some(null), false, "Failed asserting that null is identical to false."];
        yield ["assertSameSome", Option::some(null), true, "Failed asserting that null is identical to true."];
    }
}
