<?php

declare(strict_types=1);

namespace th\Bridge\PHPUnit;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use th\Bridge\PHPUnit\Constraint\IsNone;
use th\Bridge\PHPUnit\Constraint\IsSome;
use th\Option;

trait OptionAsserts
{
    /**
     * Asserts that the option does not contain a value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertNone(Option $option, string $message = ''): void
    {
        static::assertThat($option, new IsNone(), $message);
    }

    /**
     * Asserts that the option contains some value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertSome(Option $option, string $message = ''): void
    {
        static::assertThat($option, new IsSome(), $message);
    }

    /**
     * Asserts that the option contains a value equals to $expected.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertEqualsSome(mixed $expected, Option $option, string $message = ''): void
    {
        static::assertThat($option, new IsSome(), $message);
        static::assertThat($option->unwrap(), new IsEqual($expected), $message);
    }

    /**
     * Asserts that the option contains the $expected value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertSameSome(mixed $expected, Option $option, string $message = ''): void
    {
        static::assertThat($option, new IsSome(), $message);
        static::assertThat($option->unwrap(), new IsIdentical($expected), $message);
    }

    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;
}
