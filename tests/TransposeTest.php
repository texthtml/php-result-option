<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use th\Option;
use th\Result;

/**
 * @uses \th\Option
 * @uses \th\Result
 */
final class TransposeTest extends TestCase
{
    use UseResultAsserts;

    /**
     * @covers \th\Option::transpose
     */
    public function testTransposeOptionOfAResult()
    {
        $this->assertEquals(Result::ok(Option::none()), Option::none()->transpose());
        $this->assertEquals(Result::ok(Option::some(1)), Option::some(Result::ok(1))->transpose());
        $this->assertEquals(Result::error(1), Option::some(Result::error(1))->transpose());
    }

    /**
     * @covers \th\Result::transpose
     */
    public function testTransposeResultOfAnOption()
    {
        $this->assertEquals(Option::none(), Result::ok(Option::none())->transpose());
        $this->assertEquals(Option::some(Result::ok(1)), Result::ok(Option::some(1))->transpose());
        $this->assertEquals(Option::some(Result::error(1)), Result::error(1)->transpose());
    }
}
