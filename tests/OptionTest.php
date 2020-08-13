<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Exception;
use stdClass;
use th\Option;
use th\Option\NoneError;

/**
 * @covers \th\Option
 */
final class OptionTest extends TestCase
{
    public function testANoneOptionCanBeCreated()
    {
        $none = Option::none();

        $this->assertTrue($none->isNone());
        $this->assertFalse($none->isSome());
    }

    public function testASomeOptionCanBeCreated()
    {
        $some = Option::some(null);

        $this->assertTrue($some->isSome());
        $this->assertFalse($some->isNone());
    }

    public function testComparaison()
    {
        $this->assertEquals(Option::some(null), Option::some(null));
        $this->assertEquals(Option::some(1), Option::some(1));

        $this->assertNotEquals(Option::some(1), Option::some(2));
        $this->assertNotEquals(Option::some(null), Option::none());
    }

    public function testNestedComparaison()
    {
        $this->assertEquals(Option::some(Option::some(1)), Option::some(Option::some(1)));
        $this->assertEquals(Option::some(Option::none()), Option::some(Option::none()));
        $this->assertNotEquals(Option::some(Option::some(null)), Option::some(Option::none()));
        $this->assertNotEquals(Option::some(1), Option::some(Option::some(1)));
    }

    public function testOptionWithTheSameValueAreNotTheSameOne()
    {
        $this->assertNotSame(Option::some(null), Option::some(null));
    }

    public function testAllNoneValueAreTheSameOne()
    {
        $this->assertSame(Option::none(), Option::none());
    }

    public function testSomeDifferentThanNone()
    {
        $this->assertNotSame(Option::none(), Option::some(null));
        $this->assertNotEquals(Option::none(), Option::some(null));
    }

    public function testSomeOptionWithSameValuesArNotTheSameOne()
    {
        $this->assertNotSame(Option::some(null), Option::some(null));
    }

    /**
     * @covers \th\Option::contains
     */
    public function testContains()
    {
        $this->assertTrue(Option::some(2)->contains(2));
        $this->assertTrue(Option::some(new stdClass())->contains(new stdClass()));
        $this->assertFalse(Option::some(2)->contains(3));
        $this->assertFalse(Option::none()->contains(3));
    }

    /**
     * @covers \th\Option::contains
     */
    public function testContainsSame()
    {
        $value = new stdClass();

        $this->assertTrue(Option::some($value)->containsSame($value));
        $this->assertFalse(Option::some(new stdClass())->containsSame($value));
        $this->assertFalse(Option::none()->containsSame($value));
    }

    /**
     * @covers \th\Option::expect
     */
    public function testExpect()
    {
        $value = new stdClass();

        $this->assertSame($value, Option::some($value)->expect("unexpected error message"));

        $this->expectExceptionObject(new NoneError("expected error message"));

        Option::none()->expect("expected error message");
    }

    /**
     * @covers \th\Option::expectNone
     */
    public function testExpectNone()
    {
        $this->assertSame(null, Option::none()->expectNone("unexpected error message"));

        $this->expectExceptionObject(new NoneError("expected error message"));

        Option::some(1)->expectNone("expected error message");
    }

    /**
     * @covers \th\Option::unwrap
     */
    public function testUnwrap()
    {
        $value = new stdClass();

        $this->assertSame($value, Option::some($value)->unwrap());

        $this->expectExceptionObject(new NoneError());

        Option::none()->unwrap();
    }

    /**
     * @covers \th\Option::unwrapNone
     */
    public function testUnwrapNone()
    {
        $this->assertSame(null, Option::none()->unwrapNone());

        $this->expectExceptionObject(new NoneError());

        Option::some(1)->unwrapNone();
    }

    /**
     * @covers \th\Option::unwrapOr
     */
    public function testUnwrapOr()
    {
        $this->assertSame(1, Option::some(1)->unwrapOr(2));
        $this->assertSame(2, Option::none()->unwrapOr(2));
    }

    /**
     * @covers \th\Option::unwrapOrElse
     */
    public function testUnwrapOrElse()
    {
        $this->assertSame(1, Option::some(1)->unwrapOrElse($this->forbidden()));
        $this->assertSame(2, Option::none()->unwrapOrElse(fn () => 2));
    }

    /**
     * @covers \th\Option::map
     */
    public function testMap()
    {
        $this->assertEquals(Option::some(2), Option::some(1)->map(fn ($i) => $i * 2));
        $this->assertEquals(Option::none(), Option::none()->map(fn ($i) => $i * 2));
    }

