<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Exception;
use th\Bridge\PHPUnit\ResultAsserts;
use th\Option;
use th\Result;

/**
 * @uses \th\Option
 * @uses \th\Result
 * @uses \th\Bridge\PHPUnit\ResultAsserts
 * @uses \th\Bridge\PHPUnit\Constraint\IsError
 * @uses \th\Bridge\PHPUnit\Constraint\IsOk
 */
final class OptionToResultTest extends TestCase
{
    use ResultAsserts;

    /**
     * @covers \th\Option::okOr
     */
    public function testOkOr()
    {
        $this->assertEqualsOk(1, Option::some(1)->okOr(2));
        $this->assertEqualsError(2, Option::none()->okOr(2));
    }

    /**
     * @covers \th\Option::okOrElse
     */
    public function testOkOrElse()
    {
        $this->assertEqualsOk(1, Option::some(1)->okOrElse($this->forbidden()));
        $this->assertEqualsError(2, Option::none()->okOrElse(fn () => 2));
    }

    private function forbidden(): \Closure
    {
        return fn () => throw new Exception("should not be called");
    }
}
