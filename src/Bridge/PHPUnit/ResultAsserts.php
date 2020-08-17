<?php

declare(strict_types=1);

namespace th\Bridge\PHPUnit;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use th\Bridge\PHPUnit\Constraint\IsOk;
use th\Bridge\PHPUnit\Constraint\IsError;
use th\Result;

trait ResultAsserts
{
    /**
     * Asserts that the result is Ok
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertOk(Result $result, string $message = ''): void
    {
        static::assertThat($result, new IsInstanceOf(Result::class), $message);
        static::assertThat($result, new IsOk(), $message);
    }

    /**
     * Asserts that the result contains a value equals to $expected.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertEqualsOk(mixed $expected, Result $result, string $message = ''): void
    {
        static::assertThat($result, new IsInstanceOf(Result::class), $message);
        static::assertThat($result, new IsOk(), $message);
        static::assertThat($result->unwrap(), new IsEqual($expected), $message);
    }

    /**
     * Asserts that the result contains the $expected value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertSameOk(mixed $expected, Result $result, string $message = ''): void
    {
        static::assertThat($result, new IsInstanceOf(Result::class), $message);
        static::assertThat($result, new IsOk(), $message);
        static::assertThat($result->unwrap(), new IsIdentical($expected), $message);
    }

    /**
     * Asserts that the result is an error
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertError(Result $result, string $message = ''): void
    {
        static::assertThat($result, new IsInstanceOf(Result::class), $message);
        static::assertThat($result, new IsError(), $message);
    }

    /**
     * Asserts that the result contains an error equals to $expected.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertEqualsError(mixed $expected, Result $result, string $message = ''): void
    {
        static::assertThat($result, new IsInstanceOf(Result::class), $message);
        static::assertThat($result, new IsError(), $message);
        static::assertThat($result->unwrapError(), new IsEqual($expected), $message);
    }

    /**
     * Asserts that the result contains the $expected error.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertSameError(mixed $expected, Result $result, string $message = ''): void
    {
        static::assertThat($result, new IsInstanceOf(Result::class), $message);
        static::assertThat($result, new IsError(), $message);
        static::assertThat($result->unwrapError(), new IsIdentical($expected), $message);
    }

    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;
}
