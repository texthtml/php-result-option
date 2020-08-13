<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Exception;
use th\Option;
use th\Result;

/**
 * @covers \th\Option
 * @covers \th\Result
 */
final class OptionToResultTest extends TestCase
{
    use UseResultAsserts;

    /**
     * @covers \th\Option::okOr
     */
    public function testOkOr()
    {
        $this->assertEquals(Result::ok(1), Option::some(1)->okOr(2));
        $this->assertEquals(Result::error(2), Option::none()->okOr(2));
    }

    /**
     * @covers \th\Option::okOrElse
     */
    public function testOkOrElse()
    {
        $this->assertEquals(Result::ok(1), Option::some(1)->okOrElse($this->forbidden()));
        $this->assertEquals(Result::error(2), Option::none()->okOrElse(fn () => 2));
    }

    private function forbidden(): \Closure
    {
        return fn () => throw new Exception("should not be called");
    }
}