    /**
     * @covers \th\Option::mapOr
     */
    public function testMapOr()
    {
        $this->assertSame(2, Option::some(1)->mapOr(fn ($i) => $i * 2, 3));
        $this->assertSame(3, Option::none()->mapOr(fn ($i) => $i * 2, 3));
    }

    /**
     * @covers \th\Option::mapOrElse
     */
    public function testMapOrElse()
    {
        $this->assertSame(2, Option::some(1)->mapOrElse(fn ($i) => $i * 2, $this->forbidden()));
        $this->assertSame(3, Option::none()->mapOrElse(fn ($i) => $i * 2, fn () => 3));
    }

    /**
     * @covers \th\Option::getIterator
     */
    public function testIterable()
    {
        $value = new stdClass();

        $this->assertSame([$value], iterator_to_array(Option::some($value)));
        $this->assertSame([], iterator_to_array(Option::none()));
    }

    /**
     * @covers \th\Option::and
     */
    public function testAnd()
    {
        $b = Option::some(2);

        $this->assertSame($b, Option::some(1)->and($b));
        $this->assertEquals(Option::none(), Option::none()->and($b));
    }

    /**
     * @covers \th\Option::andThen
     */
    public function testAndThen()
    {
        $b = Option::some(2);

        $this->assertSame($b, Option::some(1)->andThen(fn () => $b));
        $this->assertEquals(Option::none(), Option::none()->andThen($this->forbidden()));
    }

    /**
     * @covers \th\Option::filter
     */
    public function testFilter()
    {
        $this->assertEquals(Option::none(), Option::none()->filter(fn () => true));
        $this->assertEquals(Option::none(), Option::some(1)->filter(fn ($i) => $i % 2 === 0));
        $this->assertEquals(Option::some(2), Option::some(2)->filter(fn ($i) => $i % 2 === 0));
    }

    /**
     * @covers \th\Option::or
     */
    public function testOr()
    {
        $b = Option::some(2);

        $this->assertSame($b, $b->or(Option::some(1)));
        $this->assertSame($b, Option::none()->or($b));
    }

    /**
     * @covers \th\Option::orElse
     */
    public function testOrElse()
    {
        $b = Option::some(2);

        $this->assertSame($b, $b->orElse($this->forbidden()));
        $this->assertSame($b, Option::none()->orElse(fn () => $b));
    }

    /**
     * @covers \th\Option::xor
     */
    public function testXOr()
    {
        $b = Option::some(2);

        $this->assertSame($b, $b->xor(Option::none()));
        $this->assertEquals(Option::none(), $b->xor(Option::some(1)));
        $this->assertSame($b, Option::none()->xor($b));
    }

    /**
     * @covers \th\Option::flatten
     */
    public function testFlatten()
    {
        $this->assertEquals(Option::some(1), Option::some(Option::some(1))->flatten());
        $this->assertEquals(Option::none(), Option::some(Option::none())->flatten());
        $this->assertEquals(Option::none(), Option::none()->flatten());
        $this->assertEquals(Option::some(Option::some(1)), Option::some(Option::some(Option::some(1)))->flatten());
    }

    /**
     * @covers \th\Option::zip
     */
    public function testZip()
    {
        $this->assertEquals(Option::some([1, 2]), Option::some(1)->zip(Option::some(2)));
        $this->assertEquals(Option::none(), Option::some(1)->zip(Option::none()));
        $this->assertEquals(Option::none(), Option::none()->zip(Option::some(1)));
    }

    /**
     * @covers \th\Option::zipWith
     */
    public function testZipWith()
    {
        $this->assertEquals(Option::some("1 2"), Option::some(1)->zipWith(Option::some(2), fn ($a, $b) => "$a $b"));
        $this->assertEquals(Option::none(), Option::some(1)->zipWith(Option::none(), $this->forbidden()));
        $this->assertEquals(Option::none(), Option::none()->zipWith(Option::some(1), $this->forbidden()));
    }

    /**
     * @covers \th\Option\some
     * @covers \th\Option\none
     */
    public function testFunctionHelpers()
    {
        $this->assertSame(Option::none(), \th\Option\none());
        $this->assertEquals(Option::some(1), \th\Option\some(1));
        $this->assertNotSame(Option::some(1), \th\Option\some(1));
    }

    private function forbidden(): \Closure
    {
        return fn () => throw new Exception("should not be called");
    }
}
