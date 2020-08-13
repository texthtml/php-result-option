<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use th\Option;
use th\Result;

/**
 * @covers \th\Result
 * @covers \th\Option
 */
final class ResultToOption extends TestCase
{
    /**
     * @covers \th\Result::okValue
     */
    public function testOkValue()
    {
        $this->assertEquals(Option::some(1), Result::ok(1)->okValue());
        $this->assertEquals(Option::none(), Result::error(1)->okValue());
    }

    /**
     * @covers \th\Result::errorValue
     */
    public function testErrorValue()
    {
        $this->assertEquals(Option::none(), Result::ok(1)->errorValue());
        $this->assertEquals(Option::some(1), Result::error(1)->errorValue());
    }
}
