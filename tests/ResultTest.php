<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use th\Result;
use th\Result\ResultError;
use th\Result\UnusedResultError;

/**
 * @covers \th\Result
 */
final class ResultTest extends TestCase
{
    use UseResultAsserts;

    public function testAnOkResultCanBeCreated()
    {
        $result = Result::ok(null);

        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isError());
    }

    public function testAnErrorResultCanBeCreated()
    {
        $result = Result::error(null);

        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isError());
    }

    public function testComparaison()
    {
        $this->assertEquals(Result::ok(null), Result::ok(null));
        $this->assertEquals(Result::ok(1), Result::ok(1));

        $this->assertNotEquals(Result::ok(1), Result::ok(2));

        $this->assertEquals(Result::error(null), Result::error(null));
        $this->assertEquals(Result::error(1), Result::error(1));

        $this->assertNotEquals(Result::error(1), Result::error(2));

        $this->assertNotEquals(Result::ok(null), Result::error(null));
        $this->assertNotEquals(Result::ok(1), Result::error(1));
    }

    public function testResultWithSameValueOrErrorAreNotTheSame()
    {
        $this->assertNotSame(Result::error(1), Result::error(1));
        $this->assertNotSame(Result::ok(1), Result::ok(1));
    }

    /**
     * @covers \th\Result::contains
     */
    public function testContains()
    {
        $this->assertTrue(Result::ok(1)->contains(1));
        $this->assertTrue(Result::ok(new stdClass())->contains(new stdClass()));
        $this->assertFalse(Result::ok(1)->contains(2));
        $this->assertFalse(Result::error(1)->contains(1));
    }

    /**
     * @covers \th\Result::containsSame
     */
    public function testContainsSame()
    {
        $value = new stdClass();

        $this->assertTrue(Result::ok($value)->containsSame($value));
        $this->assertFalse(Result::ok($value)->containsSame(new stdClass()));
        $this->assertFalse(Result::error($value)->containsSame($value));
    }

    /**
     * @covers \th\Result::containsError
     */
    public function testContainsError()
    {
        $this->assertTrue(Result::error(1)->containsError(1));
        $this->assertFalse(Result::error(1)->containsError(2));
        $this->assertFalse(Result::ok(1)->containsError(1));
    }

    /**
     * @covers \th\Result::containsSameError
     */
    public function testContainsSameError()
    {
        $value = new stdClass();

        $this->assertTrue(Result::error($value)->containsSameError($value));
        $this->assertFalse(Result::error($value)->containsSameError(new stdClass()));
        $this->assertFalse(Result::ok($value)->containsSameError($value));
    }

    /**
     * @covers \th\Result::map
     */
    public function testmap()
    {
        $this->assertEquals(Result::ok(2), Result::ok(1)->map(fn ($i) => $i * 2));

        $result = Result::error(1);
        $this->assertSame($result, $result->map($this->forbidden()));
    }

    /**
     * @covers \th\Result::mapOr
     */
    public function testmapOr()
    {
        $this->assertEquals(2, Result::ok(1)->mapOr(fn ($i) => $i * 2, 3));
        $this->assertEquals(3, Result::error(1)->mapOr($this->forbidden(), 3));
    }

    /**
     * @covers \th\Result::mapOrElse
     */
    public function testmapOrElse()
    {
        $this->assertEquals(2, Result::ok(1)->mapOrElse(fn ($i) => $i * 2, $this->forbidden()));
        $this->assertEquals(2, Result::error(1)->mapOrElse($this->forbidden(), fn ($i) => $i * 2));
    }

    /**
     * @covers \th\Result::mapError
     */
    public function testmapError()
    {
        $this->assertEquals(Result::error(2), Result::error(1)->mapError(fn ($i) => $i * 2, $this->forbidden()));

        $result = Result::ok(1);
        $this->assertSame($result, $result->mapError($this->forbidden(), fn ($i) => $i * 2));
    }

    /**
     * @covers \th\Result::getIterator
     */
    public function testIterable()
    {
        $value = new stdClass();

        $this->assertSame([$value], iterator_to_array(Result::ok($value)));
        $this->assertSame([], iterator_to_array(Result::error(1)));
    }

    /**
     * @covers \th\Result::and
     */
    public function testAnd()
    {
        $error = Result::error(1);
        $ok = Result::ok(2);

        $this->assertSame($error, $error->and($ok));
        $this->assertSame($error, $ok->and($error));
    }

    /**
     * @covers \th\Result::andThen
     */
    public function testAndThen()
    {
        $error = Result::error(1);
        $ok = Result::ok(2);

        $this->assertSame($error, $error->andThen($this->forbidden()));
        $this->assertEquals(Result::ok(4), $ok->andThen(fn ($i) => Result::ok($i * 2)));
    }

    /**
     * @covers \th\Result::or
     */
    public function testOr()
    {
        $error = Result::error(1);
        $ok = Result::ok(2);

        $this->assertSame($ok, $error->or($ok));
        $this->assertSame($ok, $ok->or($error));
    }

    /**
     * @covers \th\Result::orElse
     */
    public function testOrElse()
    {
        $error = Result::error(1);
        $ok = Result::ok(2);

        $this->assertSame($ok, $ok->orElse($this->forbidden()));
        $this->assertEquals(Result::ok(2), $error->orElse(fn ($i) => Result::ok($i * 2)));
    }

    /**
     * @covers \th\Result::expect
     */
    public function testExpect()
    {
        $value = new stdClass();

        $this->assertSame($value, Result::ok($value)->expect("unexpected error message"));

        $this->expectExceptionObject(new ResultError("expected error message"));

        Result::error($value)->expect("expected error message");
    }

    /**
     * @covers \th\Result::expect
     */
    public function testExpectPreviousException()
    {
        $value = new stdClass();

        $this->assertSame($value, Result::ok($value)->expect("unexpected error message"));

        $exception = new Exception();
        $this->expectExceptionObject(new ResultError("expected error message", 0, $exception));

        try {
            Result::error($exception)->expect("expected error message");
        } catch (ResultError $error) {
            $this->assertSame($exception, $error->getPrevious());

            throw $error;
        }
    }

    /**
     * @covers \th\Result::unwrap
     */
    public function testUnwrap()
    {
        $value = new stdClass();

        $this->assertSame($value, Result::ok($value)->unwrap());

        $this->expectExceptionObject(new ResultError());

        Result::error($value)->unwrap();
    }

    /**
     * @covers \th\Result::expectError
     */
    public function testExpectError()
    {
        $value = new stdClass();

        $this->assertSame($value, Result::error($value)->expectError("unexpected error message"));

        $this->expectExceptionObject(new ResultError("expected error message"));

        Result::ok($value)->expectError("expected error message");
    }

    /**
     * @covers \th\Result::unwrapError
     */
    public function testUnwrapError()
    {
        $value = new stdClass();

        $this->assertSame($value, Result::error($value)->unwrapError());

        $this->expectExceptionObject(new ResultError());

        Result::ok($value)->unwrapError();
    }

    /**
     * @covers \th\Result::unwrapOr
     */
    public function testUnwrapOr()
    {
        $this->assertEquals(1, Result::ok(1)->unwrapOr(2));
        $this->assertEquals(2, Result::error(1)->unwrapOr(2));
    }

    /**
     * @covers \th\Result::unwrapOrElse
     */
    public function testUnwrapOrElse()
    {
        $this->assertEquals(1, Result::ok(1)->unwrapOrElse($this->forbidden()));
        $this->assertEquals(2, Result::error(1)->unwrapOrElse(fn ($i) => $i * 2));
    }

    /**
     * @covers \th\Result::flatten
     */
    public function testFlatten()
    {
        $this->assertEquals(Result::ok(1), Result::ok(Result::ok(1))->flatten());
        $this->assertEquals(Result::error(1), Result::ok(Result::error(1))->flatten());
        $this->assertEquals(Result::error(1), Result::error(1)->flatten());
    }

    /**
     * @covers \th\Result\ok
     * @covers \th\Result\error
     */
    public function testFunctionHelpers()
    {
        $this->assertEquals(Result::ok(1), \th\Result\ok(1));
        $this->assertNotSame(Result::ok(1), \th\Result\ok(1));

        $this->assertEquals(Result::error(1), \th\Result\error(1));
        $this->assertNotSame(Result::error(1), \th\Result\error(1));
    }

    private function forbidden(): \Closure
    {
        return fn () => throw new Exception("should not be called");
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testResultCanBeDroppedAfterUse()
    {
        Result::ok(1)->isOk();
    }

    /**
     * @covers \th\Result\UnusedResultError
     */
    public function testAnExceptionIsThrownIfTheResultIsNotUsed()
    {
        $this->expectExceptionObject(new UnusedResultError("Result was dropped without being used"));

        Result::ok(1);
    }

    /**
     * @covers \th\Result\UnusedResultError
     */
    public function testNotUsedResultExceptionStackTrace()
    {
        $test = fn () => Result::ok(1);

        try {
            $test();
        } catch (UnusedResultError $error) {
            $this->assertStringContainsString(__FILE__ . "(" . (__LINE__ - 2) . ")", $error->getTraceAsString());
        }
    }
}
