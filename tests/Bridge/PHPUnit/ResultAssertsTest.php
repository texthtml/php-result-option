<?php

declare(strict_types=1);

namespace Tests\Bridge\PHPUnit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use th\Bridge\PHPUnit\ResultAsserts;
use th\Result;

/**
 * @covers \th\Bridge\PHPUnit\ResultAsserts
 * @covers \th\Bridge\PHPUnit\Constraint\IsOk
 * @covers \th\Bridge\PHPUnit\Constraint\IsError
 * @uses \th\Result
 */
final class ResultAssertsTest extends TestCase
{
    use ResultAsserts;

    /**
     * @dataProvider resultAssertsTypeProvider
     */
    public function testAssertType(string $method, callable $subject, ?string $exceptionExceptionMessage): void
    {
        if ($exceptionExceptionMessage !== null) {
            $this->expectException(ExpectationFailedException::class);
            $this->expectExceptionMessageMatches("/^$exceptionExceptionMessage\$/s");
        }

        $this->$method($subject());
    }

    /**
     * @dataProvider resultAssertsComparaisonProvider
     */
    public function testAssertComparaison(
        string $method,
        callable $subject,
        mixed $expected,
        ?string $exceptionExceptionMessage
    ): void {
        if ($exceptionExceptionMessage !== null) {
            $this->expectException(ExpectationFailedException::class);
            $this->expectExceptionMessageMatches("/^$exceptionExceptionMessage\$/s");
        }

        $this->$method($expected, $subject());
    }

    public function resultAssertsTypeProvider()
    {
        yield ["assertOk", static fn() => Result::ok(null), null];
        yield ["assertOk", static fn() => Result::error(null), "Failed asserting that th\\\Result Object &.* is ok\."];
        yield ["assertError", static fn() => Result::ok(null), "Failed asserting that th\\\Result Object &.* is an error\."];
        yield ["assertError", static fn() => Result::error(null), null];
    }

    public function resultAssertsComparaisonProvider()
    {
        $obj = new \stdClass();

        yield ["assertEqualsOk", static fn() => Result::ok(null), null, null];
        yield ["assertEqualsOk", static fn() => Result::ok($obj), $obj, null];
        yield ["assertEqualsOk", static fn() => Result::ok($obj), new \stdClass(), null];
        yield ["assertEqualsOk", static fn() => Result::error(null), null, "Failed asserting that th\\\Result Object &.* is ok\."];
        yield ["assertEqualsOk", static fn() => Result::ok(null), false, null];
        yield ["assertEqualsOk", static fn() => Result::ok(null), true, "Failed asserting that null matches expected true\."];

        yield ["assertSameOk", static fn() => Result::ok(null), null, null];
        yield ["assertSameOk", static fn() => Result::ok($obj), $obj, null];
        yield ["assertSameOk", static fn() => Result::ok($obj), new \stdClass(), "Failed asserting that two variables reference the same object\."];
        yield ["assertSameOk", static fn() => Result::error(null), null, "Failed asserting that th\\\Result Object &.* is ok\."];
        yield ["assertSameOk", static fn() => Result::ok(null), false, "Failed asserting that null is identical to false\."];
        yield ["assertSameOk", static fn() => Result::ok(null), true, "Failed asserting that null is identical to true\."];

        yield ["assertEqualsError", static fn() => Result::error(null), null, null];
        yield ["assertEqualsError", static fn() => Result::error($obj), $obj, null];
        yield ["assertEqualsError", static fn() => Result::error($obj), new \stdClass(), null];
        yield ["assertEqualsError", static fn() => Result::ok(0), 0, "Failed asserting that th\\\Result Object &.* is an error\."];
        yield ["assertEqualsError", static fn() => Result::error(null), false, null];
        yield ["assertEqualsError", static fn() => Result::error(null), true, "Failed asserting that null matches expected true\."];

        yield ["assertSameError", static fn() => Result::error(null), null, null];
        yield ["assertSameError", static fn() => Result::error($obj), $obj, null];
        yield ["assertSameError", static fn() => Result::error($obj), new \stdClass(), "Failed asserting that two variables reference the same object\."];
        yield ["assertSameError", static fn() => Result::ok(null), null, "Failed asserting that th\\\Result Object &.* is an error\."];
        yield ["assertSameError", static fn() => Result::error(null), false, "Failed asserting that null is identical to false\."];
        yield ["assertSameError", static fn() => Result::error(null), true, "Failed asserting that null is identical to true\."];
    }
}
